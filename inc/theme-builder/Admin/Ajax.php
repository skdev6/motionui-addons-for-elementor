<?php
/**
 * Theme Builder AJAX endpoints.
 *
 * @package MotionUI_Addons
 * @since   1.2.0
 */

namespace Themeic\MotionUI_Addons\Theme_Builder\Admin;

use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Conditions_Manager;
use Themeic\MotionUI_Addons\Theme_Builder\Conditions\Rules;
use Themeic\MotionUI_Addons\Theme_Builder\Core\Template_Cache;
use Themeic\MotionUI_Addons\Theme_Builder\Models\Template;
use Themeic\MotionUI_Addons\Theme_Builder\Theme_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Handles template creation and the condition popup requests.
 *
 * Every endpoint is registered for logged-in users only, verifies the shared
 * nonce and re-checks the capability — templates control what every visitor of
 * the site sees, so none of these may be reachable by an unauthenticated caller.
 *
 * @since 1.2.0
 */
class Ajax {

	/**
	 * Nonce action shared by all Theme Builder endpoints.
	 */
	const NONCE_ACTION = 'muia_theme_builder';

	/**
	 * Register the endpoints.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_muia_theme_builder_create_template', [ $this, 'create_template' ] );
		add_action( 'wp_ajax_muia_theme_builder_save_conditions', [ $this, 'save_conditions' ] );
		add_action( 'wp_ajax_muia_theme_builder_search_objects', [ $this, 'search_objects' ] );
	}

	/**
	 * Create a template and return its editor URL.
	 *
	 * @since 1.2.0
	 */
	public function create_template() {
		$this->verify_request();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified in verify_request().
		$type  = isset( $_POST['template_type'] ) ? sanitize_key( wp_unslash( $_POST['template_type'] ) ) : '';
		$title = isset( $_POST['template_title'] ) ? sanitize_text_field( wp_unslash( $_POST['template_title'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$template = Template::create( $type, $title );

		if ( is_wp_error( $template ) ) {
			wp_send_json_error( [ 'message' => $template->get_error_message() ] );
		}

		Template_Cache::flush();

		wp_send_json_success(
			[
				'id'       => $template->get_id(),
				'edit_url' => $template->get_edit_url(),
			]
		);
	}

	/**
	 * Save the display conditions of a template.
	 *
	 * @since 1.2.0
	 */
	public function save_conditions() {
		$this->verify_request();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified in verify_request().
		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
		$raw         = isset( $_POST['conditions'] ) ? wp_unslash( $_POST['conditions'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated and sanitized by Conditions_Manager::validate_conditions().
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$template = Template::get( $template_id );

		if ( ! $template ) {
			wp_send_json_error( [ 'message' => __( 'This template no longer exists.', 'motionui-addons-for-elementor' ) ] );
		}

		if ( ! current_user_can( 'edit_post', $template_id ) ) {
			wp_send_json_error( [ 'message' => __( 'You are not allowed to edit this template.', 'motionui-addons-for-elementor' ) ], 403 );
		}

		$validated = Conditions_Manager::instance()->validate_conditions( is_array( $raw ) ? $raw : [] );

		if ( is_wp_error( $validated ) ) {
			wp_send_json_error( [ 'message' => $validated->get_error_message() ] );
		}

		$saved = $template->set_conditions( $validated );

		Template_Cache::flush();

		$conflicts = Conditions_Manager::instance()->find_conflicts( $template->get_type(), $saved, $template_id );

		wp_send_json_success(
			[
				'conditions' => Conditions_Manager::instance()->decorate_conditions( $saved ),
				'conflicts'  => wp_list_pluck( $conflicts, 'title' ),
			]
		);
	}

	/**
	 * Search the objects a condition can be narrowed down to.
	 *
	 * The client names the *condition*, not the post type or taxonomy to query.
	 * What may be searched is resolved from the registry, so this cannot be
	 * steered into content the condition builder never offers.
	 *
	 * @since 1.2.0
	 */
	public function search_objects() {
		$this->verify_request();

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- verified in verify_request().
		$condition = isset( $_POST['condition'] ) ? sanitize_key( wp_unslash( $_POST['condition'] ) ) : '';
		$search    = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( ! Rules::get_source( $condition ) ) {
			wp_send_json_error( [ 'message' => __( 'Unknown object type.', 'motionui-addons-for-elementor' ) ] );
		}

		wp_send_json_success(
			[
				'results' => Conditions_Manager::instance()->search_objects( $condition, $search ),
			]
		);
	}

	/**
	 * Verify the nonce and the capability, or end the request.
	 *
	 * @since 1.2.0
	 */
	private function verify_request() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( Theme_Builder::capability() ) ) {
			wp_send_json_error(
				[ 'message' => __( 'You are not allowed to manage Theme Builder templates.', 'motionui-addons-for-elementor' ) ],
				403
			);
		}
	}
}
