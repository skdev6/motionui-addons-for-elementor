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
<header class="themeic-das-header">
    <div class="container-fluid d-flex align-items-center gap-2 py-3 th-das-header">
        <span class="logo-wrap th-text-primary mr-auto">
            <img src="<?php echo THEMEIC_MUIA_ASSETS . '/img/motionui-logo-black.svg'; ?>" alt="MotionUi Addons Logo" class="logo-img">
            <?php _e('MotionUi Addons', 'motionui-addons-for-elementor'); ?>
        </span>
        <a href="#" class="th-das-btn btn-sm btn-secondary"><?php _e('View All Demo', 'motionui-addons-for-elementor'); ?></a>
    </div>
</header>