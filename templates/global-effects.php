<?php
/**
 * Dashboard Global Effects Template
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

if ( ! class_exists( 'Themeic\MotionUI_Addons\Inc\Classes\Global_Effects_Manager' ) ) {
	return;
}

use Themeic\MotionUI_Addons\Inc\Classes\Global_Effects_Manager;
use Themeic\MotionUI_Addons\Inc\Classes\Dashboard;

$muia_global_effects_map = Global_Effects_Manager::global_effects_map();

if ( empty( $muia_global_effects_map ) || ! is_array( $muia_global_effects_map ) ) {
	return;
}

$muia_all_active = Dashboard::is_all_active_switch( $muia_global_effects_map );
?>

<form
	class="muia-dashboard-form"
	data-type="global_effects"
	method="post"
	action=""
>
	<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">

		<h4 class="title-md">
			<?php esc_html_e( 'Global Effects', 'motionui-addons-for-elementor' ); ?>
		</h4>

		<div class="right-menu-item d-flex gap-2 align-items-center">

			<div class="th-switch-control th-text-primary d-flex align-items-center">
				<input
					type="checkbox"
					id="enable-all-global-effects"
					class="muia-enable-all"
					<?php checked( $muia_all_active, true ); ?>
					aria-label="<?php esc_attr_e( 'Enable all global effects', 'motionui-addons-for-elementor' ); ?>"
				/>
				<span class="switch-label" aria-hidden="true"></span>
				<label for="enable-all-global-effects">
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
		<?php Dashboard::switch_card( $muia_global_effects_map, 'global_effects' ); ?>
	</div><!-- .widget-card-wrap -->

</form><!-- .muia-dashboard-form -->
