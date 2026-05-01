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

use Themeic\MotionUI_Addons\Inc\Classes\Extensions_Manager;
?>

<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">
    <h4 class="title-md">Elementor animation extension</h4>
    <div class="right-menu-item d-flex gap-2 align-items-center">
        <div class="th-switch-control th-text-primary d-flex align-items-center">
            <input type="checkbox" id="Enable-All"/>
            <span class="switch-label"></span>
            <label for="Enable-All"><?php esc_html_e( 'Enable All', 'motionui-addons-for-elementor' ); ?></label>
        </div>
        <div class="btn-wrap">
            <a href="#" class="th-das-btn btn-sm"><?php esc_html_e( 'Save Changes', 'motionui-addons-for-elementor' ); ?></a>
        </div>
    </div>
</div>

<div class="widget-card-wrap">
    <?php foreach ( Extensions_Manager::extension_map() as $muia_widget_slug => $muia_widget ) : ?>
        <div class="th-widget-card <?php echo esc_attr( $muia_widget_slug ); ?>">
            <div class="icon-wrap">
                <i class="<?php echo esc_attr( $muia_widget['icon'] ); ?>"></i>
            </div> 
            <div class="card-con">
                <h4 class="title"><?php echo esc_html( $muia_widget['title'] ?? '' ); ?></h4>
                
                <div class="gap-2 d-flex align-items-center">
                    <a href="#" class="th-doc-link">
                        <i class="th-icon-link"></i>
                        <?php esc_html_e( 'Demo', 'motionui-addons-for-elementor' ); ?>
                    </a>
                    <a href="#" class="th-doc-link">
                        <i class="th-icon-video"></i>
                        <?php esc_html_e( 'Tutorial', 'motionui-addons-for-elementor' ); ?>
                    </a>
                </div>
            </div>
            
            <div class="th-switch-control d-flex align-items-center ml-auto">
                <input type="checkbox" id="toggle-<?php echo esc_attr( $muia_widget_slug ); ?>"/>
                <label class="switch-label" for="toggle-<?php echo esc_attr( $muia_widget_slug ); ?>"></label>
            </div>
        </div>
    <?php endforeach; ?>
</div>