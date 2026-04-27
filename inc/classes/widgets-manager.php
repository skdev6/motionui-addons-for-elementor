<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

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
                'title' => __('Animate Button', 'motionui-addons'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
            'themeic-button'=>[  
                'title' => __('Animate Slider', 'motionui-addons'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
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