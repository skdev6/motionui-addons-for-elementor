<?php
/**
 * Extensions Manager Class
 *
 * Manages the registration, activation state, and initialization
 * of all MotionUI extensions for the Elementor editor.
 *
 * @package MotionUI_Addons_For_Elementor
 * @since   1.0.0
 */

namespace Themeic\MotionUI_Addons\Inc\Classes;

use Themeic\MotionUI_Addons\Inc\Extensions as Extensions;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Extensions_Manager
 *
 * @since 1.0.0
 */
class Extensions_Manager {

	/**
	 * Database option key for storing inactive extensions.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const WIDGET_DB_KEY = 'muia_inactive_extensions';

	/**
	 * Initialize active extensions and register Elementor hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		$extensions = self::get_active_extensions();

		$is_text_active     = isset( $extensions['text-animation']['is_active'] )  && $extensions['text-animation']['is_active']  === true;
		$is_scroll_active   = isset( $extensions['scroll-animation']['is_active'] ) && $extensions['scroll-animation']['is_active'] === true;
		$is_image_active    = isset( $extensions['image-animation']['is_active'] )  && $extensions['image-animation']['is_active']  === true;
		$is_position_active = isset( $extensions['advance-position']['is_active'] ) && $extensions['advance-position']['is_active'] === true;
		$is_motion_active   = isset( $extensions['motion']['is_active'] )           && $extensions['motion']['is_active']           === true;

		// Enqueue editor CSS.
		add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'enqueue_editor_css' ) );

		// Text Animation extension.
		if ( $is_text_active ) {
			foreach ( self::get_text_widgets() as $widget ) {
				if ( empty( $widget['name'] ) || empty( $widget['section'] ) ) {
					continue;
				}
				add_action(
					'elementor/element/' . sanitize_key( $widget['name'] ) . '/' . sanitize_key( $widget['section'] ) . '/after_section_end',
					array( Extensions\Text_Animation::class, 'register_controls' ),
					10,
					2
				);
			}
		}

		// Image Animation extension.
		if ( $is_image_active ) {
			foreach ( self::get_img_widgets() as $widget ) {
				if ( empty( $widget['name'] ) || empty( $widget['section'] ) ) {
					continue;
				}
				add_action(
					'elementor/element/' . sanitize_key( $widget['name'] ) . '/' . sanitize_key( $widget['section'] ) . '/after_section_end',
					array( Extensions\Image_Animation::class, 'register_controls' ),
					10,
					2
				);
			}
		}

		// Scroll Animation extension.
		if ( $is_scroll_active ) {
			add_action( 'elementor/element/common/_section_style/after_section_end',    array( Extensions\Scroll_Animation::class, 'register_controls' ), 1 );
			add_action( 'elementor/element/container/section_layout/after_section_end', array( Extensions\Scroll_Animation::class, 'register_controls' ), 1 );
		}

		// Advance Position extension.
		if ( $is_position_active ) {
			add_action( 'elementor/element/common/_section_style/after_section_end',    array( Extensions\Advance_Position::class, 'register_controls' ), 1 );
			add_action( 'elementor/element/container/section_layout/after_section_end', array( Extensions\Advance_Position::class, 'register_controls' ), 1 );
		}

		// Motion Effects extension.
		if ( $is_motion_active ) {
			add_action( 'elementor/element/common/_section_style/after_section_end',    array( Extensions\Motion::class, 'register_controls' ), 1 );
			add_action( 'elementor/element/container/section_layout/after_section_end', array( Extensions\Motion::class, 'register_controls' ), 1 );
		}
	}

	/**
	 * Returns the list of Elementor text widgets to apply Text Animation to.
	 *
	 * @since  1.0.0
	 * @return array[]
	 */
	public static function get_text_widgets() {
		return array(
			array(
				'name'    => 'heading',
				'section' => 'section_title',
			),
			array(
				'name'    => 'text-editor',
				'section' => 'section_editor',
			),
		);
	}

