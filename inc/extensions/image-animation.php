<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Image_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'muia_addons_text_animation',
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
					'image-reveal' => esc_html__( 'Image Reveal', 'motionui-addons-for-elementor' ),
					'corner-reveal' => esc_html__( 'Corner Reveal', 'motionui-addons-for-elementor' ),
					'zoom' => esc_html__( 'Zoom', 'motionui-addons-for-elementor' ),
					'image-prallax' => esc_html__( 'Image Prallax', 'motionui-addons-for-elementor' ),
				],
			]
		);
		// The shared picker from motion.php, under the muia_ani_direction name
		// the image script reads.
		Motion::get_derection_control( $element, array(
			'name'      => 'img',
			'condition' => array(
				'muia_img_ani_type!' => '',
			),
			'default'   => 'left',
		) );
		$element->add_control(  
			'muia_ani_image_space_from',
			[
				'label' => esc_html__( 'From', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--image-animation-space-form: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'muia_img_ani_type' => ['image-prallax'],
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
		if(!muia_has_pro()){     
			$element->add_control(
				'muia_pro_image_effect_notice',
				array(
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw'  => muia_get_pronotice_html(),
				)
			);
		}
        $element->end_controls_section();
    }
}