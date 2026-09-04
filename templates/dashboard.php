<?php
/**
 * Dashboard Main Template
 *
 * This file is loaded by the plugin and should not be accessed directly.
 *
 * @package MotionUI Addons for Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Themeic\MotionUI_Addons\Inc\Classes\Dashboard;
?>

<?php
$muia_all_widgets_url = apply_filters( 'muia_all_widgets_url', 'https://themeic.com/' );
?>
<div class="themeic-das-wrap th-das-root" data-all-widget="<?php echo esc_url( $muia_all_widgets_url ); ?>">
    <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/dasboard-header.php'; ?>
    
    <div class="th-das-content">
        <div class="th-das-content-wrapper container-fluid pb-container d-flex">
            
            <div class="th-left-content">
                <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/dasboard-left-nav.php'; ?>
            </div>
            
            <div class="th-right-content">
                <div id="muia-widgets" class="tab-content active">
                    <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/widgets.php'; ?>
                </div>
                <div id="muia-extensions" class="tab-content">
                    <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/extensions.php'; ?>
                </div>
                <?php do_action('add_muia_dashboard_page'); ?>
            </div>
            
        </div>
    </div>
</div>
<?php 
Dashboard::pro_html();
Dashboard::get_widget_html();
?>