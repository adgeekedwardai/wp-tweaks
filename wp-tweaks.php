<?php
/*
Plugin Name: WP Tweaks
Plugin URI: https://github.com/luizbills/wp-tweaks
Description: Several opinionated WordPress tweaks focused in security and performance.
Version: 1.4.0
Author: Luiz Bills
Author URI: https://luizpb.com/en
Text Domain: wp-tweaks
Domain Path: /languages
*/

if ( ! defined( 'WPINC' ) ) die();

if ( ! class_exists( 'WP_Tweaks' ) ) :

class WP_Tweaks {

	const VERSION = '1.3.2';
	const FILE = __FILE__;
	const DIR = __DIR__;
	const PREFIX = 'wp_tweaks_';

	protected static $_instance = null;
	protected static $_assets_dir = 'assets';

	protected function __construct () {
		$this->includes();
		$this->hooks();
	}

	protected function includes () {
		require_once self::DIR . '/vendor/better-wordpress-admin-api/framework/init.php';
		require_once self::DIR . '/inc/helpers.php';
		require_once self::DIR . '/inc/settings.php';

		// tweaks
		foreach ( WP_Tweaks_Settings::get_settings() as $id => $_ ) {
			if ( '_' === $id[0] ) continue;
			if ( apply_filters( "wp_tweaks_skip_{$id}", false ) ) continue;
			$file = self::DIR . "/inc/tweaks/{$id}.php";
			if ( file_exists( $file ) && ! empty( self::get_option( $id ) ) ) {
				include_once $file;
			}
		}
	}

	public function hooks () {
		add_action( 'init', [ $this, 'load_plugin_textdomain' ], 0 );
	}

	public function load_plugin_textdomain () {
		load_plugin_textdomain( 'wp-tweaks', false, dirname( plugin_basename( self::FILE ) ) . '/languages/' );
	}

	public static function get_asset_url ( $file_path ) {
		return plugins_url( self::$_assets_dir . '/' . $file_path, self::FILE );
	}

	public static function get_option ( $key ) {
		return WP_Tweaks_Settings::get_option( $key );
	}

	public static function get_instance () {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

function wp_tweaks () {
	return WP_Tweaks::get_instance();
}

wp_tweaks();

endif;
