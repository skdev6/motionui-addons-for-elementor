<?php
/**
 * Theme Builder module bootstrap.
 *
 * @package MotionUI_Addons
 * @since   6.7.3
 */

namespace Themeic\MotionUI_Addons\Theme_Builder;

use Themeic\MotionUI_Addons\Theme_Builder\Admin\Admin;
use Themeic\MotionUI_Addons\Theme_Builder\Admin\Ajax;
use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Conditions_Cleanup;
use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Conditions_Manager;
use Themeic\MotionUI_Addons\Theme_Builder\Core\Post_Type;
use Themeic\MotionUI_Addons\Theme_Builder\Core\Template_Types;
use Themeic\MotionUI_Addons\Theme_Builder\Frontend\Frontend;
use Themeic\MotionUI_Addons\Theme_Builder\Integrations\Compatibility;
use Themeic\MotionUI_Addons\Theme_Builder\Integrations\Editor;
use Themeic\MotionUI_Addons\Theme_Builder\Integrations\Elementor_Integration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Entry point of the Theme Builder module.
 *
 * The module ships Header and Footer template types in v1. Additional types
 * (Single, Archive, 404, Search, Popup, Mega Menu, …) can be added by third
 * parties — or by future releases — through `Template_Types::register_type()`
 * on the `muia/theme_builder/register_types` action, without touching any of
 * the components below.
 *
 * @since 6.7.3
 */
class Theme_Builder {

	/**
	 * Module version. Bumped independently of the plugin so cached artefacts
	 * (admin assets, condition caches) can be invalidated on their own.
	 */
	const VERSION = '1.0.0';

	/**
	 * Singleton instance.
	 *
	 * @var Theme_Builder|null
	 */
	private static $instance = null;

	/**
	 * Instantiated components, keyed by slug.
	 *
	 * @var array
	 */
	private $components = [];

	/**
	 * Boot the module.
	 *
	 * The plugin only loads its main class once Elementor is confirmed active, so
	 * `is_enabled()` is a second guard rather than the first.
	 *
	 * @since 6.7.3
	 *
	 * @return Theme_Builder|null The module, or null when it cannot run.
	 */
	public static function boot() {
		return self::is_enabled() ? self::instance() : null;
	}

	/**
	 * Singleton accessor.
	 *
	 * @since 6.7.3
	 *
	 * @return Theme_Builder
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the module.
	 *
	 * @since 6.7.3
	 */
	private function __construct() {
		$this->register_components();

		/**
		 * Fires once every Theme Builder component is wired up.
		 *
		 * @since 6.7.3
		 *
		 * @param Theme_Builder $module The module instance.
		 */
		do_action( 'muia/theme_builder/init', $this );
	}

	/**
	 * Instantiate the components required by the current request context.
	 *
	 * @since 6.7.3
	 */
	private function register_components() {
		// Always needed — the CPT, the type registry and the condition engine are
		// shared by both the admin screens and the front-end render engine.
		$this->components['types']      = Template_Types::instance();
		$this->components['post_type']  = new Post_Type();
		$this->components['conditions'] = Conditions_Manager::instance();
		$this->components['cleanup']    = new Conditions_Cleanup();
		$this->components['elementor']  = new Elementor_Integration();
		$this->components['compat']     = new Compatibility();

		if ( is_admin() ) {
			$this->components['admin']  = new Admin();
			$this->components['ajax']   = new Ajax();
			$this->components['editor'] = new Editor();
		} else {
			$this->components['frontend'] = new Frontend();
		}
	}

	/**
	 * Get a registered component.
	 *
	 * @since 6.7.3
	 *
	 * @param string $slug Component slug.
	 *
	 * @return object|null
	 */
	public function get_component( $slug ) {
		return isset( $this->components[ $slug ] ) ? $this->components[ $slug ] : null;
	}

	/**
	 * Absolute path of the module directory, with a trailing slash.
	 *
	 * @since 6.7.3
	 *
	 * @return string
	 */
	public static function path() {
		return THEMEIC_MUIA_DIR_PATH . 'inc/theme-builder/';
	}

	/**
	 * Whether the module can run at all.
	 *
	 * Elementor powers both the editing and the rendering side, so without it
	 * the module stays completely dormant.
	 *
	 * @since 6.7.3
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$enabled = defined( 'ELEMENTOR_VERSION' ) && class_exists( '\Elementor\Plugin' );

		/**
		 * Filters whether the Theme Builder module should load.
		 *
		 * @since 6.7.3
		 *
		 * @param bool $enabled Whether the module is enabled.
		 */
		return (bool) apply_filters( 'muia/theme_builder/enabled', $enabled );
	}

	/**
	 * Capability required to manage Theme Builder templates.
	 *
	 * @since 6.7.3
	 *
	 * @return string
	 */
	public static function capability() {
		/**
		 * Filters the capability required to manage Theme Builder templates.
		 *
		 * @since 6.7.3
		 *
		 * @param string $capability Capability name.
		 */
		return (string) apply_filters( 'muia/theme_builder/capability', 'manage_options' );
	}

	/**
	 * URL of the templates list — the post type's own screen.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Extra query args.
	 *
	 * @return string
	 */
	public static function list_url( $args = [] ) {
		$args = array_merge( [ 'post_type' => Post_Type::CPT ], $args );

		return add_query_arg( $args, admin_url( 'edit.php' ) );
	}
}
