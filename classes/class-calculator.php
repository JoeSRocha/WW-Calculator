<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Head to Head Calculator
 * Triggered on outcome_date in order_meta()
 *
 * @param Obj $workflow AutomateWoo\Workflow.
 */


class Calculator {

	public function get_points_total( $order_id ) {

		$event    = get_field( 'event', $order_id );
		$athletes = get_field( 'athletes', $order_id );
		$total    = 0;

		foreach ( $athletes as $athlete ) :

			$athlete_id   = $athlete->ID;
			$weight_class = $athlete->weight_class;

			/* In case weight class is missing */
			if ( ! isset( $weight_class ) || 'Awaiting Entry' == $weight_class ):
				$weight_class = Calculator\Missing_Division::fix( $athlete_id );
				update_field( 'weight_class', $weight_class, $athlete_id );
			endif;

			$points = Calculator\Calculate_Points::get_calculated_points( $weight_class, $athlete->outcome, $athlete->piv, $athlete->round_finish, $athlete->end_by );

			$total += $points['total'];
		endforeach;

		return $total;
	}

	public function get_outcome( $total, $opponent_total ) {
		if ( $total > $opponent_total ) :
			$outcome = 'Winner';
		elseif ( $opponent_total > $total ) :
			$outcome = 'Defeated';
		else :
			$outcome = 'Draw';
		endif;
		return $outcome;
	}

}
