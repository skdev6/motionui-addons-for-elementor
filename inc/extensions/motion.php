<?php

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Motion animation extension for Elementor.
 *
 * Registers motion/animation controls on Elementor elements
 * and widgets for use with GSAP-powered frontend animations.
 *
 * @package Themeic\MotionUI_Addons\Inc\Extensions
 */
class Motion {

	/**
	 * Register the main Element Motion controls section.
	 *
	 * @param \Elementor\Element_Base $element The Elementor element instance.
	 */
	public static function register_controls( $element ) {
		$element->start_controls_section(
			'mui_addons_motion_effects',
			array(
				'label' => sprintf(
					'<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>',
					esc_html__( 'Element Motion', 'motionui-addons-for-elementor' )
				),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);
		$element->add_control(
			'muia_motion_effects_name',
			array(
				'label'              => esc_html__( 'Animation', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT2,
				'label_block'        => true,
				'frontend_available' => true,
				'options'            => array(
					''               => esc_html__( 'None', 'motionui-addons-for-elementor' ),
					'fade'     => esc_html__( 'Fade', 'motionui-addons-for-elementor' ),
					'slide' => esc_html__( 'Slide', 'motionui-addons-for-elementor' ),
				),
				'default'            => '',
				'prefix_class'       => 'has-muia-motion-effect muia-',
			)
		);
		$element->add_control(
			'muia_motion_direction',
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
					'muia_motion_effects_name!' => '',
				],
			]
		);
		$slide_conditions = array(
			'muia_motion_effects_name' => array(
				'fade',
				'slide',
			),
		);

		$element->add_control(
			'muia_form_start_motion',
			array(
				'label'              => esc_html__( 'From', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', 'vw', 'vh', 'rem', '%', 'custom' ),
				'range'              => array(
					'px' => array(
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 100,
				),
				'frontend_available' => true,
				'condition'          => $slide_conditions,
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Register motion animation controls for an Elementor element.
	 *
	 * @param \Elementor\Element_Base $element The Elementor element instance.
	 * @param array                   $args {
	 *     Optional. Configuration arguments.
	 *
	 *     @type string $prefix      Control key prefix. Default ''.
	 *     @type array  $condition   Elementor condition array. Default ['mui_scroll_ani_enable' => 'yes'].
	 *     @type bool   $stagger     Whether to show the stagger control. Default false.
	 *     @type bool   $ani_class   Whether to show trigger/child selector controls. Default false.
	 *     @type bool   $with_scroll Whether to show the animate-with-scroll switcher. Default false.
	 *     @type string $separator   Separator before first control. Default 'before'.
	 * }
	 */
	public static function add_motion_settings_controls( $element, array $args = array() ) {

		$defaults = array(
			'prefix'             => '',
			'condition'          => array( 'mui_scroll_ani_enable' => 'yes' ),
			'stagger'            => false,
			'stagger_condition'  => array(), 
			'ani_class'          => false,
			'with_scroll'        => false,
			'separator'          => 'before',
		);

		$args      = wp_parse_args( $args, $defaults );
		$prefix    = sanitize_key( $args['prefix'] );
		$condition = $args['condition'];
		$stagger_condition = ! empty( $args['stagger_condition'] ) ? array_merge( $condition, $args['stagger_condition'] ) : $condition;

		// Duration.
		$element->add_control(
			$prefix . 'mui_motion_duration',
			array(
				'label'              => esc_html__( 'Duration', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'condition'          => $condition,
				'size_units'         => array( 'px' ),
				'separator'          => $args['separator'],
				'range'              => array( 
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'frontend_available' => true,
			)
		);

		// Delay.
		$element->add_control(
			$prefix . 'mui_motion_delay',
			array(
				'label'              => esc_html__( 'Delay', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'condition'          => $condition,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.1,
					),
				),
				'frontend_available' => true,
			)
		);

		// Easing.
		$element->add_control(
			$prefix . 'mui_motion_ease',
			array(
				'label'              => esc_html__( 'Easing', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT2,
				'condition'          => $condition,
				'default'            => 'expo.out',
				'options'            => self::get_ease_options(),
				'frontend_available' => true,
			)
		);
		// Stagger (optional).
		if ( $args['stagger'] ) {
			$element->add_control(
				$prefix . 'mui_motion_stagger',
				array(
					'label'              => esc_html__( 'Stagger', 'motionui-addons-for-elementor' ),
					'type'               => Controls_Manager::SLIDER,
					'condition'          => $stagger_condition,
					'size_units'         => array( 'px' ),
					'range'              => array(
						'px' => array(
							'min'  => 0,
							'max'  => 2,
							'step' => 0.001,
						),
					),
					'frontend_available' => true,
				)
			);
		}
		// Animate with scroll (optional).
		if ( $args['with_scroll'] ) {
			$element->add_control(
				$prefix . 'mui_motion_with_scroll',
				array(
					'label'              => esc_html__( 'Animate With Scroll', 'motionui-addons-for-elementor' ),
					'type'               => Controls_Manager::SWITCHER,
					'label_on'           => esc_html__( 'Yes', 'motionui-addons-for-elementor' ),
					'label_off'          => esc_html__( 'No', 'motionui-addons-for-elementor' ),
					'return_value'       => 'yes',
					'default'            => 'no',
					'frontend_available' => true,
					'condition'          => $condition,
				)
			);
		}

		// Trigger class name and child selector (optional).
		if ( $args['ani_class'] ) {
			$element->add_control(
				$prefix . 'mui_motion_trigger_class_name',
				array(
					'label'              => esc_html__( 'Trigger Class Name', 'motionui-addons-for-elementor' ),
					'type'               => Controls_Manager::TEXT,
					'description'        => esc_html__( 'Optional. Enter a CSS class name to use another element as the scroll trigger. If left empty, the current widget element will be used as the trigger.', 'motionui-addons-for-elementor' ),
					'placeholder'        => esc_html__( 'optional-example-trigger', 'motionui-addons-for-elementor' ),
					'sanitize_callback'  => 'sanitize_html_class',
					'frontend_available' => true,
					'condition'          => $condition,
					'separator'          => 'before',
				)
			);

			$element->add_control(
				$prefix . 'mui_motion_child_element_selector',
				array(
					'label'              => esc_html__( 'Animating Child Class Name', 'motionui-addons-for-elementor' ),
					'type'               => Controls_Manager::TEXT,
					'description'        => esc_html__( 'Optional. Enter a CSS selector to target a child element for animation. If left empty, the current widget element will be used.', 'motionui-addons-for-elementor' ),
					'placeholder'        => esc_html__( '.example-child', 'motionui-addons-for-elementor' ),
					'sanitize_callback'  => 'sanitize_text_field',
					'frontend_available' => true,
					'condition'          => $condition,
				)
			);
		}
	}

	/**
	 * Returns the list of GSAP easing options.
	 *
	 * @return array<string, string>
	 */
	private static function get_ease_options() {
		return array(
			// Expo.
			'expo.out'   => esc_html__( 'Expo Out', 'motionui-addons-for-elementor' ),
			'expo.in'    => esc_html__( 'Expo In', 'motionui-addons-for-elementor' ),
			'expo.inOut' => esc_html__( 'Expo In Out', 'motionui-addons-for-elementor' ),

			// Power 1.
			'power1.out'   => esc_html__( 'Power1 Out', 'motionui-addons-for-elementor' ),
			'power1.in'    => esc_html__( 'Power1 In', 'motionui-addons-for-elementor' ),
			'power1.inOut' => esc_html__( 'Power1 In Out', 'motionui-addons-for-elementor' ),

			// Power 2.
			'power2.out'   => esc_html__( 'Power2 Out', 'motionui-addons-for-elementor' ),
			'power2.in'    => esc_html__( 'Power2 In', 'motionui-addons-for-elementor' ),
			'power2.inOut' => esc_html__( 'Power2 In Out', 'motionui-addons-for-elementor' ),

			// Power 3.
			'power3.out'   => esc_html__( 'Power3 Out', 'motionui-addons-for-elementor' ),
			'power3.in'    => esc_html__( 'Power3 In', 'motionui-addons-for-elementor' ),
			'power3.inOut' => esc_html__( 'Power3 In Out', 'motionui-addons-for-elementor' ),

			// Power 4.
			'power4.out'   => esc_html__( 'Power4 Out', 'motionui-addons-for-elementor' ),
			'power4.in'    => esc_html__( 'Power4 In', 'motionui-addons-for-elementor' ),
			'power4.inOut' => esc_html__( 'Power4 In Out', 'motionui-addons-for-elementor' ),

			// Back.
			'back.out(1.7)'   => esc_html__( 'Back Out', 'motionui-addons-for-elementor' ),
			'back.in(1.7)'    => esc_html__( 'Back In', 'motionui-addons-for-elementor' ),
			'back.inOut(1.7)' => esc_html__( 'Back In Out', 'motionui-addons-for-elementor' ),

			// Elastic.
			'elastic.out(1, 0.3)' => esc_html__( 'Elastic Out', 'motionui-addons-for-elementor' ),
			'elastic.in(1, 0.3)'  => esc_html__( 'Elastic In', 'motionui-addons-for-elementor' ),

			// Bounce.
			'bounce.out' => esc_html__( 'Bounce Out', 'motionui-addons-for-elementor' ),
			'bounce.in'  => esc_html__( 'Bounce In', 'motionui-addons-for-elementor' ),

			// Linear.
			'none' => esc_html__( 'Linear', 'motionui-addons-for-elementor' ),
		);
	}
}