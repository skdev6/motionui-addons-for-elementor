<?php
/**
 * Base Widget Class for MotionUI Addons
 *
 * All widgets should extend this class.
 *
 * @package MotionUI Addons for Elementor
 */

namespace Themeic\MotionUI_Addons\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

abstract class Muia_Base extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        /**
         * Automatically generate widget name from class name.
         * Example: Animated_Button → muia-animated-button
         */
        $class_name = strtolower( static::class );

        // Remove namespace
        $class_name = str_replace( strtolower( __NAMESPACE__ ) . '\\', '', $class_name );

        // Replace underscores with hyphens
        $class_name = str_replace( '_', '-', $class_name );

        // Remove any remaining backslashes
        $class_name = ltrim( $class_name, '\\' );

        return 'muia-' . $class_name;
    }

    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'motionui_addons' ];   // Consider making this consistent
    }

    /**
     * Add custom HTML wrapper classes to the widget.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string
     */
    public function get_html_wrapper_class() {
        $html_class = parent::get_html_wrapper_class();

        $html_class .= ' motionui-addons';
        $html_class .= ' ' . $this->get_name();

        return trim( $html_class );
    }

}