<?php

namespace Calculator;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fix for Scraped Athlete Data, missing weight class
 * Converts weight (e.g. 205 lbs) to weight division (e.g. light heavyweight)
 *
 * @param Int $athlete_id
 * @return string @weight_class
 */

class Missing_Division {

    static function fix( $athlete_id ) {
        $weight = (int)get_field( 'weight', $athlete_id );
        $is_female = get_field( 'female_athlete', $athlete_id );

        switch($weight):
            case ( $weight <= 115 ):
                $weight_class = "Women's Strawweight";
            break;

            case ( $weight > 115 && $weight <= 125 ):
                $weight_class = $is_female ? "Women's Flyweight" : "Flyweight";
            break;

            case ( $weight > 125 && $weight <= 135 ):
                $weight_class = $is_female ? "Women's Bantamweight" : "Bantamweight";
            break;

            case ( $weight > 135 && $weight <= 145 ):
                $weight_class = $is_female ? "Women's Featherweight" : 'Featherweight';
            break;

            case ( $weight > 145 && $weight <= 155 ):
                $weight_class = 'Lightweight';
            break;

            case ( $weight > 155 && $weight <= 170 ):
                $weight_class = 'Welterweight';
            break;

            case ( $weight > 170 && $weight <= 185 ):
                $weight_class = 'Middleweight';
            break;

            case ( $weight > 185 && $weight <= 205 ):
                $weight_class = 'Light Heavyweight';
            break;

            case ( $weight > 205 && $weight <= 265 ):
                $weight_class = 'Heavyweight';
            break;

            default:
                $weight_class = NULL;

        endswitch;

        return $weight_class;
    }

}