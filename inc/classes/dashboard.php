<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Dashboard{

    public $slug = 'motionui-addons-for-elementor';

    public static function add_menu(){ 

        $page_slug = Motionui::get_admin_menu_slug();
        $menu_name = Motionui::get_admin_name();

        add_menu_page(
            $menu_name,
            $menu_name,
            'manage_options',
            $page_slug,
            [__CLASS__, "init_page"],
            THEMEIC_MUIA_ASSETS . "/img/motionui-logo-white.svg",
            3
        );
        add_submenu_page(
            $page_slug,
            esc_html__("Settings", 'motionui-addons-for-elementor'),
            esc_html__("Settings", 'motionui-addons-for-elementor'),
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
                'th-icon-basic', 
                THEMEIC_MUIA_ASSETS . 'fonts/th-icon-basic.css', 
                null, 
                THEMEIC_MUIA_VERSION
            );
            
            wp_enqueue_script('muia-dashboard', THEMEIC_MUIA_ASSETS . 'js/dashboard.js', ['jquery'], THEMEIC_MUIA_VERSION, true);

            wp_localize_script(
                'muia-dashboard',
                'muiaDashboard',
                [
                    'nonce' => wp_create_nonce( 'muia-dashboard-save-data' ),
                    'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                    'action' => 'muia_dashboard',
                    'saveChangesLabel' => esc_html__( 'Save Settings', 'motionui-addons-for-elementor' ),
                    'savedLabel' => esc_html__( 'Changes Saved', 'motionui-addons-for-elementor' ),
                ]
            );
        }
        wp_enqueue_style(
            'themeic-das-main', 
            THEMEIC_MUIA_ASSETS . 'css/style.css', 
            null, 
            THEMEIC_MUIA_VERSION
        );

    }
    public static function init_page(){

        $dasboard_file = THEMEIC_MUIA_DIR_PATH . 'templates/dashboard.php';

        if(is_readable($dasboard_file)){
            include_once $dasboard_file;
        }

    }
    public static function save_widgets_data($data) {

        $widgets_to_remove = !empty($data['widgets']) ? (array) $data['widgets'] : [];
        $real_map = Widgets_Manager::local_widgets_map();
        $filtered_map = array_diff_key($real_map, array_flip($widgets_to_remove));

        Widgets_Manager::save_widgets(array_keys($filtered_map));    

    }
    public static function save_extensions_data($data){

        $extensions_to_remove = !empty($data['extensions']) ? $data['extensions'] : [];
        $real_map = Extensions_Manager::local_extensions_map();
        $filtered_map = array_diff_key($real_map, array_flip($extensions_to_remove));

        Extensions_Manager::save_extensions(array_keys($filtered_map));

    }
    public static function save_data(){  
        // 1. Security Check
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        // 2. Nonce Check
        if ( ! check_ajax_referer( 'muia-dashboard-save-data', 'nonce' ) ) {
            wp_send_json_error( 'Invalid Nonce' );
        }

        $raw_data = isset($_POST['data']) ? $_POST['data'] : '';
        $type     = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

        $parsed_data = [];
        parse_str( $raw_data, $parsed_data );

        $final_data = muia_sanitize_array_recursively($parsed_data);
        $saved_all  = false;

        if($type === 'widgets'){
            self::save_widgets_data($final_data);
        }
        if($type === 'extensions'){
            self::save_extensions_data($final_data);
        }
        wp_send_json_success(array(  
            'message' => __( 'Settings saved successfully!', 'motionui-addons-for-elementor' ),
            'type'    => $type,
        ));
    }
}