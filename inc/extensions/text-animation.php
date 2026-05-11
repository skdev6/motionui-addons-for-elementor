<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Text_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'muia_addons_text_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Text Animation', 'motionui-addons-for-elementor')),
            ]
        );
		$element->add_control(
			'muia_text_ani',
			array(
				'label'              => esc_html__( 'Text Animation', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '',
				'frontend_available' => true,
				'prefix_class'       => 'has-muia-text-animation visibility__hidden muia-text-',
				'options'            => array(
					''           => esc_html__( 'None', 'motionui-addons-for-elementor' ),
					'fade'    => esc_html__( 'Fade', 'motionui-addons-for-elementor' ),
					'wave'       => esc_html__( 'Wave', 'motionui-addons-for-elementor' ),
					'reveal'     => esc_html__( 'Reveal', 'motionui-addons-for-elementor' ),
					'scramble'   => esc_html__( 'Scramble', 'motionui-addons-for-elementor' ),
				),
			)
		);
		$element->add_control(
			'muia_text_direction',
			[
				'label' => esc_html__( 'Direction', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'btt',
				'frontend_available' => true,  
				'options' => [
					'ltr' => esc_html__( 'Left -> Right', 'motionui-addons-for-elementor' ),
					'rtl' => esc_html__( 'Right -> Left', 'motionui-addons-for-elementor' ),
					'btt' => esc_html__( 'Bottom -> Top', 'motionui-addons-for-elementor' ),
					'ttb' => esc_html__( 'Top -> Bottom', 'motionui-addons-for-elementor' ),
				],
				'condition' => [
					'muia_text_ani!' => ['', 'scramble'],
				],
			]
		);
		$element->add_control(
			'muia_text_ani_by',
			[
				'label' => esc_html__( 'Animate By', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'words',
				'frontend_available' => true,  
				'options' => [
					'lines' => esc_html__( 'Lines', 'motionui-addons-for-elementor' ),
					'words' => esc_html__( 'Words', 'motionui-addons-for-elementor' ),
					'chars' => esc_html__( 'Characters', 'motionui-addons-for-elementor' ),
				],
				'condition' => [
					'muia_text_ani!' => ['', 'scramble'],
				],
			]
		);
		Motion::add_motion_settings_controls($element, array(  
			'prefix'=>'text',
			'with_scroll'=> true,
			'stagger'=> true,
			'condition'=>[
				'muia_text_ani!' => ['', 'scramble'],
			]
		)); 
        $element->end_controls_section();
    }
}