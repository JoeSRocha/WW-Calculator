<?php

namespace Calculator;

class Payout_Hole_In_One {

	public static function get_payout( $wager ) {

		switch ( $wager ) :

			case 1.00:
				$payout = 40.00;
				break;

			case 5.00:
				$payout = 200.00;
				break;

			case 10.00:
				$payout = 400.00;
				break;

			default:
				$payout = 0.00;

		endswitch;

		return $payout;
	}

}
