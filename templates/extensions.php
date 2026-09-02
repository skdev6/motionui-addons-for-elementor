<?php
/**
 * Dashboard Extension Template
 *
 * This file is loaded by the plugin and should not be accessed directly.
 *
 * @package MotionUI_Addons_For_Elementor
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ensure required class exists before using it.
if ( ! class_exists( 'Themeic\MotionUI_Addons\Inc\Classes\Extensions_Manager' ) ) {
	return;
}

use Themeic\MotionUI_Addons\Inc\Classes\Extensions_Manager;
use Themeic\MotionUI_Addons\Inc\Classes\Widgets_Manager;
use Themeic\MotionUI_Addons\Inc\Classes\Dashboard;

// Retrieve extensions map safely.
$muia_extensions_map = Extensions_Manager::extension_map();

// Bail early if no extensions are returned or data is invalid.
if ( empty( $muia_extensions_map ) || ! is_array( $muia_extensions_map ) ) {
	return;
}

$muia_categories = Dashboard::get_unique_categories( $muia_extensions_map );
$muia_all_active = Dashboard::is_all_active_switch( $muia_extensions_map );
?>

<form
	class="muia-dashboard-form"
	data-type="extensions"
	method="post"
	action=""
>
	<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">

		<h4 class="title-md">
			<?php esc_html_e( 'The Motion Design Toolkit', 'motionui-addons-for-elementor' ); ?>
		</h4>

		<div class="right-menu-item d-flex gap-2 align-items-center">

			<div class="th-switch-control th-text-primary d-flex align-items-center">
				<input
					type="checkbox"
					id="enable-all-extensions"
					class="muia-enable-all"
					<?php checked( $muia_all_active, true ); ?>  
					aria-label="<?php esc_attr_e( 'Enable all extensions', 'motionui-addons-for-elementor' ); ?>"
				/>
				<span class="switch-label" aria-hidden="true"></span>
				<label for="enable-all-extensions">
					<?php esc_html_e( 'Enable All', 'motionui-addons-for-elementor' ); ?>
				</label>
			</div>

			<div class="btn-wrap">
				<button class="th-das-btn btn-sm" type="submit" disabled>
					<div class="btn-text"><?php esc_html_e( 'Save Settings', 'motionui-addons-for-elementor' ); ?></div>
				</button>
			</div>

		</div>

	</div><!-- .th-das-header-sm -->

	<div class="widget-card-wrap">
		<?php Dashboard::switch_card( $muia_extensions_map, 'extensions' ); ?>
	</div><!-- .widget-card-wrap -->

</form><!-- .muia-dashboard-form -->