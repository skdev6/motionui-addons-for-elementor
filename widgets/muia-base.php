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
         * Card will be muia-card
         * Blog_Card will be muia-blog-card
         *
         * Only the short class name is used. Pro widgets extend this class from
         * their own namespace, so stripping this file's __NAMESPACE__ would
         * leave the whole path in the name — and every lookup keyed on the slug
         * would miss.
         */
        $class = ltrim( $this->get_class_name(), '\\' );
        $short = substr( strrchr( '\\' . $class, '\\' ), 1 );

        return 'themeic-' . strtolower( str_replace( '_', '-', $short ) );
    }

    /**
     * The catalog key for this widget: muia-spotlight-button => spotlight-button.
     */
    protected function get_widget_slug() {
        return substr( $this->get_name(), strlen( 'themeic-' ) );
    }

    /**
     * Retrieve the widget icon.
     */
    public function get_icon() {
        $widget_slug = $this->get_widget_slug();
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
        $widget_slug = $this->get_widget_slug();
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
        $title = str_replace( ['-', '_'], ' ', $this->get_widget_slug() );

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
     * Panel search keywords, from this widget's catalog entry.
     *
     * Left untranslated on purpose, the way Elementor's own widgets do it:
     * these are search terms rather than anything on screen, and the panel is
     * searched in whatever language the author types.
     *
     * A widget with no entry in the catalog — the ones not released yet — keeps
     * its own method and is unaffected.
     *
     * @since  1.0.0
     * @return string[]
     */
    public function get_keywords() {

        $slug = $this->get_widget_slug();
        $map  = Widgets_Manager::get_widgets_map();

        if ( empty( $map[ $slug ]['keywords'] ) ) {
            return [];
        }

        return array_values( (array) $map[ $slug ]['keywords'] );
    }

    /**
     * Scripts this widget needs, from its catalog entry.
     *
     * Declared in widgets-map.php under `js`, so a widget's assets sit beside
     * the rest of what the catalog knows about it instead of being repeated in
     * the class.
     *
     * A widget that needs something the map cannot express keeps its own
     * method and merges:
     *
     *     public function get_script_depends() {
     *         return array_merge( parent::get_script_depends(), [ 'my-handle' ] );
     *     }
     *
     * @since  1.0.0
     * @return string[]
     */
    public function get_script_depends() {
        return $this->get_muia_map_handles( 'js', 'script' );
    }

    /**
     * Styles this widget needs, from its catalog entry under `css`.
     *
     * @since  1.0.0
     * @return string[]
     */
    public function get_style_depends() {
        return $this->get_muia_map_handles( 'css', 'style' );
    }

    /**
     * Resolve a catalog asset list to handles that actually exist.
     *
     * Anything unregistered is dropped rather than handed to Elementor. Most of
     * these live in Pro — `spotlight-button`, `gsap` — so on a free-only site
     * the list is simply shorter, and the widget renders with whatever it has
     * instead of asking for a handle nothing ever registered.
     *
     * @since  1.0.0
     * @param  string $key  Catalog key: js or css.
     * @param  string $type script or style.
     * @return string[]
     */
    protected function get_muia_map_handles( $key, $type ) {

        $slug = $this->get_widget_slug();
        $map  = Widgets_Manager::get_widgets_map();

        if ( empty( $map[ $slug ][ $key ] ) ) {
            return [];
        }

        $handles = [];

        foreach ( (array) $map[ $slug ][ $key ] as $name ) {

            $handle = $this->resolve_muia_handle( $name, $type );

            if ( '' !== $handle ) {
                $handles[] = $handle;
            }
        }

        return array_values( array_unique( $handles ) );
    }

    /**
     * The registered handle for a catalog asset name.
     *
     * The catalog names a module — `spotlight-button` — while Pro registers it
     * prefixed, as `muia-spotlight-button`. Both spellings are accepted so an
     * entry can name a module or a plain handle such as `swiper` or `gsap`
     * without having to know which is which.
     *
     * @since  1.0.0
     * @param  string $name Name from the catalog.
     * @param  string $type script or style.
     * @return string Registered handle, or '' when there is none.
     */
    protected function resolve_muia_handle( $name, $type ) {

        $is_registered = 'style' === $type ? 'wp_style_is' : 'wp_script_is';

        foreach ( [ $name, 'muia-' . $name ] as $handle ) {
            if ( $is_registered( $handle, 'registered' ) ) {
                return $handle;
            }
        }

        return '';
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