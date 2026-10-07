<?php
/**
 * Global Effects Manager Class
 *
 * Manages the registration, activation state, and front-end loading of the
 * site-wide effects (smooth scroll, preloader, page transition).
 *
 * @package MotionUI_Addons_For_Elementor
 * @since   1.0.0
 */

namespace Themeic\MotionUI_Addons\Inc\Classes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Global_Effects_Manager
 *
 * @since 1.0.0
 */
class Global_Effects_Manager {

	/**
	 * Database option key for storing the active global effects.
	 *
	 * Stores the active list, not the inactive one: these effects ship off.
	 *
	 * @var string
	 */
	const DB_KEY = 'muia_active_global_effects';

	/**
	 * Register the front-end hooks for every active effect.
	 *
	 * @return void
	 */
	public static function init() {
		if ( empty( self::get_active_global_effects() ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_body_open', array( __CLASS__, 'render_preloader' ) );
	}

	/**
	 * Enqueue the CSS/JS of each active effect, when the files exist.
	 *
	 * Assets live in assets/{css,js}/global-effects/{slug}.{css,js}.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		// Keep effects out of the Elementor editor and its preview frame.
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		foreach ( array_keys( self::get_active_global_effects() ) as $slug ) {
			$handle = 'muia-global-' . $slug;
			$css    = 'css/global-effects/' . $slug . '.css';
			$js     = 'js/global-effects/' . $slug . '.js';

			if ( is_readable( THEMEIC_MUIA_DIR_PATH . 'assets/' . $css ) ) {
				wp_enqueue_style( $handle, THEMEIC_MUIA_ASSETS . $css, array(), THEMEIC_MUIA_VERSION );
			}
			if ( is_readable( THEMEIC_MUIA_DIR_PATH . 'assets/' . $js ) ) {
				wp_enqueue_script( $handle, THEMEIC_MUIA_ASSETS . $js, array(), THEMEIC_MUIA_VERSION, true );
			}
		}
	}

	/**
	 * Print the preloader markup right after <body> opens.
	 *
	 * @return void
	 */
	public static function render_preloader() {
		if ( ! self::is_active( 'preloader' ) ) {
			return;
		}
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}
		echo '<div class="muia-preloader" aria-hidden="true"><span class="muia-preloader-spinner"></span></div>';
	}

	/**
	 * Whether a single effect is switched on.
	 *
	 * @param  string $slug Effect slug.
	 * @return bool
	 */
	public static function is_active( $slug ) {
		return isset( self::get_active_global_effects()[ $slug ] );
	}

	/**
	 * Returns the full effects map with is_active applied from the database.
	 *
	 * @return array
	 */
	public static function global_effects_map() {
		$active = get_option( self::DB_KEY, null );
		$map    = self::local_global_effects_map();

		// Never saved: fall back to the shipped defaults.
		if ( ! is_array( $active ) ) {
			return $map;
		}

		foreach ( $map as $key => $effect ) {
			$map[ $key ]['is_active'] = in_array( $key, $active, true );
		}

		return $map;
	}

	/**
	 * Returns only the active effects.
	 *
	 * @return array
	 */
	public static function get_active_global_effects() {
		return array_filter(
			self::global_effects_map(),
			function ( $effect ) {
				return ! empty( $effect['is_active'] ) && empty( $effect['is_upcoming'] );
			}
		);
	}

	/**
	 * Every global effect the dashboard knows about.
	 *
	 * The list lives in inc/global-effects-map.php and can be extended through
	 * the muia_global_effects_map filter. Built once per request.
	 *
	 * @return array
	 */
	public static function local_global_effects_map() {

		static $map = null;

		if ( null !== $map ) {
			return $map;
		}

		$file    = THEMEIC_MUIA_DIR_PATH . 'inc/global-effects-map.php';
		$bundled = is_readable( $file ) ? (array) require $file : array();

		/**
		 * Filter the full global effects catalog.
		 *
		 * @param array $bundled Effect key => entry.
		 */
		$map = apply_filters( 'muia_global_effects_map', $bundled );

		if ( ! is_array( $map ) ) {
			$map = $bundled;
		}

		return $map;
	}

	/**
	 * Saves the list of active effects to the database.
	 *
	 * @param  array $effects Array of active effect slugs.
	 * @return void
	 */
	public static function save_global_effects( $effects = array() ) {
		if ( ! is_array( $effects ) ) {
			$effects = array();
		}

		$known   = array_keys( self::local_global_effects_map() );
		$effects = array_values(
			array_intersect( array_filter( array_map( 'sanitize_key', $effects ) ), $known )
		);

		update_option( self::DB_KEY, $effects );
	}
}
