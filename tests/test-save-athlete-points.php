<?php
/**
 * Class SampleTest
 *
 * @package Calculators
 */


/**
 * Test Cases for Calculator methods
 */
class CalculatorTests extends WP_UnitTestCase {

	/**
	 * Test the Calculate_Points::get_calculated_points
	 * @param int $athlete_id Athlete UID
	 */
	public function test_calculate_points() {

		$athlete_id      = 3079; // Alistair Overeem

		$weight_division = get_field( 'weight_class', $athlete_id );
		$outcome         = get_field( 'outcome', $athlete_id );
		$piv             = get_field( 'piv', $athlete_id );
		$round           = get_field( 'round_finish', $athlete_id );
		$finish          = get_field( 'end_by', $athlete_id );

		$results  = Calculator\Calculate_Points::get_calculated_points( $weight_division, $outcome, $piv, $round, $finish );
		$expected = 307;

		return $this->assertEquals( $expected, $results[ 'total' ] );
	}

}
