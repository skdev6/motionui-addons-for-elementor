<?php
/**
 * Theme Builder admin screens.
 *
 * @package MotionUI_Addons
 * @since   1.2.0
 */

namespace Themeic\MotionUI_Addons\Theme_Builder\Admin;

use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Conditions_Manager;
use Themeic\MotionUI_Addons\Theme_Builder\Core\Post_Type;
use Themeic\MotionUI_Addons\Theme_Builder\Core\Template_Types;
use Themeic\MotionUI_Addons\Theme_Builder\Models\Template;
use Themeic\MotionUI_Addons\Theme_Builder\Theme_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * The templates list under the MotionUI Addons menu.
 *
 * The list is the post type's own screen, with a type and a conditions column,
 * a type filter and an "Add New Template" popup in place of the blank post screen.
 *
 * @since 1.2.0
 */
class Admin {

	/**
	 * Parent menu slug of the MotionUI Addons dashboard.
	 */
	const PARENT_SLUG = 'motion-ui-addons';

	/**
	 * Register the admin hooks.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		// Priority 11: the MotionUI Addons top level menu is registered at 10.
		add_action( 'admin_menu', [ $this, 'register_menu' ], 11 );
		add_filter( 'parent_file', [ $this, 'highlight_parent' ] );
		add_filter( 'submenu_file', [ $this, 'highlight_submenu' ] );
		add_action( 'load-post-new.php', [ $this, 'redirect_post_new' ] );

		add_filter( 'manage_' . Post_Type::CPT . '_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_' . Post_Type::CPT . '_posts_custom_column', [ $this, 'column_content' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'type_filter' ] );
		add_action( 'pre_get_posts', [ $this, 'filter_by_type' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_footer', [ $this, 'render_popup' ] );
	}

	/**
	 * Add the Theme Builder submenu, pointing at the post type's list.
	 *
	 * @since 1.2.0
	 */
	public function register_menu() {
		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Theme Builder', 'motionui-addons-for-elementor' ),
			__( 'Theme Builder', 'motionui-addons-for-elementor' ),
			Theme_Builder::capability(),
			'edit.php?post_type=' . Post_Type::CPT
		);
	}

	/**
	 * Keep the MotionUI menu open while the list is showing.
	 *
	 * @since 1.2.0
	 *
	 * @param string $parent_file Current parent file.
	 *
	 * @return string
	 */
	public function highlight_parent( $parent_file ) {
		return $this->is_list_screen() ? self::PARENT_SLUG : $parent_file;
	}

	/**
	 * Highlight the Theme Builder submenu while the list is showing.
	 *
	 * @since 1.2.0
	 *
	 * @param string|null $submenu_file Current submenu file.
	 *
	 * @return string|null
	 */
	public function highlight_submenu( $submenu_file ) {
		return $this->is_list_screen() ? 'edit.php?post_type=' . Post_Type::CPT : $submenu_file;
	}

	/**
	 * Send the blank "Add New" post screen to the list, where the popup lives.
	 *
	 * @since 1.2.0
	 */
	public function redirect_post_new() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading the screen being loaded, nothing is changed.
		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';

		if ( Post_Type::CPT !== $post_type || ! current_user_can( Theme_Builder::capability() ) ) {
			return;
		}

		wp_safe_redirect( Theme_Builder::list_url( [ 'muia_add_new' => 1 ] ) );
		exit;
	}

	/**
	 * List columns.
	 *
	 * @since 1.2.0
	 *
	 * @param array $columns Default columns.
	 *
	 * @return array
	 */
	public function columns( $columns ) {
		return [
			'cb'                => isset( $columns['cb'] ) ? $columns['cb'] : '<input type="checkbox" />',
			'title'             => __( 'Title', 'motionui-addons-for-elementor' ),
			'muia_tb_type'      => __( 'Type', 'motionui-addons-for-elementor' ),
			'muia_tb_condition' => __( 'Display Conditions', 'motionui-addons-for-elementor' ),
			'date'              => isset( $columns['date'] ) ? $columns['date'] : __( 'Date', 'motionui-addons-for-elementor' ),
		];
	}

	/**
	 * Column content.
	 *
	 * @since 1.2.0
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Template ID.
	 */
	public function column_content( $column, $post_id ) {
		if ( 'muia_tb_type' !== $column && 'muia_tb_condition' !== $column ) {
			return;
		}

		$template = Template::get( $post_id );

		if ( ! $template ) {
			return;
		}

		if ( 'muia_tb_type' === $column ) {
			echo esc_html( $template->get_type_label() );
			return;
		}

		echo esc_html( Conditions_Manager::instance()->get_conditions_summary( $template->get_conditions() ) );
	}

	/**
	 * Template type dropdown above the list.
	 *
	 * @since 1.2.0
	 *
	 * @param string $post_type Post type being listed.
	 */
	public function type_filter( $post_type ) {
		if ( Post_Type::CPT !== $post_type ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
		$current = isset( $_GET['muia_tb_type'] ) ? sanitize_key( wp_unslash( $_GET['muia_tb_type'] ) ) : '';

		echo '<select name="muia_tb_type"><option value="">' . esc_html__( 'All types', 'motionui-addons-for-elementor' ) . '</option>';

		foreach ( Template_Types::instance()->get_types() as $slug => $type ) {
			printf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $slug ),
				selected( $current, $slug, false ),
				esc_html( $type['label'] )
			);
		}

		echo '</select>';
	}

	/**
	 * Apply the type dropdown to the list query.
	 *
	 * @since 1.2.0
	 *
	 * @param \WP_Query $query The query.
	 */
	public function filter_by_type( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() || Post_Type::CPT !== $query->get( 'post_type' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
		$type = isset( $_GET['muia_tb_type'] ) ? sanitize_key( wp_unslash( $_GET['muia_tb_type'] ) ) : '';

		if ( '' === $type || ! Template_Types::instance()->type_exists( $type ) ) {
			return;
		}

		$meta_query   = (array) $query->get( 'meta_query' );
		$meta_query[] = [
			'key'   => Post_Type::META_TYPE,
			'value' => $type,
		];

		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * Load the list screen assets.
	 *
	 * @since 1.2.0
	 *
	 * @param string $hook Admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'edit.php' !== $hook || ! $this->is_list_screen() ) {
			return;
		}

		self::enqueue_style();

		wp_enqueue_script(
			'muia-theme-builder-admin',
			THEMEIC_MUIA_DIR_URL . 'assets/theme-builder/js/theme-builder-admin.js',
			[],
			THEMEIC_MUIA_VERSION,
			true
		);

		wp_localize_script(
			'muia-theme-builder-admin',
			'muiaThemeBuilder',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( Ajax::NONCE_ACTION ),
				'i18n'    => [
					'chooseType' => __( 'Please choose a template type.', 'motionui-addons-for-elementor' ),
					'creating'   => __( 'Creating…', 'motionui-addons-for-elementor' ),
					'error'      => __( 'Something went wrong. Please try again.', 'motionui-addons-for-elementor' ),
				],
			]
		);
	}

	/**
	 * Popup styles, shared by the list screen and the Elementor editor.
	 *
	 * Buttons, fields and the popup shell come from the MotionUI design system,
	 * which is only loaded on the dashboard by default.
	 *
	 * @since 1.2.0
	 */
	public static function enqueue_style() {
		wp_enqueue_style( 'themeic-das-motionui', THEMEIC_MUIA_ASSETS . 'css/themeic-dasboard.min.css', null, THEMEIC_MUIA_VERSION );
		wp_enqueue_style( 'themeic-das-main', THEMEIC_MUIA_ASSETS . 'css/style.css', null, THEMEIC_MUIA_VERSION );
		wp_enqueue_style(
			'muia-theme-builder',
			THEMEIC_MUIA_DIR_URL . 'assets/theme-builder/css/theme-builder.css',
			[ 'themeic-das-motionui', 'themeic-das-main' ],
			THEMEIC_MUIA_VERSION
		);
	}

	/**
	 * The "Add New Template" popup.
	 *
	 * @since 1.2.0
	 */
	public function render_popup() {
		if ( ! $this->is_list_screen() ) {
			return;
		}

		?>
		<div class="muia-popup-wrap muia-tb-new-popup" id="muia-tb-new-popup">
			<div class="backdrop"></div>
			<div class="muia-pro-card muia-tb-card" role="dialog" aria-modal="true" aria-labelledby="muia-tb-new-title">
				<div class="muia-close-btn eicon-close" aria-label="<?php esc_attr_e( 'Close', 'motionui-addons-for-elementor' ); ?>"></div>
				<h2 id="muia-tb-new-title"><?php esc_html_e( 'Add New Template', 'motionui-addons-for-elementor' ); ?></h2>
				<p><?php esc_html_e( 'Create a header or footer and design it with Elementor.', 'motionui-addons-for-elementor' ); ?></p>
				<form id="muia-tb-new-form">
					<label for="muia-tb-new-type"><?php esc_html_e( 'Template type', 'motionui-addons-for-elementor' ); ?></label>
					<select id="muia-tb-new-type" class="muia-form-control" name="template_type" required>
						<option value=""><?php esc_html_e( 'Select a template type', 'motionui-addons-for-elementor' ); ?></option>
						<?php foreach ( Template_Types::instance()->get_types() as $slug => $type ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $type['label'] ); ?></option>
						<?php endforeach; ?>
					</select>

					<label for="muia-tb-new-title-input"><?php esc_html_e( 'Template name', 'motionui-addons-for-elementor' ); ?></label>
					<input type="text" id="muia-tb-new-title-input" class="muia-form-control" name="template_title" placeholder="<?php esc_attr_e( 'Enter a template name', 'motionui-addons-for-elementor' ); ?>" />

					<p class="muia-tb-notice" role="alert"></p>
					<div class="muia-tb-actions">
						<button type="button" class="th-das-btn btn-secondary btn-sm muia-close-btn-text"><?php esc_html_e( 'Cancel', 'motionui-addons-for-elementor' ); ?></button>
						<button type="submit" class="th-das-btn btn-sm"><?php esc_html_e( 'Create Template', 'motionui-addons-for-elementor' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Whether the current screen is the templates list.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	private function is_list_screen() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $screen && 'edit' === $screen->base && Post_Type::CPT === $screen->post_type;
	}
}
