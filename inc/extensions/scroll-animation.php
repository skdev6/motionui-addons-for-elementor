<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Scroll_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'mui_addons_scroll_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Scroll Animation', 'motionui-addons-for-elementor')),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$element->add_control(
			'mui_scroll_ani_enable',
			[
				'label' => __( 'Enable', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'mui-scroll-ani-',
			]
		);

		$element->start_controls_tabs('mui_scroll_ani_tabs');

		$element->start_controls_tab(
			'mui_scroll_ani_from',
			[
				'label' => __( 'From', 'motionui-addons-for-elementor' ),
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->add_control(
			'mui_scroll_ani_translate_toggle',
			[
				'label' => __( 'Translate', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_translate_x',
			[
				'label' => __( 'Translate X', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px', '%', 'vw', 'vh', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-x: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_translate_y',
			[
				'label' => __( 'Translate Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px', '%', 'vw', 'vh', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-y: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_toggle',
			[
				'label' => __( 'Rotation', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();


		$element->add_control(
			'mui_scroll_ani_rotate_hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_x',
			[
				'label' => __( 'Rotation X', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-rotate-x: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_y',
			[
				'label' => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-rotate-y: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_z',
			[
				'label' => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-rotate-z: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_scale_toggle',
			[
				'label' => __( 'Scale', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();


		$element->add_control(
			'mui_scroll_ani_scale_hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_scale_x',
			[
				'label' => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-scale-x: {{SIZE}}; --mui-scroll-scale-y: {{SIZE}};'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_scale_y',
			[
				'label' => __( 'Scale Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-scale-y: {{SIZE}};'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_skew_toggle',
			[
				'label' => __( 'Skew', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_skew_x',
			[
				'label' => __( 'Skew X', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-skew-x: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_skew_y',
			[
				'label' => __( 'Skew Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-skew-y: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();
		$element->add_responsive_control(
			'mui_scroll_ani_opacity',
			[
				'label' => esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .your-class' => '--mui-scroll-opacity: {{SIZE}};',
				],
			]
		);
		$element->end_controls_tab();

		$element->start_controls_tab(
            'mui_scroll_ani_to',
            [
				'label' => __( 'To', 'motionui-addons-for-elementor' ),
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
            ]
		);

		$element->add_control(
			'mui_scroll_ani_translate_toggle_hover',
			[
				'label' => __( 'Translate', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_translate_x_hover',
			[
				'label' => __( 'Translate X', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px', '%', 'vw', 'vh', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-x-to: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_translate_y_hover',
			[
				'label' => __( 'Translate Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px', '%', 'vw', 'vh', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-y-to: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_toggle_hover',
			[
				'label' => __( 'Rotation', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_hr_hover',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_x_hover',
			[
				'label' => __( 'Rotation X', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-rotate-x-to: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_y_hover',
			[
				'label' => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-rotate-y-to: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_z_hover',
			[
				'label' => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-rotate-z-to: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_scale_toggle_hover',
			[
				'label' => __( 'Scale', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_scale_hr_hover',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_scale_x_hover',
			[
				'label' => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-scale-x-to: {{SIZE}}; --mui-scroll-scale-y-to: {{SIZE}};'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_scale_y_hover',
			[
				'label' => __( 'Scale Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['px'],
				'default' => [
					'size' => 1
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5,
						'step' => .1
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-scale-y-to: {{SIZE}};'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_skew_toggle_hover',
			[
				'label' => __( 'Skew', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_skew_x_hover',
			[
				'label' => __( 'Skew X', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-skew-x-to: {{SIZE}}deg;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_skew_y_hover',
			[
				'label' => __( 'Skew Y', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],
				'size_units' => ['deg'],
				'range' => [
					'px' => [
						'min' => -180,
						'max' => 180,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-skew-y-to: {{SIZE}}deg;'
				],
			]
		);

		$element->end_popover();
		$element->add_responsive_control(
			'mui_scroll_ani_opacity_to',
			[
				'label' => esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.01,
					],
				],
				'condition' => [
					'mui_scroll_ani_enable' => 'yes',
				],     
				'selectors' => [
					'{{WRAPPER}} .your-class' => '--mui-scroll-opacity-to: {{SIZE}};',
				],
			]
		);
		$element->end_controls_tab();

		$element->end_controls_tabs();


		$element->add_control( 
			'mui_scroll_ani_delay',
			[
				'label' => esc_html__( 'Delay (optional)', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'separator' => 'before',
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
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-delay:{{SIZE}}',
				]
			]
		);
		Motion::add_motion_settings_controls($element, 'scroll_');
        $element->end_controls_section();
    }
}