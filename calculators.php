<?php
/**
 * Plugin Name: Waged War Calculator
 * Description: A tool to help calculate points and payouts.
 *
 * Plugin URI: https://adroitgraphics.com
 * Version: 1.0.0
 * Author: Joe Rocha
 * Author URI: https://adroitgraphics.com
 * Text Domain: pp-calculator
 * License: GPLv2 or later
 *
 * @package wagedwar-calculator
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CALCULATOR_PATH', dirname( __FILE__ ) );
require_once 'inc/Autoloader.php';
require_once 'return-balance.php';

/**
 * Credit Results on Orders
 * Triggered on results_date in order_meta()
 *
 * @param Obj $workflow AutomateWoo\Workflow.
 */
function results_credited( $workflow ) {

	$order            = $workflow->data_layer()->get_order();
	$wager            = $order->get_total();
	$order_id         = $order->get_id();
	$order_status     = $order->get_status();
	$status_ready     = ( in_array( $order_status, [ 'awaiting-opponent', 'awaiting-results' ] ) ) ? 1 : 0;
	$status_ready     = apply_filters( 'override_status_check', $status_ready );
	$results_credited = get_field( 'results_credited', $order_id );
	$results_credited = apply_filters( 'override_results_credited', $results_credited );

	if ( ! $order or $results_credited or ! $status_ready )
		return;

	$user_id    = $order->get_user_id();
	$wager_type = get_field( 'wager_type', $order_id );
	$disable_payout = FALSE;
	$disable_payout = apply_filters( 'disable_payout', $disable_payout );

	if ( 'Head to Head' === $wager_type ) :

		$calculator = new Calculator();
		$total      = $calculator->get_points_total( $order_id );
		update_field( 'points_total', $total, $order_id );

		$opponent_oid   = get_field( 'opponent_oid', $order_id );
		$opponent_total = $calculator->get_points_total( $opponent_oid );
		update_field( 'opponents_points_total', $opponent_total, $order_id );

		$outcome = $calculator->get_outcome($total, $opponent_total);
		update_field( 'outcome', $outcome, $order_id );

		/** Payout based on wager amount */
		$payout = Calculator\Payout::get_payout( $wager );
		update_field( 'winnings', $payout, $order_id );

		$order->update_status( 'processing', 'H2H Event calculating' );

		$disable_payout = FALSE;
		$disable_payout = apply_filters( 'disable_payout', $disable_payout );
		if ( ! $disable_payout ) :
			$balance = mycred_get_users_balance( $user_id  );
			if ( 'Winner' === $outcome ) :
				mycred_add( 'H2H Winnings', $user_id, $payout, "Won $$payout", '', $order_id, 'Balance' );
				$new_balance = $balance + $payout;
				$order->add_order_note( "Player earned $$payout. Balance is now $$new_balance." );
			else :
				$order->add_order_note( "Player defeated. Wagered $$wager. Balance is $$balance." );
			endif;
		endif;

		$outcome_data = [
			'total'          => $total,
			'opponent_total' => $opponent_total,
			'outcome'        => $outcome,
			'payout'         => $payout
		 ];

		 return $outcome_data;

	elseif ( 'Pool' === $wager_type ) :

		$calculator = new Calculator();

		/* Fix for mycred firing unneeded on order status change to completed */
		remove_action( 'woocommerce_order_status_completed', 'mycred_woo_payout_rewards' );

		/* Get Product ID */
		$items = $order->get_items();
		foreach ( $items as $item ) :
			$product_id = $item->get_product_id();
		endforeach;

		$entries = get_field( 'entries', $product_id );
		if ( empty( $entries ) )
			return;

		$all_entries_data = [];
		foreach ( $entries as $i => $entry_order_id ) :
			$total = $calculator->get_points_total( $entry_order_id );
			$all_entries_data[ $i ] = array(
				'oid'    => $entry_order_id,
				'points' => $total,
			);
		endforeach;

		usort( $all_entries_data, function ( $item1, $item2 ) {
			return $item2['points'] <=> $item1['points'];
		});

		foreach ( $all_entries_data as $i => $entry ) :
			$order_id       = $entry['oid'];
			$place          = $i + 1;
			$points         = $entry['points'];
			$winnings       = get_field( $place, $product_id );
			$winnings       = ( empty( $winnings ) ? 0 : $winnings );
			$order          = wc_get_order( $order_id );
			$user_id        = $order->get_user_id();
			update_field( 'place', $place, $order_id );
			update_field( 'points_total', $points, $order_id );

			if ( $winnings >= 1 ) :
				$outcome = 'Winner';
				update_field( 'outcome', $outcome, $order_id );
			else :
				$outcome = 'Defeated';
				update_field( 'outcome', $outcome, $order_id );
			endif;

			$balance     = mycred_get_users_balance( $user_id  );
			$new_balance = $balance + (float)$winnings;

			if ( ! $disable_payout && $winnings > 0 && $outcome == 'Winner' ) :
				mycred_add( 'Pool Winnings', $user_id, $winnings, "Payment for Order: #$order_id", '', $order_id, 'Balance' );
				$note = "Player earned $$winnings. Balance is now $$new_balance.";
				$order->add_order_note( $note );
			endif;

			/* Update Winnings, if any */
			update_field( 'winnings', $winnings, $order_id );

			/* Mark Event Completed */
			$order->update_status( 'processing', 'Event Pool Results Calculating' );

			$items = [
				'oid'     => $order_id,
				'points'  => $points,
				'place'   => $place,
				'outcome' => $outcome,
				'payout'  => $winnings,
				'balance' => $new_balance
			];

			$outcome_data[] = $items;

		endforeach;

		/* Mark Event Product as Calculated */
		update_field( 'calculate_pool_results', TRUE, $product_id );

		return $outcome_data;

	elseif( 'Hole-in-one' === $wager_type ) :
		//Get athletes picks
		$chosen_athletes  = get_field( 'athletes', $order_id );
		$athletes_correct = 0;
		$winners = [];
		$defeated = [];
		foreach( $chosen_athletes as $athlete ) :
			if ( $athlete->outcome === 'Winner' ) :
				++$athletes_correct;
				array_push( $winners, $athlete->post_title );
			else :
				array_push( $defeated, $athlete->post_title );
			endif;
		endforeach;
		update_field( 'points_total', $athletes_correct, $order_id );
		if ( $athletes_correct === 6 && ! $disable_payout ) :
			$winnings    = Calculator\Payout_Hole_In_One::get_payout( $wager );
			$balance     = mycred_get_users_balance( $user_id  );
			$new_balance = $balance + (float)$winnings;
			$note        = "Player earned $$winnings. Balance is now $$new_balance.";
			update_field( 'outcome', 'Winner', $order_id );
			mycred_add( 'Pool Winnings', $user_id, $winnings, "Payment for Order: #$order_id", '', $order_id, 'Balance' );
			$order->add_order_note( $note );
		else :
			$note = "Player Defeated. $athletes_correct/6 Correct.";
			$order->add_order_note( $note );
			update_field( 'outcome', 'Defeated', $order_id );
		endif;

		/* Mark Results Credited */
		update_field( 'results_credited', TRUE, $order_id );

		/* Mark Event Completed */
		$order->update_status( 'processing', 'Hole-in-one Pool Results Calculating' );
		return [
			'correct_picks' => $athletes_correct,
			'winners'       => $winners,
			'defeated'      => $defeated
		];
	endif;

	/* Mark Results Credited */
	update_field( 'results_credited', TRUE, $order_id );

}
