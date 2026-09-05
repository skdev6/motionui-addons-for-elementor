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
    /**
     * Is the Pro plugin installed and running?
     *
     * Says nothing about the licence — use it to tell "no Pro at all" apart
     * from "Pro is here but not activated", so the dashboard can offer to
     * activate a licence instead of selling one that is already owned.
     */
    public static function is_pro_installed() {
        return defined('THEMEIC_MUIA_PRO_VERSION');
    }

    /**
     * Are the Pro features unlocked?
     *
     * True only when Pro is running AND its licence is valid. The licence
     * itself lives in the Pro plugin, which answers through this filter — the
     * free plugin never sees a key or talks to the store.
     *
     * Defaults to false, so Pro without a valid licence stays locked.
     */
    public static function is_active_pro() {

        if ( ! self::is_pro_installed() ) {
            return false;
        }

        /**
         * Filter the licence state.
         *
         * @param bool $is_active Whether the Pro licence is valid.
         */
        return (bool) apply_filters( 'muia_pro_license_active', false );
    }
}