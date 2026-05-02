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

// Retrieve widgets map safely.
$muia_widgets_map = Widgets_Manager::get_widgets_map();

// Bail early if no widgets are returned or data is invalid.
if ( empty( $muia_widgets_map ) || ! is_array( $muia_widgets_map ) ) {
	return;
}

// Retrieve unique categories from widgets for the filter nav.
$muia_categories = array();
foreach ( $muia_widgets_map as $muia_widget ) {
	if ( ! empty( $muia_widget['category'] ) && is_string( $muia_widget['category'] ) ) {
		$category_key = sanitize_key( $muia_widget['category'] );
		if ( ! isset( $muia_categories[ $category_key ] ) ) {
			// Capitalize for display.
			$muia_categories[ $category_key ] = ucfirst( $category_key );
		}
	}
}

$muia_all_active = ! empty( $muia_widgets_map ) && ! array_filter(
    $muia_widgets_map,
    function( $muia_widget ) {
        return empty( $muia_widget['is_active'] );
    }
);
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
						<?php echo esc_html( $muia_cat_label ); ?>
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
		<?php foreach ( $muia_widgets_map as $muia_widget_slug => $muia_widget ) :

			// Sanitize slug used in attributes and IDs.
			$muia_widget_slug = sanitize_key( $muia_widget_slug );

			// Bail if slug is empty after sanitization.
			if ( empty( $muia_widget_slug ) ) {
				continue;
			}

			// Safely extract and sanitize individual widget fields.
			$muia_title      = isset( $muia_widget['title'] )     && is_string( $muia_widget['title'] )    ? $muia_widget['title']              : '';
			$muia_category   = isset( $muia_widget['category'] )  && is_string( $muia_widget['category'] ) ? sanitize_key( $muia_widget['category'] ) : '';
			$muia_icon       = isset( $muia_widget['icon'] )      && is_string( $muia_widget['icon'] )     ? $muia_widget['icon']               : '';
			$muia_demo_url   = isset( $muia_widget['demo'] )      && is_string( $muia_widget['demo'] )     ? $muia_widget['demo']               : '';
			$muia_tutorial   = isset( $muia_widget['tutorial'] )  && is_string( $muia_widget['tutorial'] ) ? $muia_widget['tutorial']           : '';
			$muia_is_active  = isset( $muia_widget['is_active'] ) ? (bool) $muia_widget['is_active']       : false;
			$muia_is_pro     = isset( $muia_widget['is_pro'] )    ? (bool) $muia_widget['is_pro']          : false;
			$muia_upcoming   = isset( $muia_widget['is_upcoming'] ) ? (bool) $muia_widget['is_upcoming']   : false;

			// Sanitize and validate demo/tutorial URLs — only allow http/https or empty.
			$muia_demo_url = ! empty( $muia_demo_url ) ? esc_url( $muia_demo_url ) : '';
			$muia_tutorial = ! empty( $muia_tutorial ) ? esc_url( $muia_tutorial ) : '';

			// Build CSS classes for the card (used by JS isotope/filter).
			$muia_card_classes = array( 'th-widget-card', $muia_widget_slug );
			if ( ! empty( $muia_category ) ) {
				$muia_card_classes[] = 'muia-cat-'.$muia_category;
			}
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
				<?php if(! empty( $muia_demo_url ) || ! empty( $muia_tutorial )): ?>
				<div class="gap-2 d-flex align-items-center">

					<?php if ( ! empty( $muia_demo_url ) ) : ?>
						<a
							href="<?php echo esc_url( $muia_demo_url ); ?>"
							class="th-doc-link"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
								/* translators: %s: widget title */
								printf( esc_attr__( 'View demo for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
							?>"
						>
							<i class="th-icon-link" aria-hidden="true"></i>
							<?php esc_html_e( 'Demo', 'motionui-addons-for-elementor' ); ?>
						</a>
					<?php endif; ?>

					<?php if ( ! empty( $muia_tutorial ) ) : ?>
						<a
							href="<?php echo esc_url( $muia_tutorial ); ?>"
							class="th-doc-link"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
								/* translators: %s: widget title */
								printf( esc_attr__( 'Watch tutorial for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
							?>"
						>
							<i class="th-icon-video" aria-hidden="true"></i>
							<?php esc_html_e( 'Tutorial', 'motionui-addons-for-elementor' ); ?>
						</a>
					<?php endif; ?>

				</div>
				<?php endif; ?>
			</div><!-- .card-con -->

			<div class="th-switch-control d-flex align-items-center ml-auto">
				<input
					type="checkbox"
					id="toggle-<?php echo esc_attr( $muia_widget_slug ); ?>"
					name="widgets[]"
					value="<?php echo esc_attr( $muia_widget_slug ); ?>"
					<?php checked( $muia_is_active, true ); ?>
					<?php disabled( $muia_upcoming || $muia_is_pro, true ); ?>
					aria-label="<?php
						/* translators: %s: widget title */
						printf( esc_attr__( 'Toggle %s widget', 'motionui-addons-for-elementor' ), esc_attr( $muia_title ) );
					?>"
				/>
				<label
					class="switch-label"
					for="toggle-<?php echo esc_attr( $muia_widget_slug ); ?>"
					aria-hidden="true"
				></label>
			</div><!-- .th-switch-control -->

		</div><!-- .th-widget-card -->

		<?php endforeach; ?>
	</div><!-- .widget-card-wrap -->

</form><!-- .muia-dashboard-form -->