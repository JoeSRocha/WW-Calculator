<?php

namespace Calculator;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Calculator for events on completion
 */
class Calculate_Points {

	/**
	 * Points Sum Factors
	 *
	 * @param Int    $weight_division
	 * @param String $outcome
	 * @param Int    $piv points in victory
	 * @param Int    $round ending
	 * @param String $finish type of finish
	 * @return Array:Int $all_points Total of points
	 */
	public static function get_calculated_points( $weight_division, $outcome, $piv, $round, $finish ) {
        $points        = 0;
		$round_points  = 0;
		$weight_points = 0;
		$round_points  = 0;
		$finish_points = 0;
		$piv_points    = 0;

		if ( 'Winner' === $outcome ) :

			$piv_points = $piv;
			$points     = (int)$piv;

			/* Weight Factor */
			switch ( $weight_division ) :
				case "Women’s Strawweight":
				case "Women's Strawweight":
					$weight_points = 67;
					break;
				case "Women’s Flyweight":
				case "Women's Flyweight":
					$weight_points = 64;
					break;
				case "Women’s Bantamweight":
				case "Women's Bantamweight":
					$weight_points = 52;
					break;
				case "Women’s Featherweight":
				case "Women's Featherweight":
					$weight_points = 60;
					break;
				case 'Flyweight':
					$weight_points = 57;
					break;
				case 'Bantamweight':
					$weight_points = 49;
					break;
				case 'Featherweight':
					$weight_points = 54;
					break;
				case 'Lightweight':
					$weight_points = 49;
					break;
				case 'Welterweight':
					$weight_points = 46;
					break;
				case 'Middleweight':
					$weight_points = 38;
					break;
				case 'Light Heavyweight':
					$weight_points = 35;
					break;
				case 'Heavyweight':
					$weight_points = 23;
					break;
				default:
					$weight_points = 0;
			endswitch;
			$points += $weight_points;

			/* Round Finish Factor */
			switch ( $round ) :
				case 1:
					$round_points = 100;
					break;
				case 2:
					$round_points = 75;
					break;
				case 3:
					$round_points = 50;
					break;
				case 4:
					$round_points = 40;
					break;
				case 5:
					$round_points = 20;
					break;
				default:
					$round_points = 0;
			endswitch;
			$points += $round_points;

			/* Finish Type Factor */
			switch ( $finish ) :
				case 'KO/TKO':
					$finish_points = 100;
					break;
				case 'Doctor Stoppage':
					$finish_points = 100;
					break;
				case 'Submission':
					$finish_points = 75;
					break;
				case 'Decision':
					$finish_points = 50;
					break;
				case 'Draw':
					$finish_points = 25;
					break;
				case 'Disqualification':
					$finish_points = 20;
					break;
				default:
					$finish_points = 0;
			endswitch;
			$points += $finish_points;

		endif;

		if ( 'Defeated' === $outcome ) :

			/* Round Finish Factor */
			switch ( $round ) :
				case 1:
					$round_points += 0;
					break;
				case 2:
					$round_points += 10;
					break;
				case 3:
					$round_points += 25;
					break;
				case 4:
					$round_points += 50;
					break;
				case 5:
					$round_points += 75;
					break;
				default:
					$round_points += 0;
			endswitch;
			$points = $round_points;
		endif;

		$all_points = array(
			'piv_points'    => $piv_points,
			'weight_points' => $weight_points,
			'round_points'  => $round_points,
			'finish_points' => $finish_points,
			'total'         => $points,
		);

 		return $all_points;
	}

}
