<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return Wager when Order does not receive opponent(s) by event start
 * @param $workflow AutomateWoo\Workflow
 * @return void
 */
function jsr_return_wager( $workflow ) {
	$order    = $workflow->data_layer()->get_order();
	$order_id = $order->get_id();
	$wager    = get_post_meta( $order_id, '_order_total', true );
	if ( empty( $order ) || empty( $wager ) ) :
		return;
	endif;

	$user_id = $order->get_user_id();
	$results_credited = get_post_meta( $order_id, 'results_credited', true );
	if ( ! $results_credited ) :
		/* Return Wager to Balance */
		mycred_add( 'Balance Returned', $user_id, $wager, 'No Opponent(s)', '', '', 'Balance' );

		/* Add Order Notes */
		$note = __( "Returned $wager to balance." );
		$order->add_order_note( $note );
		/* Mark order as credited */
		update_post_meta( $order_id, 'results_credited', 1 );
	endif;
}
