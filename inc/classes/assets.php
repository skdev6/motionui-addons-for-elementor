<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets{

    public static function register_scripts(){
        wp_register_script('gsap', THEMEIC_MUIA_ASSETS . 'js/gsap.min.js', ['jquery'], '3.15.0', true);
        wp_register_script('ScrollTrigger', THEMEIC_MUIA_ASSETS . 'js/ScrollTrigger.min.js', ['gsap'], '3.15.0', true);
        wp_register_script('Draggable', THEMEIC_MUIA_ASSETS . 'js/Draggable.min.js', ['gsap'], '3.15.0', true);
        wp_register_script('DrawSVGPlugin', THEMEIC_MUIA_ASSETS . 'js/DrawSVGPlugin.min.js', ['gsap'], '3.15.0', true);
        wp_register_script('SplitText', THEMEIC_MUIA_ASSETS . 'js/SplitText.min.js', ['gsap'], '3.13.0', true);
        wp_register_script('split-type', THEMEIC_MUIA_ASSETS . 'js/split-type.js', ['gsap'], '3.13.0', true);
        wp_register_script('Flip', THEMEIC_MUIA_ASSETS . 'js/Flip.min.js', ['gsap'], '3.15.0', true);
        wp_register_script('MotionPathPlugin', THEMEIC_MUIA_ASSETS . 'js/MotionPathPlugin.min.js', ['gsap'], '3.15.0', true);
        wp_register_script('Observer', THEMEIC_MUIA_ASSETS . 'js/Observer.min.js', ['gsap'], '3.15.0', true);
        //
        wp_register_script('motionui-addons', THEMEIC_MUIA_ASSETS . 'js/motionui-addons.js', ['gsap'], THEMEIC_MUIA_VERSION, true); 
        // wp_register_script('motionui-addons-init', THEMEIC_MUIA_ASSETS . 'js/motionui-addons-init.js', ['gsap', 'motionui-addons-for-elementor'], THEMEIC_MUIA_VERSION, true); 
    }    

    public static function enqueue_scripts(){
        wp_enqueue_script('gsap');
        wp_enqueue_script('ScrollTrigger');
        wp_enqueue_script('Observer');
        wp_enqueue_script('SplitText');      
        wp_enqueue_script('split-type');      
        wp_enqueue_script('motionui-addons');  
        // wp_enqueue_script('motionui-addons-init'); 
    }
    public static function enqueue_styles(){  
         wp_enqueue_style(
            'motionui-addons-widgets', 
            THEMEIC_MUIA_ASSETS . 'css/widgets.css', 
            [], 
            THEMEIC_MUIA_VERSION
        ); 
         wp_enqueue_style(
            'themeic-icons', 
            THEMEIC_MUIA_ASSETS . 'fonts/th-icon-basic.css', 
            [], 
            THEMEIC_MUIA_VERSION
        ); 
    }
}   