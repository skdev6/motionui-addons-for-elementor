<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Widgets_Manager{

    const WIDGET_DB_KEY = 'muia_pro_inactive_widgets';

    public static function get_inactive_widgets(){
        return get_option(self::WIDGET_DB_KEY, []);
    }
    public static function update_inactive_widgets($widgets = []){
        update_option(self::WIDGET_DB_KEY, $widgets);
    }
    /**
     * Map key => class name suffix, e.g. glow-button => Glow_Button.
     */
    public static function get_class_suffix($widget_key){
        return str_replace('-', '_', ucwords($widget_key, '-'));
    }

    /**
     * Resolve the class that should render a widget.
     *
     * A bundled/pro class always wins. An uploaded custom widget is only used
     * as a fallback, and only for entries flagged with is_in_custom_widget.
     *
     * @return string|false Class name, or false when nothing is installed.
     */
    public static function resolve_widget_class($widget_key, $widget_data = []){

        $suffix = self::get_class_suffix($widget_key);

        // 1st priority: bundled or pro widget.
        $class_name = '\Themeic\MotionUI_Addons\Widgets\\' . $suffix;

        if(class_exists($class_name)){
            return $class_name;
        }

        // 2nd priority: uploaded custom widget.
        if(!empty($widget_data['is_in_custom_widget'])){
            $custom_class = '\Themeic\CustomWidget\\' . $suffix;

            if(class_exists($custom_class)){
                return $custom_class;
            }
        }

        return false;
    }

    public static function get_widgets_map(){
        $get_inactive_widgets = get_option(self::WIDGET_DB_KEY, []);
        $local_widgets = self::local_widgets_map();

        foreach ($get_inactive_widgets as $key) {
            if (isset($local_widgets[$key])) {
                $local_widgets[$key]['is_active'] = false;
            }
        }

        // Flag custom widgets the user has not installed (or has deleted).
        foreach ($local_widgets as $key => $widget) {

            if(empty($widget['is_in_custom_widget'])){
                continue;
            }

            if(!self::resolve_widget_class($key, $widget)){
                $local_widgets[$key]['widget_not_installed'] = true;
            }
        }

        return $local_widgets;
    }
    public static function get_pro_widgets_map(){
        $get_inactive_widgets = get_option(self::WIDGET_DB_KEY, []);
        $pro_widgets = self::get_pro_widgets();

        foreach ($get_inactive_widgets as $key) {
            if (isset($pro_widgets[$key])) {
                $pro_widgets[$key]['is_active'] = false;
            }
        }

        return $pro_widgets;
    }
    public static function get_active_widgets() {
        return array_filter(
            self::get_widgets_map(),
            function( $widget ) {
                return isset( $widget['is_active'] ) && $widget['is_active'] === true;
            }
        );
    }

    public static function get_pro_widgets() {
        return array_filter(
            self::local_widgets_map(),
            function( $widget ) {
                return isset( $widget['is_pro'] ) && $widget['is_pro'] === true;
            }
        );
    }

    public static function local_widgets_map(){
        return [
            // Buttons
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
            // Slider
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
            // Images and Gallery
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
            ],
            'motion-gallery'=>[  
                'title' => __('Motion Gallery', 'motionui-addons-for-elementor'),
                'category'=> 'image',
                'is_active'=> true,
                'is_pro'       => true,
                'is_upcoming'  => true,
                'icon'=>'eicon-gallery-justified',
                'demo'=> 'dd',
                'tutorial'=> 'cd',
            ],
            // Portfolio and Filter
            'post-filter'=>[  
                'title' => __('Portfolio and Post Filter', 'motionui-addons-for-elementor'),
                'category'=> ['filter', 'portfolio'],
                'is_active'=> true,
                'is_pro'       => true,
                'is_upcoming'  => false,
                'icon'=>'eicon-posts-grid',
                'demo'=> 'dd',
                'tutorial'=> 'cd',
            ],
            'glow-button'=>[  
                'title' => __('Glow Button', 'motionui-addons-for-elementor'),
                'category'=> ['button'],
                'is_active'=> true,
                'is_pro'       => true,
                'is_upcoming'  => false,
                'is_in_custom_widget'    => true,
                'icon'=>'eicon-posts-grid',
                'demo'=> 'dd',
                'tutorial'=> 'cd',
            ],
            // Testimonial

        ];
    }
    public static function register_widgets($widgets_manager = null){

        if(!$widgets_manager){
            return;
        }

        foreach (self::get_active_widgets() as $widget_key => $widget_data) {

            // Bundled/pro first, uploaded custom widget as fallback.
            $class_name = self::resolve_widget_class($widget_key, $widget_data);

            if($class_name){
                $widgets_manager->register(new $class_name());
            }

        }

        // Uploaded widgets with no entry in the map still register, so a widget
        // bought before the plugin knew about it remains usable. Entries that
        // ARE mapped are skipped here — their toggle decides, including "off".
        $mapped_classes = [];

        foreach (self::local_widgets_map() as $widget_key => $widget_data) {
            if(!empty($widget_data['is_in_custom_widget'])){
                $mapped_classes[] = 'Themeic\CustomWidget\\' . self::get_class_suffix($widget_key);
            }
        }

        foreach (Custom_Widgets_Manager::get_widget_classes() as $custom_class) {

            if(in_array(ltrim($custom_class, '\\'), $mapped_classes, true)){
                continue;
            }

            $widgets_manager->register(new $custom_class());
        }
        if ( ! muia_has_pro() && ! empty( self::get_pro_widgets() ) ) {
            // foreach ( self::get_pro_widgets() as $name => $widget ) {
            //     $widgets_manager->register(
            //         new \Themeic\MotionUI_Addons\Widgets\Motionui_Placeholder( $name, $widget )
            //     );
            // }
        }
    }
    public static function save_widgets($widgets = []){
        update_option( self::WIDGET_DB_KEY, $widgets);
    }
}
