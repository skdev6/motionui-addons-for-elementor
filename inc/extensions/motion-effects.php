<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;
use Themeic\MotionUI_Addons\Inc\Classes\Motionui;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Motion_Effects{ 
	public static function register_controls($element){
		$element->start_controls_section(
			'muia_addons_scroll_animation',
			array(
				'label' => sprintf(
					'<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>',
					__( 'MotionUI Effects', 'motionui-addons-for-elementor' )
				),
				'tab' => Controls_Manager::TAB_ADVANCED,
			)
		);
		if(Motionui::is_active_pro()){
			self::get_controls($element);
		}else{
			$element->add_control(
				'muia_pro_motion_effect_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => muia_get_pronotice_html(),
				)
			);
		}
		$element->end_controls_section();
	}
	public static function get_controls($element){
		self::custom_motion_controls($element);
		self::_get_motions_controls($element, [
			'condition' => array(
				'muia_custom_ani_enable!' => 'yes',
			),
		]);
		$element->add_control(
			'muia_animate_for_child',
			array(
				'label'        => __( 'Animate for Child', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true,
				'render_type'        => 'template', 
				'conditions'         => array(   
					'relation' => 'or',   
					'terms'    => array(
						array(
							'name'     => 'muia_custom_ani_enable',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'name'     => 'muia_motion_effects_name',
							'operator' => '!==',
							'value'    => '',
						),
					),
				),
			)
		);
		$element->add_control(
			'muia_child_selector',
			array(
				'label'              => esc_html__( 'Child Class Name', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::TEXT,
				'description'        => esc_html__( 'Optional. Enter a CSS selector to target a child element for animation. If left empty, the current widget element will be used.', 'motionui-addons-for-elementor' ),
				'placeholder'        => esc_html__( 'class-name, class-2', 'motionui-addons-for-elementor' ),
				'sanitize_callback'  => 'sanitize_text_field',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => [
					'muia_animate_for_child' => 'yes',
				],
			)
		);
		Motion::add_motion_settings_controls($element, [ 
			'with_scroll'=>true,
			'prefix' => 'effect_',
			'stagger' => true,
			'stagger_condition' => array(
				'muia_child_selector!' => '',
			),
		]);
	}
    public static function custom_motion_controls($element){

		$element->add_control(
			'muia_custom_ani_enable',
			array(
				'label'        => __( 'Custom Animation', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'visibility__hidden mui-custom-ani-',
				'frontend_available' => true,   
			)
		);

		$element->start_controls_tabs( 'muia_scroll_ani_tabs' );

		// ── FROM TAB ────────────────────────────────────────────────────────────────

		$element->start_controls_tab(
			'muia_scroll_ani_from',
			array(
				'label'     => __( 'From', 'motionui-addons-for-elementor' ),
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		// Translate.
		$element->add_control(
			'muia_scroll_ani_translate_toggle',
			array(
				'label'        => __( 'Translate', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,  
				'return_value' => 'yes',
				'condition'    => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'muia_scroll_x',
			array(
				'label'      => __( 'Translate X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_y',
			array(
				'label'      => __( 'Translate Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Rotation.
		$element->add_control(
			'muia_scroll_ani_rotate_toggle',
			array(
				'label'     => __( 'Rotation', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,
				'return_value' => 'yes',
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'muia_scroll_ani_rotate_hr',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_rotate_x',
			array(
				'label'      => __( 'Rotation X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-rotate-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_rotate_y',
			array(
				'label'      => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-rotate-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_rotate_z',
			array(
				'label'      => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-rotate-z: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Scale.
		$element->add_control(
			'muia_scroll_ani_scale_toggle',
			array(
				'label'        => __( 'Scale', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,  
				'return_value' => 'yes',
				'condition'    => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'muia_scroll_ani_scale_hr',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_scale_x',
			array(
				'label'      => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-scale-x: {{SIZE}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_scale_y',
			array(
				'label'      => __( 'Scale Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-scale-y: {{SIZE}};',
				),
			)
		);

		$element->end_popover();

		// Skew.
		$element->add_control(
			'muia_scroll_ani_skew_toggle',
			array(
				'label'        => __( 'Skew', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,  
				'return_value' => 'yes',
				'condition'    => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'muia_scroll_skew_x',
			array(
				'label'      => __( 'Skew X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-skew-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_skew_y',
			array(
				'label'      => __( 'Skew Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-skew-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Opacity.
		$element->add_responsive_control(
			'muia_scroll_opacity',
			array(
				'label'      => esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ),
				),
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-opacity: {{SIZE}};',
				),
			)
		);

		$element->end_controls_tab();

		// ── TO TAB ──────────────────────────────────────────────────────────────────

		$element->start_controls_tab(
			'muia_scroll_ani_to',
			array(
				'label'     => __( 'To', 'motionui-addons-for-elementor' ),
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		// Translate.
		$element->add_control(
			'muia_scroll_ani_translate_toggle_to',
			array(
				'label'        => __( 'Translate', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,  
				'return_value' => 'yes',
				'condition'    => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'muia_scroll_x_to',
			array(
				'label'      => __( 'Translate X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-x-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_y_to',
			array(
				'label'      => __( 'Translate Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'range'      => array(
					'px' => array( 'min' => -1000, 'max' => 1000 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-y-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Rotation.
		$element->add_control(
			'muia_scroll_ani_rotate_toggle_to',
			array(
				'label'     => __( 'Rotation', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'frontend_available' => true,
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'muia_scroll_ani_rotate_hr_to',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_rotate_x_to',
			array(
				'label'      => __( 'Rotation X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-rotate-x-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_rotate_y_to',
			array(
				'label'      => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-rotate-y-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_rotate_z_to',
			array(
				'label'      => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-rotate-z-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Scale.
		$element->add_control(
			'muia_scroll_ani_scale_toggle_to',
			array(
				'label'        => __( 'Scale', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,  
				'return_value' => 'yes',
				'condition'    => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_control(
			'muia_scroll_ani_scale_hr_to',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_scale_x_to',
			array(
				'label'      => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-scale-x-to: {{SIZE}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_scale_y_to',
			array(
				'label'      => __( 'Scale Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 1 ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-scale-y-to: {{SIZE}};',
				),
			)
		);

		$element->end_popover();

		// Skew.
		$element->add_control(
			'muia_scroll_ani_skew_toggle_to',
			array(
				'label'        => __( 'Skew', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,  
				'return_value' => 'yes',
				'condition'    => array( 'muia_custom_ani_enable' => 'yes' ),
			)
		);

		$element->start_popover();

		$element->add_responsive_control(
			'muia_scroll_skew_x_to',
			array(
				'label'      => __( 'Skew X', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-skew-x-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			'muia_scroll_skew_y_to',
			array(
				'label'      => __( 'Skew Y', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'size_units' => array( 'deg' ),
				'range'      => array(
					'deg' => array( 'min' => -180, 'max' => 180 ),
				),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-skew-y-to: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->end_popover();

		// Opacity.
		$element->add_responsive_control(
			'muia_scroll_opacity_to',
			array(
				'label'      => esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ),
				),
				'condition'  => array( 'muia_custom_ani_enable' => 'yes' ),
				'selectors'  => array(
					'{{WRAPPER}}' => '--muia-opacity-to: {{SIZE}};',
				),
			)
		);

		$element->end_controls_tab();
		$element->end_controls_tabs();        
    }
	public static function _get_motions_controls($element, array $args = array()){
		$defaults = array(
			'condition'          => array(),
		);
		$condition = $args['condition'];

		$element->add_control(
			'muia_motion_effects_name',
			array(
				'label'              => esc_html__( 'Animation', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT2,
				'frontend_available' => true,
				'render_type'        => 'template',
				'options' => array(
					''        => esc_html__( 'None',     'motionui-addons-for-elementor' ),
					'fade'    => esc_html__( 'Fade',     'motionui-addons-for-elementor' ),
					'slide'   => esc_html__( 'Slide',    'motionui-addons-for-elementor' ),
					'zoom'    => esc_html__( 'Zoom',     'motionui-addons-for-elementor' ),
					'zoom_center'    => esc_html__( 'Zoom Center', 'motionui-addons-for-elementor' ),
					'flip'    => esc_html__( 'Flip',     'motionui-addons-for-elementor' ),
					'rotate'  => esc_html__( 'Rotate',   'motionui-addons-for-elementor' ),
					'skew'    => esc_html__( 'Skew',     'motionui-addons-for-elementor' ),
					'bounce'  => esc_html__( 'Bounce',   'motionui-addons-for-elementor' ),
					'elastic' => esc_html__( 'Elastic',  'motionui-addons-for-elementor' ),
					'blur'    => esc_html__( 'Blur',     'motionui-addons-for-elementor' ),
					'clip'    => esc_html__( 'Clip',     'motionui-addons-for-elementor' ),
				),
				'default'            => '',
				'prefix_class'       => 'has-muia-motion-effect visibility__hidden muia-motion-effect-',
				'condition'          => $condition,
			)
		);
		$element->add_responsive_control(
			'muia_ani_from',
			array(
				'label'      => __( 'From', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'frontend_available' => true,
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 1000 ),
				),
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'condition'=>[
					'muia_motion_effects_name'=>['fade', 'slide'],
					'muia_custom_ani_enable!' => 'yes',
				]
			)
		);
		Motion::get_derection_control('muia_motion_direction',
			$element, 
			array_merge(
				$condition,
				[
					'muia_motion_effects_name!' => ['', 'zoom-center'],
				]
			)
		);
	}
}  