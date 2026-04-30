<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Image_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'mui_addons_text_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Image Animations', 'motionui-addons-for-elementor')),
            ]
        );
		$element->add_control(   
			'muia_img_ani_type',
			[
				'label' => esc_html__( 'Animation', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'frontend_available' => true,  
				'prefix_class' => 'has-muia-img-ani visibility__hidden muia-img-',
				'options' => [
					'' => esc_html__( 'None', 'motionui-addons-for-elementor' ),
					'grid-reveal' => esc_html__( 'Grid Reveal', 'motionui-addons-for-elementor' ),
					'column-reveal' => esc_html__( 'Column Reveal', 'motionui-addons-for-elementor' ),
					'reveal' => esc_html__( 'reveal', 'motionui-addons-for-elementor' ),
				],
			]
		);
		$element->add_control(
			'muia_ani_direction',
			[
				'label' => esc_html__( 'Direction', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'ltr',
				'frontend_available' => true,  
				'options' => [
					'ltr' => esc_html__( 'Left -> Right', 'motionui-addons-for-elementor' ),
					'rtl' => esc_html__( 'Right -> Left', 'motionui-addons-for-elementor' ),
					'btt' => esc_html__( 'Bottom -> Top', 'motionui-addons-for-elementor' ),
					'ttb' => esc_html__( 'Top -> Bottom', 'motionui-addons-for-elementor' ),
				],
				'condition' => [
					'muia_img_ani_type!' => '',
				],
			]
		);
		Motion::add_motion_settings_controls($element, array(
			'prefix'=>'img',
			'with_scroll'=> true,
			'stagger'=> true,
			'stagger_condition'=>[
				'muia_img_ani_type'=>['grid-reveal', 'column-reveal']
			],
			'condition'=>[
				'muia_img_ani_type!' => '',
			]
		)); 
		
        $element->end_controls_section();
    }
}  