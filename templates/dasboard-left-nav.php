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
<div class="th-left-nav d-lg-flex">
    <span class="logo-text2"><?php esc_html_e('Menu', 'motionui-addons-for-elementor'); ?></span>
    <ul class="th-das-navbar">
        <li class="current-menu-item">
            <a href="#muia-widgets">
                <i class="eicon-shape"></i>
                <?php esc_html_e('Widgets', 'motionui-addons-for-elementor'); ?>
            </a>
            <ul class="submenu">    
                <li><a  href="#muia-custom-widgets"><?php esc_html_e('Add Widget', 'motionui-addons-for-elementor'); ?></a></li>
            </ul>
        </li>
        
        <li>
            <a href="#muia-extensions">
                <i class="th-icon-mouse-click"></i>
                <?php esc_html_e('Tools', 'motionui-addons-for-elementor'); ?>
            </a>
        </li>
        <?php do_action('add_muia_dashboard_menu'); ?>
    </ul>
    <div class="nav-left-footer">
        <a href="https://motionuiaddons.com/" target=_blank"" class="th-das-btn"><?php esc_html_e('View Demo', 'motionui-addons-for-elementor'); ?></a>
    </div>
</div>