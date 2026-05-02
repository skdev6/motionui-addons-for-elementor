<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Widgets_Manager{

    const WIDGET_DB_KEY = 'muia_inactive_widgets';

    public static function get_inactive_widgets(){
        return get_option(self::WIDGET_DB_KEY, []);
    }
    public static function update_inactive_widgets($widgets = []){
        update_option(self::WIDGET_DB_KEY, $widgets);
    }
    public static function get_widgets_map(){     
        $get_inactive_widgets = get_option(self::WIDGET_DB_KEY, []);
        $local_widgets = self::local_widgets_map();

        foreach ($get_inactive_widgets as $key) {
            if (isset($local_widgets[$key])) {
                $local_widgets[$key]['is_active'] = false;
            }
        }

        return $local_widgets;
    }
    public static function get_active_widgets() {
        return array_filter(
            self::get_widgets_map(),
            function( $widget ) {
                return isset( $widget['is_active'] ) && $widget['is_active'] === true;
            }
        );
    }
    public static function local_widgets_map(){
        return [
            'animated-button'=>[
                'title' => __('Button', 'motionui-addons-for-elementor'),
                'category'=> 'button',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'icon'=>'eicon-button',
                'demo'=> '',
                'tutorial'=> '',
            ],
            'animated-slider'=>[  
                'title' => __('Slider', 'motionui-addons-for-elementor'),
                'category'=> 'image',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'icon'=>'eicon-post-slider',
                'demo'=> '',
                'tutorial'=> '',
            ],
            'animated-image'=>[  
                'title' => __('Image', 'motionui-addons-for-elementor'),
                'category'=> 'image',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'icon'=>'eicon-image',
                'demo'=> '',
                'tutorial'=> '',
            ],
            'animated-gallery'=>[  
                'title' => __('Gallery', 'motionui-addons-for-elementor'),
                'category'=> 'image',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'icon'=>'eicon-gallery-justified',
                'demo'=> '',
                'tutorial'=> '',
            ]
        ];
    }
    public static function register_widgets($widgets_manager = null){

        foreach (self::get_active_widgets() as $widget_key => $widget_data) {
            $file = THEMEIC_MUIA_DIR_PATH . 'widgets/' . $widget_key . '.php';

            if(is_readable($file)){

                $class_name = '\Themeic\MotionUI_Addons\Widgets\\' . str_replace('-', '_', ucwords($widget_key, '-'));

                if(class_exists($class_name)){
                    $widgets_manager->register(new $class_name());  
                }

            }

        }
    }
    public static function save_widgets($widgets = []){
        update_option( self::WIDGET_DB_KEY, $widgets);
    }
}