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
use Themeic\MotionUI_Addons\Inc\Classes\Widgets_Manager;

if ( ! defined( 'ABSPATH' ) )  exit;

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
         * Automatically generate widget name from class
         *
         * Card will be card
         * Blog_Card will be blog-card
         */
        $name = str_replace(strtolower(__NAMESPACE__), '', strtolower($this->get_class_name()));
        $name = str_replace('_', '-', $name);
        $name = ltrim($name, '\\');
        return 'muia-' . $name;
    }
    /**
     * Retrieve the widget icon.
     */
    public function get_icon() {
        $widget_slug = str_replace( 'muia-', '', $this->get_name() );
        $widgets_map = Widgets_Manager::get_widgets_map();

        if ( isset( $widgets_map[ $widget_slug ]['icon'] ) ) {
            return $widgets_map[ $widget_slug ]['icon'] . ' themeic-muia-logo';
        }  
        return 'themeic-muia-logo';
    }
    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        // Automatically generate widget name from get_widgets_map
        $widget_slug = str_replace( 'muia-', '', $this->get_name() );
        $widgets_map = Widgets_Manager::get_widgets_map();

        if ( isset( $widgets_map[ $widget_slug ]['title'] ) ) {
            return $widgets_map[ $widget_slug ]['title'];
        }
        return $this->get_muia_pro_default_title();
    }

    /**
     * Fallback title generator (if not found in map)
     */
    private function get_muia_pro_default_title() {
        $class_name = str_replace( 'muia-', '', $this->get_name() );
        $title = str_replace( ['-', '_'], ' ', $class_name );
        return ucwords( $title );
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
	/**
	 * Get all public post types
	 *
	 * @return array
	 */
    public function __get_post_types() {

        $post_types = get_post_types(
            [
                'public' => true,
            ],
            'objects'
        );

        $options = [];

        if ( ! empty( $post_types ) ) {

            foreach ( $post_types as $post_type ) {

                $options[ $post_type->name ] = $post_type->labels->singular_name;
            }
        }

        return $options;
    }
	/**
	 * Get all navigation menus
	 *
	 * @return array
	 */
	public function __get_menus() {

		$menus = wp_get_nav_menus();

		$options = [];

		if ( ! empty( $menus ) ) {

			foreach ( $menus as $menu ) {

				$options[ $menu->term_id ] = $menu->name;
			}
		}

		return $options;
	}
	/**
	 * Get all public taxonomies
	 *
	 * @return array
	 */
	public function __get_taxonomies() {

		$taxonomies = get_taxonomies(
			[
				'public' => true,
			],
			'objects'
		);

		$options = [];

		if ( ! empty( $taxonomies ) ) {

			foreach ( $taxonomies as $taxonomy ) {

				$options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
			}
		}

		return $options;
	}
}