	/**
	 * Returns the list of Elementor image widgets to apply Image Animation to.
	 *
	 * @since  1.0.0
	 * @return array[]
	 */
	public static function get_img_widgets() {
		return array(
			array(
				'name'    => 'image',
				'section' => 'section_image',
			),
			array(
				'name'    => 'muia-animated-image',
				'section' => 'section_content',
			),
		);
	}

	/**
	 * Enqueue editor-only CSS for the Elementor panel.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_editor_css() {
		// Bail if required constants are not defined.
		if ( ! defined( 'THEMEIC_MUIA_ASSETS' ) || ! defined( 'THEMEIC_MUIA_VERSION' ) ) {
			return;
		}

		wp_enqueue_style(
			'motionui-elementor-editor',
			THEMEIC_MUIA_ASSETS . 'css/elementor-editor.css',
			array(),
			THEMEIC_MUIA_VERSION
		);
	}

	/**
	 * Returns the full extensions map with is_active state applied from the database.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function extension_map() {
		$inactive_extensions = get_option( self::WIDGET_DB_KEY, array() );
		$local_extensions    = self::local_extensions_map();

		// Ensure DB value is an array to prevent foreach errors.
		if ( ! is_array( $inactive_extensions ) ) {
			$inactive_extensions = array();
		}

		foreach ( $inactive_extensions as $key ) {
			// Only process valid, non-empty string keys.
			if ( ! is_string( $key ) || empty( $key ) ) {
				continue;
			}
			$key = sanitize_key( $key );
			if ( isset( $local_extensions[ $key ] ) ) {
				$local_extensions[ $key ]['is_active'] = false;
			}
		}

		return $local_extensions;
	}

	/**
	 * Returns only the active extensions.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function get_active_extensions() {
		return array_filter(
			self::extension_map(),
			function ( $extension ) {
				return isset( $extension['is_active'] ) && $extension['is_active'] === true;
			}
		);
	}

	/**
	 * Returns the default local extensions map.
	 * All extensions default to active (is_active: true) on first install.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function local_extensions_map() {
		return array(
			'text-animation'   => array(
				'title'       => __( 'Text Animation', 'motionui-addons-for-elementor' ),
				'description' => __( 'Add entrance animations to heading and text editor widgets.', 'motionui-addons-for-elementor' ),
				'is_active'   => true,
				'is_pro'      => false,
				'is_upcoming' => false,
				'icon'        => 'eicon-t-letter',
				'demo'        => '',
				'tutorial'    => '',
			),
			'image-animation'  => array(
				'title'       => __( 'Image Animation', 'motionui-addons-for-elementor' ),
				'description' => __( 'Add entrance animations to image widgets.', 'motionui-addons-for-elementor' ),
				'is_active'   => true,
				'is_pro'      => false,
				'is_upcoming' => false,
				'icon'        => 'eicon-image',
				'demo'        => '',
				'tutorial'    => '',
			),
			'advance-position' => array(
				'title'       => __( 'Advance Position', 'motionui-addons-for-elementor' ),
				'description' => __( 'Fine-tune widget positioning with advanced CSS controls.', 'motionui-addons-for-elementor' ),
				'is_active'   => true,
				'is_pro'      => false,
				'is_upcoming' => false,
				'icon'        => 'eicon-page-transition',
				'demo'        => '',
				'tutorial'    => '',
			)
		);
	}

	/**
	 * Saves the list of inactive extensions to the database.
	 *
	 * @since  1.0.0
	 * @param  array $extensions Array of inactive extension slugs.
	 * @return void
	 */
	public static function save_extensions( $extensions = array() ) {
		// Ensure input is always a clean array of sanitized strings.
		if ( ! is_array( $extensions ) ) {
			$extensions = array();
		}

		$extensions = array_values(
			array_filter(
				array_map( 'sanitize_key', $extensions ),
				function ( $key ) {
					return ! empty( $key );
				}
			)
		);

		update_option( self::WIDGET_DB_KEY, $extensions );
	}
}