<?php
/**
 * Dashboard Main Template
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
if ( ! class_exists( 'Themeic\MotionUI_Addons\Inc\Classes\Widgets_Manager' ) ) {
	return;
}

use Themeic\MotionUI_Addons\Inc\Classes\Widgets_Manager;
use Themeic\MotionUI_Addons\Inc\Classes\Dashboard;

// Retrieve widgets map safely.
$muia_widgets_map = Widgets_Manager::get_widgets_map();

// Bail early if no widgets are returned or data is invalid.
if ( empty( $muia_widgets_map ) || ! is_array( $muia_widgets_map ) ) {
	return;
}

// Retrieve unique categories from widgets for the filter nav.
$muia_categories = Dashboard::get_unique_categories( $muia_widgets_map );
$muia_all_active = Dashboard::is_all_active_switch( $muia_widgets_map );
?>
<form
	class="muia-dashboard-form"
	data-type="widgets"
	method="post"
	action=""
>

	<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">
		<ul class="th-das-navbar inline-nav filter-navbar" role="tablist" data-area="#widgets-area" aria-label="<?php esc_attr_e( 'Filter widgets by category', 'motionui-addons-for-elementor' ); ?>">

			<li class="current-menu-item" role="presentation">
				<a
					href="#"
					data-filter="*"
					role="tab"
					aria-selected="true"
				>
					<?php esc_html_e( 'All Widgets', 'motionui-addons-for-elementor' ); ?>
				</a>
			</li>

			<?php foreach ( $muia_categories as $muia_cat_key => $muia_cat_label ) : ?>
				<li role="presentation">
					<a
						href="#"
						data-filter=".muia-cat-<?php echo esc_attr( $muia_cat_key ); ?>"
						role="tab"
						aria-selected="false"
					>
						<?php echo esc_html( str_replace("-", " ", $muia_cat_label) ); ?>
					</a>
				</li>
			<?php endforeach; ?>

		</ul>

		<div class="right-menu-item d-flex gap-2 align-items-center">
			<div class="th-switch-control th-text-primary d-flex align-items-center">
				<input
					type="checkbox"
					id="enable-all-widgets"
					class="muia-enable-all"
                    <?php checked( $muia_all_active, true ); ?> 
					aria-label="<?php esc_attr_e( 'Enable all widgets', 'motionui-addons-for-elementor' ); ?>"
				/>
				<span class="switch-label" aria-hidden="true"></span>
				<label for="enable-all-widgets">
					<?php esc_html_e( 'Enable All', 'motionui-addons-for-elementor' ); ?>
				</label>
			</div>

			<div class="btn-wrap">
				<button
					class="th-das-btn btn-sm"
					type="submit"
                    disabled
				>
					<span class="btn-text"><?php esc_html_e( 'Save Settings', 'motionui-addons-for-elementor' ); ?></span>
				</button>
			</div>
		</div>

	</div><!-- .th-das-header-sm -->

	<div id="widgets-area" class="widget-card-wrap">
		<?php Dashboard::switch_card( $muia_widgets_map ); ?>
	</div><!-- .widget-card-wrap -->

</form><!-- .muia-dashboard-form -->