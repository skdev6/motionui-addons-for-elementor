<?php
/**
 * Plugin Name:           MotionUI Addons for Elementor
 * Plugin URI:            https://motionuiaddons.com/
 * Description:           Bring powerful GSAP animations to Elementor with ease. Includes advanced widgets like Animated Slider, Testimonial Carousel, News Ticker, Floating Effects, and more.
 * Version:               1.1.2
 * Requires at least:     6.4
 * Tested up to:          6.9
 * Requires PHP:          7.4
 * Requires Plugins:      elementor
 * Elementor tested up to: 3.25
 * Author:                Themeic
 * Author URI:            https://themeic.com/
 * License:               GPL-2.0-or-later
 * License URI:           https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:           motionui-addons-for-elementor
 * Domain Path:           /languages/
 *
 * @package              MotionUI_Addons
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define Versioning
 */
// define( 'THEMEIC_MUIA_DEV', true );

if ( defined( 'THEMEIC_MUIA_DEV' ) && true == THEMEIC_MUIA_DEV ) {
    define( 'THEMEIC_MUIA_VERSION', '1.1.2.' . time() );
} else {
    define( 'THEMEIC_MUIA_VERSION', '1.1.2' );
}

/**
 * Plugin Constants
 */
define( 'THEMEIC_MUIA_FILE', __FILE__ );
define( 'THEMEIC_MUIA_DIR_PATH', plugin_dir_path( THEMEIC_MUIA_FILE ) );
define( 'THEMEIC_MUIA_DIR_URL', plugin_dir_url( THEMEIC_MUIA_FILE ) );
define( 'THEMEIC_MUIA_ASSETS', THEMEIC_MUIA_DIR_URL . 'assets/' );

/**
 * Minimum Requirements
 */
define( 'THEMEIC_MUIA_MIN_ELEMENTOR_VERSION', '3.15.0' );
define( 'THEMEIC_MUIA_MIN_PHP_VERSION', '7.4' );

/**
 * Entry Point Function
 */
function themeic_muia_base_begin() {

    // 1. Check if Elementor is installed and activated
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', 'themeic_muia_notice_missing_main_plugin' );
        return;
    }

    // 2. Check for required Elementor version
    if ( ! version_compare( ELEMENTOR_VERSION, THEMEIC_MUIA_MIN_ELEMENTOR_VERSION, '>=' ) ) {
        add_action( 'admin_notices', 'themeic_muia_notice_minimum_elementor_version' );
        return;
    }

    // 3. Check for required PHP version
    if ( version_compare( PHP_VERSION, THEMEIC_MUIA_MIN_PHP_VERSION, '<' ) ) {
        add_action( 'admin_notices', 'themeic_muia_notice_minimum_php_version' );
        return;
    }

    // 4. Load the Main Base Class using the new Namespace
    if ( ! class_exists( 'Themeic\MotionUI_Addons\Base' ) ) {
        require_once trailingslashit( THEMEIC_MUIA_DIR_PATH ) . 'base.php';
    }
}
add_action( 'plugins_loaded', 'themeic_muia_base_begin' );

/**
 * Admin notice when Elementor is not installed or activated.
 */
function themeic_muia_notice_missing_main_plugin() {
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }

    $message = sprintf(
        /* translators: 1: Plugin name, 2: Required plugin name */
        esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'motionui-addons-for-elementor' ),
        '<strong>' . esc_html__( 'MotionUI Addons', 'motionui-addons-for-elementor' ) . '</strong>',
        '<strong>Elementor</strong>'
    );

    printf(
        '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
        wp_kses_post( $message )
    );
}

/**
 * Admin notice for outdated Elementor version
 */
function themeic_muia_notice_minimum_elementor_version() {
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }

    $message = sprintf(
        /* translators: 1: Plugin name, 2: Required plugin name, 3: Required version number */
        esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'motionui-addons-for-elementor' ),
        '<strong>' . esc_html__( 'MotionUI Addons', 'motionui-addons-for-elementor' ) . '</strong>',
        '<strong>' . esc_html__( 'Elementor', 'motionui-addons-for-elementor' ) . '</strong>',
        THEMEIC_MUIA_MIN_ELEMENTOR_VERSION
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
}

/**
 * Admin notice for outdated PHP version
 */
function themeic_muia_notice_minimum_php_version() {
    if ( isset( $_GET['activate'] ) ) {
        unset( $_GET['activate'] );
    }

    $message = sprintf(
        /* translators: 1: Plugin name, 2: Required technology name, 3: Required version number */
        esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'motionui-addons-for-elementor' ),
        '<strong>' . esc_html__( 'MotionUI Addons', 'motionui-addons-for-elementor' ) . '</strong>',
        '<strong>' . esc_html__( 'PHP', 'motionui-addons-for-elementor' ) . '</strong>',
        THEMEIC_MUIA_MIN_PHP_VERSION
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
}