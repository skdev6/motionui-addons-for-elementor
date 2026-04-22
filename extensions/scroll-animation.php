<?php 

namespace Themeic\MotionUI_Addons\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

class Scroll_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'mui_addons_scroll_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap">%s <i class="themeic-muia-logo"></i></div>', __('Scroll Animation', 'motionui-addons')),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$element->add_control(
			'mui_scroll_ani_enable',
			[
				'label' => __( 'Enable', 'motionui-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'mui-scroll-',
			]
		);

		$element->start_controls_tabs('mui_scroll_ani_tabs');

		$element->start_controls_tab(
			'mui_scroll_ani_from',
			[
				'label' => __( 'From', 'motionui-addon' )
			]
		);

		$element->add_control(
			'mui_scroll_ani_translate_toggle',
			[
				'label' => __( 'Translate', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes'
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_translate_x',
			[
				'label' => __( 'Translate X', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-x: {{SIZE}}px;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_translate_y',
			[
				'label' => __( 'Translate Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-y: {{SIZE}}px;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_toggle',
			[
				'label' => __( 'Rotation', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
			]
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_mode',
			[
				'label' => __( 'Mode', 'motionui-addon' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'motionui-addon' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'motionui-addon' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'mui_scroll_ani_rotate_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_x',
			[
				'label' => __( 'Rotation X', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Rotation Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Rotation (Z)', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Scale', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_scale_mode',
			[
				'label' => __( 'Mode', 'motionui-addon' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'motionui-addon' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'motionui-addon' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'mui_scroll_ani_scale_hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_scale_x',
			[
				'label' => __( 'Scale (X)', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Scale Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Skew', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_skew_x',
			[
				'label' => __( 'Skew X', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Skew Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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

		$element->end_controls_tab();

		$element->start_controls_tab(
            'mui_scroll_ani_to',
            [
				'label' => __( 'To', 'motionui-addon' ),
            ]
		);

		$element->add_control(
			'mui_scroll_ani_translate_toggle_hover',
			[
				'label' => __( 'Translate', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_translate_x_hover',
			[
				'label' => __( 'Translate X', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-x-to: {{SIZE}}px;'
				],
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_translate_y_hover',
			[
				'label' => __( 'Translate Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-translate-y-to: {{SIZE}}px;'
				],
			]
		);

		$element->end_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_toggle_hover',
			[
				'label' => __( 'Rotation', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
			]
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_mode_hover',
			[
				'label' => __( 'Mode', 'motionui-addon' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'motionui-addon' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'motionui-addon' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'mui_scroll_ani_rotate_hr_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_rotate_x_hover',
			[
				'label' => __( 'Rotation X', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Rotation Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Rotation (Z)', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Scale', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_scale_mode_hover',
			[
				'label' => __( 'Mode', 'motionui-addon' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'compact' => [
						'title' => __( 'Compact', 'motionui-addon' ),
						'icon' => 'eicon-plus-circle',
					],
					'loose' => [
						'title' => __( 'Loose', 'motionui-addon' ),
						'icon' => 'eicon-minus-circle',
					],
				],
				'default' => 'loose',
				'toggle' => false
			]
		);

		$element->add_control(
			'mui_scroll_ani_scale_hr_hover',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$element->add_responsive_control(
			'mui_scroll_ani_scale_x_hover',
			[
				'label' => __( 'Scale (X)', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Scale Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Skew', 'motionui-addon' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_ani_skew_x_hover',
			[
				'label' => __( 'Skew X', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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
				'label' => __( 'Skew Y', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
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

		$element->add_control(
			'mui_scroll_ani_transition_duration',
			[
				'label' => __( 'Transition Duration', 'motionui-addon' ),
				'type' => Controls_Manager::SLIDER,
				'separator' => 'before',
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => .1,
					]
				],
				'selectors' => [
					'{{WRAPPER}}' => '--mui-scroll-transition-duration: {{SIZE}}s;'
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

        $element->end_controls_section();
    }
}