<?php
/**
 * Elementor editor integration.
 *
 * @package MotionUI_Addons
 * @since   1.2.0
 */

namespace Themeic\MotionUI_Addons\Theme_Builder\Integrations;

use Themeic\MotionUI_Addons\Theme_Builder\Admin\Admin;
use Themeic\MotionUI_Addons\Theme_Builder\Admin\Ajax;
use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Conditions_Manager;
use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Rules;
use Themeic\MotionUI_Addons\Theme_Builder\Models\Template;
use Themeic\MotionUI_Addons\Theme_Builder\Theme_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Adds the display conditions popup to the Elementor editor.
 *
 * The popup opens after a template is published, and from the "Display
 * Conditions" item of the editor's save menu. It only loads for Theme Builder
 * templates.
 *
 * @since 1.2.0
 */
class Editor {

	/**
	 * Register the editor hooks.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_assets' ], 100 );
		add_action( 'elementor/editor/footer', [ $this, 'render_popup' ] );
	}

	/**
	 * Load the popup script for the template open in the editor.
	 *
	 * @since 1.2.0
	 */
	public function enqueue_assets() {
		$template = $this->get_edited_template();

		if ( ! $template ) {
			return;
		}

		Admin::enqueue_style();

		wp_enqueue_script(
			'muia-theme-builder-editor',
			THEMEIC_MUIA_DIR_URL . 'assets/theme-builder/js/theme-builder-editor.js',
			[ 'jquery', 'elementor-editor' ],
			THEMEIC_MUIA_VERSION,
			true
		);

		wp_localize_script(
			'muia-theme-builder-editor',
			'muiaThemeBuilder',
			[
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( Ajax::NONCE_ACTION ),
				'templateId' => $template->get_id(),
				'status'     => $template->get_status(),
				'conditions' => Conditions_Manager::instance()->decorate_conditions( $template->get_conditions() ),
				'registry'   => Rules::get_conditions_for_ui(),
				'topLevel'   => Rules::get_top_level_for_ui(),
				'i18n'       => [
					'include'     => __( 'Include', 'motionui-addons-for-elementor' ),
					'exclude'     => __( 'Exclude', 'motionui-addons-for-elementor' ),
					'all'         => __( 'All', 'motionui-addons-for-elementor' ),
					'search'      =>__( 'Search…', 'motionui-addons-for-elementor' ),
					'noResults'   => __( 'No results found', 'motionui-addons-for-elementor' ),
					'clear'       => __( 'Clear', 'motionui-addons-for-elementor' ),
					'saving'      => __( 'Saving…', 'motionui-addons-for-elementor' ),
					'save'        => __( 'Save & Close', 'motionui-addons-for-elementor' ),
					'error'       => __( 'Something went wrong. Please try again.', 'motionui-addons-for-elementor' ),
					'menuTitle'   => __( 'Display Conditions', 'motionui-addons-for-elementor' ),
					'removeLabel' => __( 'Remove this condition', 'motionui-addons-for-elementor' ),
					/* translators: %s: comma separated template titles. */
					'conflict'    => __( 'Heads up: some of these pages are already targeted by %s. The most specific template wins, then the lowest priority number.', 'motionui-addons-for-elementor' ),
				],
			]
		);
	}

	/**
	 * Print the conditions popup.
	 *
	 * @since 1.2.0
	 */
	public function render_popup() {
		$template = $this->get_edited_template();

		if ( ! $template ) {
			return;
		}

		?>
		<div class="muia-popup-wrap muia-tb-conditions-popup" id="muia-tb-conditions-popup">
			<div class="backdrop"></div>
			<div class="muia-pro-card muia-tb-card muia-tb-card-wide" role="dialog" aria-modal="true" aria-labelledby="muia-tb-conditions-title">
				<div class="themeic-das-header container-fluid d-flex align-items-center gap-2">
					<span class="logo-wrap th-text-primary mr-auto">
					<img src="<?php echo esc_url( THEMEIC_MUIA_ASSETS . 'img/motionui-logo-black.svg' ); ?>" alt="<?php esc_attr_e( 'MotionUI Addons Logo', 'motionui-addons-for-elementor' ); ?>" class="logo-img">

					<span>
					<?php esc_html_e( 'MotionUI Addons', 'motionui-addons-for-elementor' ); ?>
					<a href="https://themeic.com/" target="_blank" rel="noopener noreferrer" class="d-block small"><?php esc_html_e( 'Themeic', 'motionui-addons-for-elementor' ); ?></a>
					</span>
					</span>
					<div class="muia-close-btn eicon-close" aria-label="<?php esc_attr_e( 'Close', 'motionui-addons-for-elementor' ); ?>"></div>
				</div>
				<h2 id="muia-tb-conditions-title"><?php esc_html_e( 'Where do you want to display your template?', 'motionui-addons-for-elementor' ); ?></h2>
				<p>
					<?php esc_html_e( 'Set the conditions that determine where your template is used throughout your site.', 'motionui-addons-for-elementor' ); ?>
					<br>
					<?php esc_html_e( 'For example, choose “Entire Site” to display the template across your site.', 'motionui-addons-for-elementor' ); ?>
				</p>
				<p class="muia-tb-notice" role="alert"></p>
				<div class="muia-tb-condition-rows"></div>
				<button type="button" class="th-das-btn btn-secondary btn-sm muia-tb-add-condition">+ <?php esc_html_e( 'Add Condition', 'motionui-addons-for-elementor' ); ?></button>
				<div class="muia-tb-actions">
					<button type="button" class="th-das-btn btn-secondary btn-sm muia-close-btn-text"><?php esc_html_e( 'Cancel', 'motionui-addons-for-elementor' ); ?></button>
					<button type="button" class="th-das-btn btn-sm muia-tb-save"><?php esc_html_e( 'Save & Close', 'motionui-addons-for-elementor' ); ?></button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * The Theme Builder template open in the editor, if the user may manage it.
	 *
	 * @since 1.2.0
	 *
	 * @return Template|null
	 */
	private function get_edited_template() {
		if ( ! class_exists( '\Elementor\Plugin' ) || ! Theme_Builder::is_enabled() ) {
			return null;
		}

		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();

		if ( ! $post_id || ! current_user_can( Theme_Builder::capability() ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return null;
		}

		return Template::get( $post_id );
	}
}
