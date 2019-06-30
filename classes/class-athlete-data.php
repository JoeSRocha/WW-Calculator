<?php

namespace Calculator;

defined( 'ABSPATH' ) || die( 'No script kiddies!' );

/**
 * Helper to get Athlete Results
 *
 * @param Int @athlete_id
 * @return Array:Any Weight, Outcome, PIV, Round, Finish
 */
class Athlete_Data {

	protected static $athlete_id;

	public static function get_outcome_results( $athlete_id ) {
		self::$athlete_id = $athlete_id;

		// Create Athlete Obj
		$athlete      = get_post( $athlete_id );
		$weight_class = $athlete->weight_class;

		if ( empty( $weight_class ) || 'Awaiting Entry' === $weight_class ):
			$weight_class = Missing_Division::fix( $athlete_id );
			update_field( 'weight_class', $weight_class, $athlete_id );
			self::get_outcome_results( $athlete_id );
		endif;

		return [
			'weight'  => $weight_class,
			'outcome' => $athlete->outcome,
			'piv'     => $athlete->piv,
			'round'   => $athlete->round_finish,
			'finish'  => $athlete->end_by
		];
	}

	/**
	 * Saves points to athlete
	 * @param integer $athlete_id UID for athlete
	 * @param array $points calculated from self::get_outcome_results()[weight, outcome, piv, round, finish ]
	 */
	public static function save_points( $athlete_id, $points ) {
		foreach( $points as $key => $val ) :
			update_field( $key, $val, $athlete_id );
		endforeach;
	}

	public function get_points( $athlete_id ) {
		self::$athlete_id = $athlete_id;
		return [
			'weight_points' => self::get_field( 'weight_points' ),
			'round_points'  => self::get_field( 'round_points' ),
			'finish_points' => self::get_field( 'finish_points' ),
			'total'         => self::get_field( 'total' )
		];
	}

	protected static function get_field( $key ) {
		return get_field( $key, self::$athlete_id );
	}

	protected static function update_field( $key, $value ) {
		return update_field( $key, $value, self::$athlete_id );
	}
}