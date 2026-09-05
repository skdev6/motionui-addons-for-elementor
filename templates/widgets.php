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

// Filtering happens in the browser, so the select always renders on "All".
$muia_current_filter = '*';
?>
<form
	class="muia-dashboard-form"
	data-type="widgets"
	method="post"
	action=""
>

	<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">

		<div class="d-flex gap-1">
			<select class="muia-form-control" data-area="#widgets-area" aria-label="<?php esc_attr_e( 'Filter widgets by category', 'motionui-addons-for-elementor' ); ?>">

				<option value="*" <?php selected( $muia_current_filter, '*' ); ?>>
					<?php esc_html_e( 'All Widgets', 'motionui-addons-for-elementor' ); ?>
				</option>

				<?php foreach ( $muia_categories as $muia_cat_key => $muia_cat_label ) : ?>
					<option value=".muia-cat-<?php echo esc_attr( $muia_cat_key ); ?>" <?php selected( $muia_current_filter, '.muia-cat-' . $muia_cat_key ); ?>>
						<?php echo esc_html( str_replace("-", " ", $muia_cat_label) ); ?>
					</option>
				<?php endforeach; ?>

			</select>
			<div class="search-wrap">
				<input type="text" class="muia-form-control" placeholder="<?php esc_attr_e( 'Search Widgets', 'motionui-addons-for-elementor' ); ?>" data-area="#widgets-area" aria-label="<?php esc_attr_e( 'Search widgets', 'motionui-addons-for-elementor' ); ?>" />
				<i class="eicon-search"></i>
			</div>
		</div>

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

	<?php
	/**
	 * Shown by dashboard.js when the filter leaves nothing on screen. It sits
	 * outside #widgets-area so the filter loop does not treat it as a card.
	 *
	 * @param string $muia_request_widget_url Where the request button points.
	 */
	$muia_request_widget_url = apply_filters( 'muia_request_widget_url', 'https://themeic.com/contact/' );
	?>
	<div class="muia-no-results text-center d-none" data-empty-for="#widgets-area">

		<div class="muia-no-results-icon" aria-hidden="true">
			<img src="<?php echo esc_url( THEMEIC_MUIA_ASSETS . 'img/empty-search.svg' ); ?>" alt="" />
		</div>

		<h4><?php esc_html_e( 'No widget found', 'motionui-addons-for-elementor' ); ?></h4>

		<p>
			<?php
			printf(
				/* translators: %s: the text typed in the search box, filled in by the script. */
				esc_html__( 'Nothing here matches %s. Tell us what you need and we can build it for you.', 'motionui-addons-for-elementor' ),
				'<strong class="muia-no-results-term"></strong>'
			);
			?>
		</p>

		<a
			href="<?php echo esc_url( $muia_request_widget_url ); ?>"
			class="th-das-btn btn-sm muia-request-widget-btn"
			data-base-url="<?php echo esc_url( $muia_request_widget_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
		>
			<?php esc_html_e( 'Request a Custom Widget', 'motionui-addons-for-elementor' ); ?>
		</a>

	</div><!-- .muia-no-results -->

</form><!-- .muia-dashboard-form -->