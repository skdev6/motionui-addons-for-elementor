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
					'reveal'     => esc_html__( 'Reveal', 'motionui-addons-for-elementor' ),
					'wave'       => muia_has_pro() ? esc_html__( 'Wave', 'motionui-addons-for-elementor' ) : esc_html__( 'Wave (Pro ✦)', 'motionui-addons-for-elementor' ),
					'scramble'   => muia_has_pro() ? esc_html__( 'Scramble', 'motionui-addons-for-elementor' ) : esc_html__( 'Scramble (Pro ✦)', 'motionui-addons-for-elementor' ),
					'text-auto-scroll'   => muia_has_pro() ? esc_html__( 'Auto Scroll', 'motionui-addons-for-elementor' ) : esc_html__( 'Auto Scroll (Pro ✦)', 'motionui-addons-for-elementor' ),
				),
			)
		);
		if(muia_has_pro()){
			Motion::get_derection_control($element, 'muia_text_scroll_direction', ['muia_text_ani' => ['text-auto-scroll']], ['btt', 'ttb']);  
			
			Motion::get_derection_control($element, 'muia_text_direction', ['muia_text_ani!' => ['', 'scramble', 'text-auto-scroll']]);  
		}
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
					'muia_text_ani!' => ['', 'scramble', 'text-auto-scroll'],
				],
			]
		);
		if(muia_has_pro()){
			// $element->add_control(
			// 	'muia_text_animate_for_child',
			// 	array(
			// 		'label'        => __( 'Animate for Child', 'motionui-addons-for-elementor' ),
			// 		'type'         => \Elementor\Controls_Manager::SWITCHER,
			// 		'return_value' => 'yes',
			// 		'frontend_available' => true,
			// 		'render_type'        => 'template', 
			// 		'condition'         => array(   
			// 			'muia_text_ani!' => ['', 'scramble'],
			// 		),
			// 	)
			// );
			// $element->add_control(
			// 	'muia_text_animate_selector',
			// 	array(
			// 		'label'              => esc_html__( 'Child Class Name', 'motionui-addons-for-elementor' ),
			// 		'type'               => \Elementor\Controls_Manager::TEXT,
			// 		'description'        => esc_html__( 'Optional. Enter a CSS selector to target a child element for animation. If left empty, the current widget element will be used.', 'motionui-addons-for-elementor' ),
			// 		'placeholder'        => esc_html__( 'class-name, class-2', 'motionui-addons-for-elementor' ),
			// 		'sanitize_callback'  => 'sanitize_text_field',
			// 		'frontend_available' => true,
			// 		'render_type'        => 'template',
			// 		'condition'          => [
			// 			'muia_text_ani!' => ['', 'scramble'],
			// 			'muia_text_animate_for_child' => 'yes',  
			// 		],
			// 	)
			// );
		}
		Motion::add_motion_settings_controls($element, array(    
			'prefix'=>'text',
			'with_scroll'=> true,
			'stagger'=> true,
			'stagger_condition'=>[
				'muia_text_ani!' => ['scramble'],
			],
			'delay_condition'=>[
				'muia_text_ani!' => ['scramble'],
			],
			'duration_condition'=>[
				'muia_text_ani!' => ['scramble'],
			],
			'ease_condition'=>[
				'muia_text_ani!' => ['scramble'],
			],
			'condition'=>[
				'muia_text_ani!' => [''],
			]
		)); 
		if(!muia_has_pro()){     
			$element->add_control(
				'muia_pro_text_effect_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => muia_get_pronotice_html(),
				)
			);
		}
        $element->end_controls_section();
    }
}