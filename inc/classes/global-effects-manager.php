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
	 * Database option key for the chosen sub-effect of each effect
	 * (effect slug => sub-effect slug).
	 *
	 * @var string
	 */
	const CHOICE_DB_KEY = 'muia_global_effect_choices';

	/**
	 * Database option key for the user's settings values
	 * (effect slug => setting key => value).
	 *
	 * @var string
	 */
	const SETTINGS_DB_KEY = 'muia_global_effect_settings';

	/**
	 * Register the front-end hooks for every active effect.
	 *
	 * @return void
	 */
	public static function init() {
		if ( empty( self::get_active_global_effects() ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ), 20 );
		add_action( 'wp_body_open', array( __CLASS__, 'render_preloader' ) );
	}

	/**
	 * Whether the current request is the Elementor editor / preview frame,
	 * where front-end effects must stay off.
	 *
	 * @return bool
	 */
	private static function is_editor_request() {
		return class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode();
	}

	/**
	 * Enqueue the assets of each active effect.
	 *
	 * An entry lists registered handles under `css` and `js`, the same way
	 * widgets-map.php does; they are registered in Assets (assets.php), which
	 * runs earlier on wp_enqueue_scripts. Handles that were never registered
	 * are skipped.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		if ( self::is_editor_request() ) {
			return;
		}

		foreach ( self::get_active_global_effects() as $slug => $effect ) {
			foreach ( (array) ( $effect['css'] ?? array() ) as $handle ) {
				if ( wp_style_is( $handle, 'registered' ) ) {
					wp_enqueue_style( $handle );
				}
			}

			$last_js = '';
			foreach ( (array) ( $effect['js'] ?? array() ) as $handle ) {
				if ( wp_script_is( $handle, 'registered' ) ) {
					wp_enqueue_script( $handle );
					$last_js = $handle;
				}
			}

			// Tell the scripts which variant and settings the site picked.
			// Attached to the last handle so it prints after its dependencies.
			if ( $last_js ) {
				$config = array(
					'effect'   => $effect['selected_effect'] ?? '',
					'settings' => wp_list_pluck( (array) ( $effect['settings'] ?? array() ), 'value' ),
				);

				wp_add_inline_script(
					$last_js,
					'window.muiaGlobalEffect = window.muiaGlobalEffect || {}; window.muiaGlobalEffect[' . wp_json_encode( $slug ) . '] = ' . wp_json_encode( $config ) . ';',
					'before'
				);
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
		if ( self::is_editor_request() ) {
			return;
		}
		$effects = self::get_active_global_effects();
		$variant = ! empty( $effects['preloader']['selected_effect'] ) ? sanitize_html_class( $effects['preloader']['selected_effect'] ) : '';

		echo '<div class="muia-preloader' . ( $variant ? ' muia-preloader--' . esc_attr( $variant ) : '' ) . '" aria-hidden="true"><span class="muia-preloader-spinner"></span></div>';
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
		$active  = get_option( self::DB_KEY, null );
		$choices = get_option( self::CHOICE_DB_KEY, array() );
		$saved_settings = get_option( self::SETTINGS_DB_KEY, array() );
		$map     = self::local_global_effects_map();

		if ( ! is_array( $choices ) ) {
			$choices = array();
		}
		if ( ! is_array( $saved_settings ) ) {
			$saved_settings = array();
		}

		foreach ( $map as $key => $effect ) {
			// Never saved: keep the shipped is_active default.
			if ( is_array( $active ) ) {
				$map[ $key ]['is_active'] = in_array( $key, $active, true );
			}

			// Overlay the user's value on each setting, falling back to its default.
			foreach ( (array) ( $effect['settings'] ?? array() ) as $field_key => $field ) {
				$value = $saved_settings[ $key ][ $field_key ] ?? null;

				$map[ $key ]['settings'][ $field_key ]['value'] = null !== $value
					? self::sanitize_setting( $field, $value )
					: ( $field['default'] ?? '' );
			}

			if ( empty( $effect['effects'] ) ) {
				continue;
			}

			$saved = isset( $choices[ $key ] ) ? $choices[ $key ] : '';

			$map[ $key ]['selected_effect'] = self::is_valid_effect( $effect, $saved )
				? $saved
				: self::default_effect( $effect );
		}

		return $map;
	}

	/**
	 * Clean one settings value against its field definition.
	 *
	 * A field with `options` is a select and must hold one of them; otherwise
	 * `type` decides. Anything invalid falls back to the field's default.
	 *
	 * @param  array $field Field definition from the map.
	 * @param  mixed $value Raw value.
	 * @return string|float|int
	 */
	public static function sanitize_setting( $field, $value ) {
		$default = $field['default'] ?? '';

		if ( ! is_scalar( $value ) ) {
			return $default;
		}

		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
			$value = (string) $value;
			return array_key_exists( $value, $field['options'] ) ? $value : $default;
		}

		if ( ( $field['type'] ?? 'text' ) === 'number' ) {
			if ( ! is_numeric( $value ) ) {
				return $default;
			}

			$value = $value + 0;

			// Clamp to the declared range, so a hand-edited form can't exceed it.
			if ( isset( $field['min'] ) && is_numeric( $field['min'] ) ) {
				$value = max( $value, $field['min'] + 0 );
			}
			if ( isset( $field['max'] ) && is_numeric( $field['max'] ) ) {
				$value = min( $value, $field['max'] + 0 );
			}

			return $value;
		}

		return sanitize_text_field( (string) $value );
	}

	/**
	 * Flat list of effect slugs an entry offers, across all its groups.
	 *
	 * @param  array $effect Catalog entry.
	 * @return string[]
	 */
	public static function effect_slugs( $effect ) {
		$slugs = array();

		foreach ( (array) ( $effect['effects'] ?? array() ) as $group ) {
			foreach ( array_keys( (array) $group ) as $slug ) {
				$slugs[] = (string) $slug;
			}
		}

		return $slugs;
	}

	/**
	 * Whether a sub-effect belongs to the entry and is usable on this site
	 * (pro sub-effects need an active pro licence).
	 *
	 * @param  array  $effect Catalog entry.
	 * @param  string $slug   Sub-effect slug.
	 * @return bool
	 */
	public static function is_valid_effect( $effect, $slug ) {
		if ( ! is_string( $slug ) || ! in_array( $slug, self::effect_slugs( $effect ), true ) ) {
			return false;
		}

		return ! in_array( $slug, (array) ( $effect['pro_effects'] ?? array() ), true ) || Motionui::is_active_pro();
	}

	/**
	 * First usable sub-effect of an entry.
	 *
	 * @param  array $effect Catalog entry.
	 * @return string
	 */
	public static function default_effect( $effect ) {
		foreach ( self::effect_slugs( $effect ) as $slug ) {
			if ( self::is_valid_effect( $effect, $slug ) ) {
				return $slug;
			}
		}

		return '';
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
	public static function save_global_effects( $effects = array(), $choices = array(), $settings = array() ) {
		if ( ! is_array( $effects ) ) {
			$effects = array();
		}
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}
		if ( ! is_array( $choices ) ) {
			$choices = array();
		}

		$map     = self::local_global_effects_map();
		$effects = array_values(
			array_intersect( array_filter( array_map( 'sanitize_key', $effects ) ), array_keys( $map ) )
		);

		// Keep only choices that name a real, usable sub-effect of a real entry.
		$clean = array();
		foreach ( $choices as $key => $slug ) {
			$key  = sanitize_key( $key );
			$slug = is_string( $slug ) ? sanitize_key( $slug ) : '';

			if ( isset( $map[ $key ] ) && self::is_valid_effect( $map[ $key ], $slug ) ) {
				$clean[ $key ] = $slug;
			}
		}

		// Keep only declared settings of real entries, each cleaned by its field type.
		$clean_settings = array();
		foreach ( $map as $key => $effect ) {
			foreach ( (array) ( $effect['settings'] ?? array() ) as $field_key => $field ) {
				if ( isset( $settings[ $key ][ $field_key ] ) ) {
					$clean_settings[ $key ][ $field_key ] = self::sanitize_setting( $field, $settings[ $key ][ $field_key ] );
				}
			}
		}

		update_option( self::DB_KEY, $effects );
		update_option( self::CHOICE_DB_KEY, $clean );
		update_option( self::SETTINGS_DB_KEY, $clean_settings );
	}
}
