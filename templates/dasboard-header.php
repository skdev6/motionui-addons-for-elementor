<?php
/**
 * Dashboard Header Template
 *
 * @package MotionUI Addons for Elementor
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<header class="themeic-das-header">
    <div class="container-fluid d-flex align-items-center gap-2 py-3 th-das-header">
        <span class="logo-wrap th-text-primary mr-auto">
            <img src="<?php echo esc_url( THEMEIC_MUIA_ASSETS . '/img/motionui-logo-black.svg' ); ?>" 
                 alt="<?php esc_attr_e( 'MotionUI Addons Logo', 'motionui-addons-for-elementor' ); ?>" 
                 class="logo-img">
            
            <?php esc_html_e( 'MotionUI Addons', 'motionui-addons-for-elementor' ); ?>
        </span>
        
        <a href="https://motionuiaddons.com/" target="_blank" class="th-das-btn btn-sm btn-secondary">
            <?php esc_html_e( 'View All Demo', 'motionui-addons-for-elementor' ); ?>
        </a>
    </div>
</header>  