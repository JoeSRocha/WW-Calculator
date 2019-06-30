<?php


if ( ! defined( 'ABSPATH' ) ) exit;

class Autoloader {

	/**
	 * Register autoloader
	 */
	static function init() {
		spl_autoload_register( [ __CLASS__, 'autoload' ] );
	}

	/**
	 * @param $class
	 */
	static function autoload( $class ) {
		$path = self::get_autoload_path( $class );

		if ( $path && file_exists( $path ) ) {
			include $path;
		}
	}


	/**
	 * @param string $class
	 * @return string
	 */
	static function get_autoload_path( $class ) {
		if ( substr( $class, 0, 4 ) != 'Calc' ) :
			return false;
		endif;

		$file = str_replace( 'Calculator', '/class-', $class );
		$file = $file === '/class-' ? '/class-calculator' : $file;
		$file = str_replace( '_', '-', $file );
		$file = strtolower( $file );
		$file = str_replace( '\\', '', $file );

		return CALCULATOR_PATH . '/classes' . $file . '.php';
	}

}

Autoloader::init();
