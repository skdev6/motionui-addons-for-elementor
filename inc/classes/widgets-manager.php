<?php 
namespace Themeic_Gsap_Addons\Inc\Classes;

class Widgets_Manager{

    const WIDGET_DB_KEY = 'themeic_gsap_inactive_addons';


    public function get_inactive_widgets(){
        return get_option(self::WIDGET_DB_KEY, []);
    }
    public function update_inactive_widgets($widgets = []){
        update_option(self::WIDGET_DB_KEY, $widgets);
    }
    public function get_widgets_map(){
        return [
            'creative-button'=>[
                'title' => __('GSAP Button', 'motionui-addons'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
            'creative-slider'=>[
                'title' => __('GSAP Slider', 'motionui-addons'),
                'category'=> 'general',
                'is_active'=> true,
                'is_pro'       => false,
                'is_upcoming'  => false,
                'demo'=> '',
                'tutorial'=> '',
            ],
        ];
    }
    public function register($manager = null){
        $inactive_widgets = $this->get_inactive_widgets();

        foreach ($this->get_widgets_map() as $widget_key => $widget_data) {
            
        }
    }
    public function register_widget($key, $manager = null){

    }
    
}