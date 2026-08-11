<?php
/**
 * Dashboard Custom Widgets Template
 *
 * Lets users upload their own widget folder as a zip file
 * (e.g. minimal-button/index.php, js, css). Uploaded widgets are
 * extracted to uploads/motionui-addons-for-elementor/{widget-folder}.
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

use Themeic\MotionUI_Addons\Inc\Classes\Custom_Widgets_Manager;

if ( ! class_exists( 'Themeic\MotionUI_Addons\Inc\Classes\Custom_Widgets_Manager' ) ) {
	return;
}

$muia_custom_widgets = Custom_Widgets_Manager::get_custom_widgets();

// Upload status notice (set by the redirect after upload).
$muia_upload_status  = isset( $_GET['muia_upload'] ) ? sanitize_key( $_GET['muia_upload'] ) : '';
$muia_status_notices = Custom_Widgets_Manager::get_status_messages();
$muia_has_notice     = $muia_upload_status && isset( $muia_status_notices[ $muia_upload_status ] );
?>

<div class="muia-custom-widgets-wrap">

	<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-1 justify-content-between">
		<h4 class="title-md">
			<?php
			printf(
				/* translators: %s: linked "Themeic" brand name */  
				esc_html__( '%s', 'motionui-addons-for-elementor' ),
				'<a href="https://themeic.com/" class="text-dark text-link-btn" target="_blank" rel="noopener noreferrer">Themeic Widget Library <i class="eicon-arrow-right"></i></a>'
			);
			?>
		</h4>
		<a class="th-das-btn btn-sm btn-secondary ml-auto" href="https://themeic.com/">
			<i class="eicon-cart-medium" aria-hidden="true"></i>
			<?php esc_html_e( 'Get Widgets', 'motionui-addons-for-elementor' ); ?>
		</a>
	</div>
			<div class="header-second d-flex align-items-center gap-2">
				<h4 class="title-md mb-0"><?php esc_html_e( 'Add Widget', 'motionui-addons-for-elementor' ); ?></h4>
				<button class="th-das-btn btn-sm import-widget-btn <?php echo $muia_has_notice ? 'active' : ''; ?>">
					<?php esc_html_e( 'Upload Widget', 'motionui-addons-for-elementor' ); ?>
				</button>
			</div>
			<div class="upload-wrapper <?php echo $muia_has_notice ? 'active' : ''; ?>">
				<form
					class="muia-custom-widget-upload-form th-das-navbar inline-nav ml-auto mr-auto"
					method="post"
					action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					enctype="multipart/form-data"
				>
					<input type="hidden" name="action" value="<?php echo esc_attr( Custom_Widgets_Manager::MUIA_UPLOAD_ACTION ); ?>" />
					<?php wp_nonce_field( Custom_Widgets_Manager::MUIA_UPLOAD_NONCE, 'muia_custom_widget_nonce' ); ?>
					<input
						type="file"
						name="muia_widget_zip"
						accept=".zip"
						required
						aria-label="<?php esc_attr_e( 'Widget zip file', 'motionui-addons-for-elementor' ); ?>"
					/>
					<button type="submit" class="th-das-btn btn-sm">
						<i class="eicon-upload" aria-hidden="true"></i>
						<?php esc_html_e( 'Install Widget', 'motionui-addons-for-elementor' ); ?>
					</button>
				</form>
				<?php if ( $muia_has_notice ) : ?>
					<div class="muia-upload-notice muia-upload-notice-<?php echo esc_attr( $muia_status_notices[ $muia_upload_status ][0] ); ?>">
						<?php
						echo wp_kses(
							$muia_status_notices[ $muia_upload_status ][1],
							[
								'a' => [
									'href'   => [],
									'target' => [],
									'rel'    => [],
								],
							]
						);
						?>
					</div>
				<?php endif; ?>
			</div>

	<?php if ( ! empty( $muia_custom_widgets ) ) : ?>
		<h4 class="title-md installed-w-title"><?php esc_html_e( 'Installed widgets', 'motionui-addons-for-elementor' ); ?></h4>
		<div class="muia-installed-custom-widgets widget-card-wrap">
			<?php foreach ( $muia_custom_widgets as $muia_widget_slug => $muia_widget_path ) :

				// Readable title from the folder name, e.g.
				// themeic-minimal-button-widget => Minimal Button.
				$muia_widget_title = preg_replace(
					[ '/^themeic[-_]/', '/[-_]elementor[-_]widget$/', '/[-_]widget$/' ],
					'',
					$muia_widget_slug
				);
				$muia_widget_title = ucwords( str_replace( [ '-', '_' ], ' ', $muia_widget_title ) );

				// Demo/tutorial URLs and icon from the widget class, when it provides them.
				$muia_demo_url     = '';
				$muia_tutorial_url = '';
				$muia_widget_icon  = '';
				$muia_widget_class = Custom_Widgets_Manager::get_widget_class( $muia_widget_slug );

				if ( class_exists( '\Elementor\Widget_Base' ) && class_exists( $muia_widget_class ) ) {
					$muia_widget_instance = new $muia_widget_class();

					if ( method_exists( $muia_widget_instance, 'get_themeic_demo_url' ) ) {
						$muia_demo_url = (string) $muia_widget_instance->get_themeic_demo_url();
					}
					if ( method_exists( $muia_widget_instance, 'get_themeic_tutorial_url' ) ) {
						$muia_tutorial_url = (string) $muia_widget_instance->get_themeic_tutorial_url();
					}
					if ( method_exists( $muia_widget_instance, 'get_icon' ) ) {
						$muia_widget_icon = (string) $muia_widget_instance->get_icon();
					}
				}
			?>
				<!-- Widget Card -->
				<div class="th-widget-card <?php echo esc_attr( $muia_widget_slug ); ?>">
					<div class="icon-wrap" aria-hidden="true">
						<img class="icon-themeic" src="<?php echo esc_url( THEMEIC_MUIA_ASSETS . '/img/themeic-logo.svg' ); ?>">
						<?php if ( $muia_widget_icon ) : ?>
							<i class="<?php echo esc_attr( $muia_widget_icon ); ?>"></i>
						<?php endif; ?>
					</div>
					<div class="card-con">
						<h4 class="title">
							<?php echo esc_html( $muia_widget_title ); ?>
						</h4>
						<?php if ( $muia_demo_url || $muia_tutorial_url ) : ?>
						<div class="gap-2 d-flex align-items-center">

							<?php if ( $muia_demo_url ) : ?>
								<a
									href="<?php echo esc_url( $muia_demo_url ); ?>"
									class="th-doc-link"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php
										/* translators: %s: widget title */
										printf( esc_attr__( 'View demo for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_widget_title ) );
									?>"
								>
									<i class="th-icon-link" aria-hidden="true"></i>
									<?php esc_html_e( 'Demo', 'motionui-addons-for-elementor' ); ?>
								</a>
							<?php endif; ?>

							<?php if ( $muia_tutorial_url ) : ?>
								<a
									href="<?php echo esc_url( $muia_tutorial_url ); ?>"
									class="th-doc-link"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php
										/* translators: %s: widget title */
										printf( esc_attr__( 'Watch tutorial for %s', 'motionui-addons-for-elementor' ), esc_attr( $muia_widget_title ) );
									?>"
								>
									<i class="th-icon-video" aria-hidden="true"></i>
									<?php esc_html_e( 'Tutorial', 'motionui-addons-for-elementor' ); ?>
								</a>
							<?php endif; ?>

						</div>
						<?php endif; ?>
					</div>
					<button
						type="button"
						class="ml-auto btn-transparent delete-custom-widget"
						data-widget="<?php echo esc_attr( $muia_widget_slug ); ?>"
						aria-label="<?php esc_attr_e( 'Delete widget', 'motionui-addons-for-elementor' ); ?>"
					><i class="eicon-library-delete"></i></button>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<!-- Empty state: shown when no widgets are installed, or via JS after the last one is deleted. -->
	<div class="muia-no-custom-widgets text-center" <?php echo ! empty( $muia_custom_widgets ) ? 'style="display:none"' : ''; ?>>
		<h2 class="title-md">
			<?php esc_html_e( 'No Widgets Installed Yet', 'motionui-addons-for-elementor' ); ?>
		</h2>
		<p class="muia-no-widgets-desc">
			<?php esc_html_e( 'Supercharge your site with premium widgets crafted by Themeic. Browse the collection, pick the widgets you need, and import them here with one click.', 'motionui-addons-for-elementor' ); ?>
		</p>
		<a
			href="https://themeic.com/"
			class="th-das-btn"
			target="_blank"
			rel="noopener noreferrer"
		>
			<i class="eicon-cart-medium" aria-hidden="true"></i>
			<?php esc_html_e( 'Get Widgets from Themeic', 'motionui-addons-for-elementor' ); ?>
		</a>
	</div>

	<?php do_action( 'muia_custom_widgets_content' ); ?>
</div>
