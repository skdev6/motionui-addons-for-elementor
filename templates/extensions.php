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

// Retrieve extensions map safely.
$muia_extensions_map = Extensions_Manager::extension_map();

// Bail early if no extensions are returned or data is invalid.
if ( empty( $muia_extensions_map ) || ! is_array( $muia_extensions_map ) ) {
	return;
}

$muia_all_active = ! empty( $muia_extensions_map ) && ! array_filter(
    $muia_extensions_map,
    function( $muia_extensions ) {
        return empty( $muia_extensions['is_active'] );
    }
);
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
		<?php foreach ( $muia_extensions_map as $muia_ext_slug => $muia_ext ) :

			// Sanitize slug used in attributes and IDs.
			$muia_ext_slug = sanitize_key( $muia_ext_slug );

			// Bail if slug is empty after sanitization.
			if ( empty( $muia_ext_slug ) ) {
				continue;
			}

			// Safely extract and sanitize individual extension fields.
			$muia_title    = isset( $muia_ext['title'] )       && is_string( $muia_ext['title'] )      ? $muia_ext['title']                      : '';
			$muia_icon     = isset( $muia_ext['icon'] )        && is_string( $muia_ext['icon'] )        ? $muia_ext['icon']                       : '';
			$muia_demo_url = isset( $muia_ext['demo'] )        && is_string( $muia_ext['demo'] )        ? esc_url( $muia_ext['demo'] )            : '';
			$muia_tutorial = isset( $muia_ext['tutorial'] )    && is_string( $muia_ext['tutorial'] )    ? esc_url( $muia_ext['tutorial'] )        : '';
			$muia_is_active = isset( $muia_ext['is_active'] )  ? (bool) $muia_ext['is_active']          : false;
			$muia_is_pro    = isset( $muia_ext['is_pro'] )     ? (bool) $muia_ext['is_pro']             : false;
			$muia_upcoming  = isset( $muia_ext['is_upcoming'] ) ? (bool) $muia_ext['is_upcoming']       : false;

			// Build CSS classes for the card.
			$muia_card_classes = array( 'th-widget-card', $muia_ext_slug );
			if ( $muia_is_pro ) {
				$muia_card_classes[] = 'is-pro';
			}
			if ( $muia_upcoming ) {
				$muia_card_classes[] = 'is-upcoming';
			}
		?>

		<div class="<?php echo esc_attr( implode( ' ', $muia_card_classes ) ); ?>">

			<div class="icon-wrap" aria-hidden="true">
				<?php if ( ! empty( $muia_icon ) ) : ?>
					<i class="<?php echo esc_attr( $muia_icon ); ?>"></i>
				<?php endif; ?>
			</div>

			<div class="card-con">

				<h4 class="title">
					<?php echo esc_html( $muia_title ); ?>

					<?php if ( $muia_is_pro ) : ?>
						<span class="muia-badge muia-badge-pro">
							<?php esc_html_e( 'Pro', 'motionui-addons-for-elementor' ); ?>
						</span>
					<?php endif; ?>

					<?php if ( $muia_upcoming ) : ?>
						<span class="muia-badge muia-badge-upcoming">
							<?php esc_html_e( 'Upcoming', 'motionui-addons-for-elementor' ); ?>
						</span>
					<?php endif; ?>
				</h4>

				<div class="gap-2 d-flex align-items-center">

					<?php if ( ! empty( $muia_demo_url ) ) : ?>
						<a
							href="<?php echo esc_url( $muia_demo_url ); ?>"
							class="th-doc-link"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
								/* translators: %s: extension title */
								printf( esc_attr__( 'View demo for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
							?>"
						>
							<i class="th-icon-link" aria-hidden="true"></i>
							<?php esc_html_e( 'Demo', 'motionui-addons-for-elementor' ); ?>
						</a>
					<?php else : ?>
						<span class="th-doc-link th-doc-link--disabled" aria-hidden="true">
							<i class="th-icon-link" aria-hidden="true"></i>
							<?php esc_html_e( 'Demo', 'motionui-addons-for-elementor' ); ?>
						</span>
					<?php endif; ?>

					<?php if ( ! empty( $muia_tutorial ) ) : ?>
						<a
							href="<?php echo esc_url( $muia_tutorial ); ?>"
							class="th-doc-link"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
								/* translators: %s: extension title */
								printf( esc_attr__( 'Watch tutorial for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
							?>"
						>
							<i class="th-icon-video" aria-hidden="true"></i>
							<?php esc_html_e( 'Tutorial', 'motionui-addons-for-elementor' ); ?>
						</a>
					<?php else : ?>
						<span class="th-doc-link th-doc-link--disabled" aria-hidden="true">
							<i class="th-icon-video" aria-hidden="true"></i>
							<?php esc_html_e( 'Tutorial', 'motionui-addons-for-elementor' ); ?>
						</span>
					<?php endif; ?>

				</div><!-- .d-flex -->

			</div><!-- .card-con -->

			<div class="th-switch-control d-flex align-items-center ml-auto">
				<input
					type="checkbox"
					id="toggle-<?php echo esc_attr( $muia_ext_slug ); ?>"
					name="extensions[]"
					value="<?php echo esc_attr( $muia_ext_slug ); ?>"
					<?php checked( $muia_is_active, true ); ?>
					<?php disabled( $muia_upcoming || $muia_is_pro, true ); ?>
					aria-label="<?php
						/* translators: %s: extension title */
						printf( esc_attr__( 'Toggle %s extension', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
					?>"
				/>
				<label
					class="switch-label"
					for="toggle-<?php echo esc_attr( $muia_ext_slug ); ?>"
					aria-hidden="true"
				></label>
			</div><!-- .th-switch-control -->

		</div><!-- .th-widget-card -->

		<?php endforeach; ?>
	</div><!-- .widget-card-wrap -->

</form><!-- .muia-dashboard-form -->