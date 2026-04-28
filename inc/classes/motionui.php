<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Motionui {

    /**
     * The unique slug used for admin pages and URLs
     */
    public static function get_admin_menu_slug() {
        return 'motion-ui-addons';
    }
    /**
     * Check is dasboard
     */
    public static function is_motion_admin_page( $hook ) {
        return ( 
            strpos($hook, 'motion') !== false && 
            strpos($hook, 'ui')     !== false && 
            strpos($hook, 'addons') !== false 
        );
    }
    /**
     * The display name of the plugin
     */
    public static function get_admin_name() {
        return __( 'MotionUI Addons', 'motionui-addons-for-elementor' );
    }
    /**
     * The display name of the plugin
     */
    public static function get_extention_logo() {
        return __( 'MotionUI Addons', 'motionui-addons-for-elementor' );
    }
    
}