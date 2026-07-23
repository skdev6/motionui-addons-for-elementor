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

	<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">
		<h4 class="title-md">
			<?php
			printf(
				/* translators: %s: linked "Themeic" brand name */
				esc_html__( '%s Widget Library', 'motionui-addons-for-elementor' ),
				'<a href="https://themeic.com/" class="text-dark underline-none" target="_blank" rel="noopener noreferrer">Themeic</a>'
			);
			?>
		</h4>
		<button class="th-das-btn btn-sm import-widget-btn <?php echo $muia_has_notice ? 'active' : ''; ?>">
			<?php esc_html_e( 'Import Widget', 'motionui-addons-for-elementor' ); ?>
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
						<?php esc_html_e( 'Upload Widget', 'motionui-addons-for-elementor' ); ?>
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
		<div class="muia-installed-custom-widgets widget-card-wrap">
			<?php foreach ( $muia_custom_widgets as $muia_widget_slug => $muia_widget_path ) : ?>
				<!-- Widget Card -->
				<div class="th-widget-card <?php echo esc_attr( $muia_widget_slug ); ?>">
					<div class="icon-wrap" aria-hidden="true">
						<img class="icon-themeic" src="<?php echo esc_url( THEMEIC_MUIA_ASSETS . '/img/themeic-logo.svg' ); ?>">
					</div>
					<div class="card-con">
						<h4 class="title">
							<?php   
							$muia_widget_title = preg_replace(
								[ '/^themeic[-_]/', '/[-_]elementor[-_]widget$/', '/[-_]widget$/' ],
								'',
								$muia_widget_slug
							);
							echo esc_html( ucwords( str_replace( [ '-', '_' ], ' ', $muia_widget_title ) ) );
							?>
						</h4>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'muia_custom_widgets_content' ); ?>
</div>
