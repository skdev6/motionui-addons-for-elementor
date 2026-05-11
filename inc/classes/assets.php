<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets {

    public static function register_scripts() {
        wp_register_script( 'split-type', THEMEIC_MUIA_ASSETS . 'vendor/split-type/split-type.min.js', [], '0.3.4', true );
        wp_register_script( 'isotope', THEMEIC_MUIA_ASSETS . 'vendor/isotope/isotope.pkgd.min.js', [], '3.0.6', true );
        // plugin script
        wp_register_script( 'motionui-ani', THEMEIC_MUIA_ASSETS . 'js/motionui-ani.min.js', [], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'motionui-addons', THEMEIC_MUIA_ASSETS . 'js/motionui-addons.js', [], THEMEIC_MUIA_VERSION, true );
    }

    public static function enqueue_scripts() {
        wp_enqueue_script( 'motionui-ani' );
        wp_enqueue_script( 'split-type' );
        wp_enqueue_script( 'isotope' );
        wp_enqueue_script( 'motionui-addons' );
    }

    public static function enqueue_styles() {
        wp_enqueue_style(
            'motionui-addons-widgets',
            THEMEIC_MUIA_ASSETS . 'css/widgets.css',
            [],
            THEMEIC_MUIA_VERSION
        );
        wp_enqueue_style(
            'themeic-icons',
            THEMEIC_MUIA_ASSETS . 'fonts/th-icon-basic.css',
            [],
            THEMEIC_MUIA_VERSION
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
	 * Enqueue editor-only JavaScript for the Elementor panel.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_editor_js() {
		// Bail if required constants are not defined.
		if ( ! defined( 'THEMEIC_MUIA_ASSETS' ) || ! defined( 'THEMEIC_MUIA_VERSION' ) ) {
			return;
		}

		wp_enqueue_script(
			'motionui-elementor-editor',
			THEMEIC_MUIA_ASSETS . 'js/elementor-editor.js',
			array(),
			THEMEIC_MUIA_VERSION,
			true
		);
	}
}