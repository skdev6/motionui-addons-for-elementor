<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

class Dashboard{

    public $slug = 'motionui-addons';

    public static function add_menu(){ 

        $page_slug = Motionui::get_admin_menu_slug();
        $menu_name = Motionui::get_admin_name();

        add_menu_page(
            $menu_name,
            $menu_name,
            'manage_options',
            $page_slug,
            [__CLASS__, "init_page"],
            THEMEIC_MUIA_ASSETS . "/img/themeic-icon.svg",
            3
        );
        add_submenu_page(
            $page_slug,
            esc_html__("Settings", "motionui-addons"),
            esc_html__("Settings", "motionui-addons"),
            "manage_options",
            $page_slug,
            [__CLASS__, "init_page"]
        );
    }
    public static function enqueue_scripts($hook){
        $screen = get_admin_page_parent();
        $page_slug = Motionui::get_admin_menu_slug();


        if (Motionui::is_motion_admin_page($hook)) {
            wp_enqueue_style(
                'themeic-das-motionui', 
                THEMEIC_MUIA_ASSETS . 'css/themeic-dasboard.min.css', 
                null, 
                THEMEIC_MUIA_VERSION
            );
            wp_enqueue_style(
                'themeic-das-main', 
                THEMEIC_MUIA_ASSETS . 'css/style.css', 
                null, 
                THEMEIC_MUIA_VERSION
            );
            wp_enqueue_style(
                'th-icon-basic', 
                THEMEIC_MUIA_ASSETS . 'fonts/th-icon-basic.css', 
                null, 
                THEMEIC_MUIA_VERSION
            );
            
            // If you have JS for the dashboard:
            // wp_enqueue_script('themeic-das-motionui-js', THEMEIC_MUIA_ASSETS . 'js/dashboard.js', ['jquery'], THEMEIC_MUIA_VERSION, true);
        }
    }
    public static function init_page(){

        $dasboard_file = THEMEIC_MUIA_DIR_PATH . 'templates/dashboard.php';

        if(is_readable($dasboard_file)){
            include_once $dasboard_file;
        }

    }
}