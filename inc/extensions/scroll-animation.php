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
			array(
				'label' => sprintf(
					'<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>',
					__( 'Scroll Animation', 'motionui-addons-for-elementor' )
				),
				'tab' => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'mui_scroll_ani_enable',
			array(
				'label'        => __( 'Enable', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'mui-scroll-ani-',
			)
		);

		$element->start_controls_tabs( 'mui_scroll_ani_tabs' );

		// ── FROM TAB ────────────────────────────────────────────────────────────────

		$element->start_controls_tab(
			'mui_scroll_ani_from',
			array(
				'label'     => __( 'From', 'motionui-addons-for-elementor' ),
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		// Translate.
		$element->add_control(
			'mui_scroll_ani_translate_toggle',
			array(
				'label'        => __( 'Translate', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'muia_scroll_x',
			array(
				'label'      => __( 'Translate X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_y',
			array(
				'label'      => __( 'Translate Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Rotation.
		$element->add_control(
			'mui_scroll_ani_rotate_toggle',
			array(
				'label'     => __( 'Rotation', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_hr',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_rotate_x',
			array(
				'label'      => __( 'Rotation X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-rotate-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_rotate_y',
			array(
				'label'      => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-rotate-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_rotate_z',
			array(
				'label'      => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-rotate-z: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Scale.
		$element->add_control(
			'mui_scroll_ani_scale_toggle',
			array(
				'label'        => __( 'Scale', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_scale_hr',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_scale_x',
			array(
				'label'      => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-scale-x: {{SIZE}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_scale_y',
			array(
				'label'      => __( 'Scale Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-scale-y: {{SIZE}};',
				),
			)
		);

		$element->end_popover();

		// Skew.
		$element->add_control(
			'mui_scroll_ani_skew_toggle',
			array(
				'label'        => __( 'Skew', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_skew_x',
			array(
				'label'      => __( 'Skew X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-skew-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_skew_y',
			array(
				'label'      => __( 'Skew Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-skew-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Opacity.
		$element->add_responsive_control(
			'mui_scroll_opacity',
			array(
				'label'      => esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ),
				),
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-opacity: {{SIZE}};',
				),
			)
		);

		$element->end_controls_tab();

		// ── TO TAB ──────────────────────────────────────────────────────────────────

		$element->start_controls_tab(
			'mui_scroll_ani_to',
			array(
				'label'     => __( 'To', 'motionui-addons-for-elementor' ),
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		// Translate.
		$element->add_control(
			'mui_scroll_ani_translate_toggle_to',
			array(
				'label'        => __( 'Translate', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_x_to',
			array(
				'label'      => __( 'Translate X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-x-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_y_to',
			array(
				'label'      => __( 'Translate Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-y-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Rotation.
		$element->add_control(
			'mui_scroll_ani_rotate_toggle_to',
			array(
				'label'     => __( 'Rotation', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_rotate_hr_to',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_rotate_x_to',
			array(
				'label'      => __( 'Rotation X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-rotate-x-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_rotate_y_to',
			array(
				'label'      => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-rotate-y-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_rotate_z_to',
			array(
				'label'      => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-rotate-z-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Scale.
		$element->add_control(
			'mui_scroll_ani_scale_toggle_to',
			array(
				'label'        => __( 'Scale', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'mui_scroll_ani_scale_hr_to',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_scale_x_to',
			array(
				'label'      => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-scale-x-to: {{SIZE}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_scale_y_to',
			array(
				'label'      => __( 'Scale Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-scale-y-to: {{SIZE}};',
				),
			)
		);

		$element->end_popover();

		// Skew.
		$element->add_control(
			'mui_scroll_ani_skew_toggle_to',
			array(
				'label'        => __( 'Skew', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => array( 'mui_scroll_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'mui_scroll_skew_x_to',
			array(
				'label'      => __( 'Skew X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-skew-x-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'mui_scroll_skew_y_to',
			array(
				'label'      => __( 'Skew Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-skew-y-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Opacity.
		$element->add_responsive_control(
			'mui_scroll_opacity_to',
			array(
				'label'      => esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ),
				),
				'condition'  => array( 'mui_scroll_ani_enable' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--mui-opacity-to: {{SIZE}};',
				),
			)
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();
		Motion::add_motion_settings_controls($element, [
			'with_scroll'=>true
		]);
        $element->end_controls_section();
    }
}  