<?php

namespace Calculator;

if ( ! defined( 'ABSPATH' ) ) exit;

class Payout {

    public static function get_payout( $wager ) {

        switch ( $wager ) :

            case 10.00:
                $payout = 17.00;
                break;

            case 25.00:
                $payout = 45.00;
                break;

            case 50.00:
                $payout = 93.00;
                break;
            case 100.00:
                $payout = 189.00;
                break;
            case 250.00:
                $payout = 480.00;
                break;

            default:
                $payout = 0.00;

        endswitch;

        return $payout;
    }

}
