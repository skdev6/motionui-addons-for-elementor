<?php
/**
 * Dashboard Main Template
 *
 * This file is loaded by the plugin and should not be accessed directly.
 *
 * @package MotionUI Addons for Elementor
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="th-das-header-sm flex-wrap sticky-nav sticky-das-nav-top-30 d-flex align-items-center gap-2 justify-content-between">
    <ul class="th-das-navbar inline-nav">
        <li class="current-menu-item"><a href="#"><?php esc_html_e( 'All Widgets', 'motionui-addons-for-elementor' ); ?></a></li>
        <li><a href="#"><?php esc_html_e( 'Cards', 'motionui-addons-for-elementor' ); ?></a></li>
        <li><a href="#"><?php esc_html_e( 'Buttons', 'motionui-addons-for-elementor' ); ?></a></li>
    </ul>
    
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
    <?php foreach ( MotionUiClasses\Widgets_Manager::get_widgets_map() as $muia_widget_slug => $muia_widget ) : ?>
        <div class="th-widget-card <?php echo esc_attr( $muia_widget_slug ); ?>">
            <div class="icon-wrap">
                <i class="th-icon">
                    <!-- Your SVG here -->
                </i>
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