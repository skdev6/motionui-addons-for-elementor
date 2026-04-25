<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

class Assets{

    public static function register_scripts(){
        wp_register_script('gsap', THEMEIC_MUIA_ASSETS . 'js/gsap.min.js', [], null, true);
        wp_register_script('ScrollTrigger', THEMEIC_MUIA_ASSETS . 'js/ScrollTrigger.min.js', ['gsap'], null, true);
        wp_register_script('Draggable', THEMEIC_MUIA_ASSETS . 'js/Draggable.min.js', ['gsap'], null, true);
        wp_register_script('DrawSVGPlugin', THEMEIC_MUIA_ASSETS . 'js/DrawSVGPlugin.min.js', ['gsap'], null, true);
        wp_register_script('SplitText', THEMEIC_MUIA_ASSETS . 'js/SplitText.min.js', ['gsap'], null, true);
        wp_register_script('Flip', THEMEIC_MUIA_ASSETS . 'js/Flip.min.js', ['gsap'], null, true);
        wp_register_script('MotionPathPlugin', THEMEIC_MUIA_ASSETS . 'js/MotionPathPlugin.min.js', ['gsap'], null, true);
        wp_register_script('Observer', THEMEIC_MUIA_ASSETS . 'js/Observer.min.js', ['gsap'], null, true);
        wp_register_script('SplitText', THEMEIC_MUIA_ASSETS . 'js/SplitText.min.js', ['gsap'], null, true); 
        wp_register_script('motionui-addons-main', THEMEIC_MUIA_ASSETS . 'js/motionui-addons-main.js', ['gsap'], null, true); 
    }
    public static function enqueue_scripts(){
        wp_enqueue_script('gsap');
        wp_enqueue_script('ScrollTrigger');
        wp_enqueue_script('SplitText');
        wp_enqueue_script('Observer');
        wp_enqueue_script('SplitText'); 
        wp_enqueue_script('motionui-addons-main'); 
    }
}