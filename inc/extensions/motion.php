<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

class Motion{
    public static function register_controls($element){
        $element->start_controls_section(
            'mui_addons_motion_effects',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Element Motion', 'motionui-addons')),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$element->add_control(
			'muia_motion_effects_name',
			[
				'label' => esc_html__( 'Animation', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
                'frontend_available' => true,  
				'options' => [
					''  => esc_html__( 'None', 'textdomain' ),
					'fade-in-up' => esc_html__( 'Fade In Up', 'textdomain' ),
					'fade-in-down' => esc_html__( 'Fade In Down', 'textdomain' ),
					'fade-in-left' => esc_html__( 'Fade In Left', 'textdomain' ),
					'fade-in-right' => esc_html__( 'Fade In Right', 'textdomain' ),
					'slide-in-up' => esc_html__( 'Slide In Up', 'textdomain' ),
					'slide-in-down' => esc_html__( 'Slide In Down', 'textdomain' ),
					'slide-in-left' => esc_html__( 'Slide In Left', 'textdomain' ),
					'slide-in-right' => esc_html__( 'Slide In Right', 'textdomain' ),
				],
				'default' => '',
                'prefix_class' => 'has-muia-motion-effect muia-',
			]
		);
        $element->add_control( 
            'muia_form_start_motion',
            [
                'label' => esc_html__( 'Transform From', 'themeic' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px','vw', 'vh', 'rem', '%', 'custom'],
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 500,
                        'step' => 1,
                    ]
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 100,
                ],
                'frontend_available' => true,
                'condition' => [
                    'muia_motion_effects_name' => ['fade-in-up','fade-in-down','fade-in-left','fade-in-right', 'slide-in-up','slide-in-down','slide-in-left','slide-in-right'],
                ], 
            ]
        );
        $element->end_controls_section();
    }
	public static function add_motion_settings_controls($element, $prefix = ''){  
		$element->add_control( 
			$prefix . 'mui_motion_duration',
			[
				'label' => esc_html__( 'Duration (optional)', 'motionui-addon' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => .1,
					]
				],
				'frontend_available' => true,
			]
		);
		$element->add_control( 
			$prefix . 'mui_motion_stagger',
			[
				'label' => esc_html__( 'stagger (optional)', 'themeic' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => .01,
					]
				],
				'frontend_available' => true,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				], 
			]
		);
		$element->add_control(
			$prefix . 'mui_motion_ease',
			[
				'label'   => __('Easing (optional)', 'motionui-addon'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'expo.out',
				'options' => [
					// Expo
					'expo.out'   => __('Expo Out', 'motionui-addon'),
					'expo.in'    => __('Expo In', 'motionui-addon'),
					'expo.inOut' => __('Expo InOut', 'motionui-addon'),

					// Power
					'power1.out'   => __('Power1 Out', 'motionui-addon'),
					'power1.in'    => __('Power1 In', 'motionui-addon'),
					'power1.inOut' => __('Power1 InOut', 'motionui-addon'),

					'power2.out'   => __('Power2 Out', 'motionui-addon'),
					'power2.in'    => __('Power2 In', 'motionui-addon'),
					'power2.inOut' => __('Power2 InOut', 'motionui-addon'),

					'power3.out'   => __('Power3 Out', 'motionui-addon'),
					'power3.in'    => __('Power3 In', 'motionui-addon'),
					'power3.inOut' => __('Power3 InOut', 'motionui-addon'),

					'power4.out'   => __('Power4 Out', 'motionui-addon'),
					'power4.in'    => __('Power4 In', 'motionui-addon'),
					'power4.inOut' => __('Power4 InOut', 'motionui-addon'),

					// Back
					'back.out(1.7)'   => __('Back Out', 'motionui-addon'),
					'back.in(1.7)'    => __('Back In', 'motionui-addon'),
					'back.inOut(1.7)' => __('Back InOut', 'motionui-addon'),

					// Elastic
					'elastic.out(1, 0.3)' => __('Elastic Out', 'motionui-addon'),
					'elastic.in(1, 0.3)'  => __('Elastic In', 'motionui-addon'),

					// Bounce
					'bounce.out' => __('Bounce Out', 'motionui-addon'),
					'bounce.in'  => __('Bounce In'),

					// Linear
					'none' => __('Linear (None)', 'motionui-addon'),
				],
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'default'      => '',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			$prefix . 'mui_motion_trigger_class_name',
			[
				'label' => esc_html__( 'Trigger Class Name', 'motionui-addons' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__(
					'Optional. Enter a CSS class name to use another element as the scroll trigger. If left empty, the current widget element will be used as the trigger.',
					'motionui-addons'
				),
				'placeholder' => esc_html__( 'optional-example-trigger', 'motionui-addons' ),
				'frontend_available' => true,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		); 
		$element->add_control(
			$prefix . 'mui_motion_child_element_selector',
			[
				'label' => esc_html__( 'Child Element Selector', 'motionui-addons' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__(
					'Optional. Enter a CSS selector to target a child element as animation. If left empty, the current widget element will be used as animation.',
					'motionui-addons'
				),
				'placeholder' => esc_html__( '.example-child', 'motionui-addons' ),
				'frontend_available' => true,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		); 
		$element->add_control(
			$prefix . 'mui_motion_with_scroll',
			[
				'label' => esc_html__( 'Animate with scroll', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'textdomain' ),
				'label_off' => esc_html__( 'No', 'textdomain' ),
				'return_value' => 'yes',
				'default' => 'no',
				'frontend_available' => true,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],  
			]
		);
	}
}