<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Widgets_Manager{

    const WIDGET_DB_KEY = 'muia_active_widgets';

    public static function get_inactive_widgets(){
        return get_option(self::WIDGET_DB_KEY, []);
    }
    public static function update_inactive_widgets($widgets = []){
        update_option(self::WIDGET_DB_KEY, $widgets);
    }
    public static function get_widgets_map(){
        return [
            'animate-button'=>[
                'title' => __('Animated Button', 'motionui-addons-for-elementor'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
            'animate-slider'=>[  
                'title' => __('Animate Slide', 'motionui-addons-for-elementor'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
            'animate-image'=>[  
                'title' => __('Animate Image', 'motionui-addons-for-elementor'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
            'animate-gallery'=>[  
                'title' => __('Animate Gallery', 'motionui-addons-for-elementor'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ]
        ];
    }
    public static function register_widgets($widgets_manager = null){

        foreach (self::get_widgets_map() as $widget_key => $widget_data) {
            $file = THEMEIC_MUIA_DIR_PATH . 'widgets/' . $widget_key . '.php';

            if(is_readable($file)){

                $class_name = '\Themeic\MotionUI_Addons\Widgets\\' . str_replace('-', '_', ucwords($widget_key, '-'));

                if(class_exists($class_name)){
                    $widgets_manager->register(new $class_name());  
                }

            }

        }
    }
}