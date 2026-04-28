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
?>

<div class="themeic-das-wrap th-das-root">
    <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/dasboard-header.php'; ?>
    
    <div class="th-das-content">
        <div class="th-das-content-wrapper container-fluid pb-container d-flex">
            
            <div class="th-left-content">
                <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/dasboard-left-nav.php'; ?>
            </div>
            
            <div class="th-right-content">
                <?php require_once THEMEIC_MUIA_DIR_PATH . 'templates/dasboard-widgets.php'; ?>
            </div>
            
        </div>
    </div>
</div>