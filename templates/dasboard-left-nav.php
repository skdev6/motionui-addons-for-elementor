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
        </li>
        <li>
            <a href="#muia-extensions">
                <i class="th-icon-mouse-click"></i>
                <?php esc_html_e('Extensions', 'motionui-addons-for-elementor'); ?>
            </a>
        </li>
    </ul>
    <div class="nav-left-footer">
        <a href="https://motionuiaddons.com/" target=_blank"" class="th-das-btn"><?php esc_html_e('View Demo', 'motionui-addons-for-elementor'); ?></a>
    </div>
</div>