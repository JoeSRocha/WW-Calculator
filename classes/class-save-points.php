<?php

namespace Calculator;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Save calculated points
 */
class Save_Points {

	public function update_lineup_points( $athlete_id ) {

		// Get Results
		$results = Athlete_Data::get_outcome_results( $athlete_id );

		// Check that results exist
		foreach ( $results as $key => $result ) :
			if ( empty( $result ) || 'Awaiting Results' === $result ) :
				echo '<span style="color:red;">' . get_the_title( $athlete_id ) . '  is missing ' . $key . ' data!</span><br/>';
				return;
			endif;
		endforeach;

		// Calculate Athlete Points
		$points = Calculate_Points::get_calculated_points( $results['weight'], $results['outcome'], $results['piv'], $results['round'], $results['finish'] );

		// Update Athletes points
		Athlete_Data::save_points( $athlete_id, $points );

		echo '<b>' . get_the_title( $athlete_id ) . ' points:</b><br/>';
		echo "Points in Victory (PIV): {$points['piv_points']}<br/>";
		echo "Weight Points updated to {$points['weight_points']}<br/>";
		echo "Round Points updated to {$points['round_points']}<br/>";
		echo "Finish Points updated to {$points['finish_points']}<br/>";
		echo "Total Points updated to {$points['total']}<br/><br/>";

	}

}