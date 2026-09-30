<?php

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Controls_Manager;
use Themeic\MotionUI_Addons\Inc\Classes\Motionui;

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
	 * Register motion animation controls for an Elementor element.
	 *
	 * @param \Elementor\Element_Base $element The Elementor element instance.
	 * @param array                   $args {
	 *     Optional. Configuration arguments.
	 *
	 *     @type string $prefix      Control key prefix. Default ''.
	 *     @type array  $condition   Elementor condition array. Default ['muia_scroll_ani_enable' => 'yes'].
	 *     @type bool   $stagger     Whether to show the stagger control. Default false.
	 *     @type array  $trigger_condition Condition for the trigger point controls. Default $condition.
	 *     @type bool   $ani_class   Whether to show trigger/child selector controls. Default false.
	 *     @type bool   $with_scroll Whether to show the animate-with-scroll switcher. Default false.
	 *     @type string $separator   Separator before first control. Default 'before'.
	 * }
	 */
	public static function add_motion_settings_controls( $element, array $args = array() ) {

		$defaults = array(
			'prefix'             => '',
			'condition'          => array(),
			'stagger'            => false,
			'stagger_condition'  => array(), 
			'delay_condition'  => array(), 
			'duration_condition'  => array(), 
			'ease_condition'  => array(), 
			'trigger_condition'  => array(), 
			'ani_class'          => false,
			'with_scroll'        => false,
			'reverse_ani'        => true,
			'separator'          => 'before',
		);

		$args      = wp_parse_args( $args, $defaults );
		$prefix    = sanitize_key( $args['prefix'] );
		$condition = $args['condition'];
		$stagger_condition = ! empty( $args['stagger_condition'] ) ? array_merge( $condition, $args['stagger_condition'] ) : $condition;
		$duration_condition = ! empty( $args['duration_condition'] ) ? array_merge( $condition, $args['duration_condition'] ) : $condition;
		$delay_condition = ! empty( $args['delay_condition'] ) ? array_merge( $condition, $args['delay_condition'] ) : $condition;
		$ease_condition = ! empty( $args['ease_condition'] ) ? array_merge( $condition, $args['ease_condition'] ) : $condition;
		$trigger_condition = ! empty( $args['trigger_condition'] ) ? array_merge( $condition, $args['trigger_condition'] ) : $condition;
		// Duration.
		$element->add_control(
			$prefix . 'muia_motion_duration',
			array(
				'label'              => esc_html__( 'Duration (s)', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'condition'          => $duration_condition,
				'separator'          => $args['separator'],
				'default'            => 0.7,
				'min'  => 0,
				'max'  => 10,
				'step' => 0.1,
				'frontend_available' => true,
			)
		);

		// Delay.
		$element->add_control(
			$prefix . 'muia_motion_delay',
			array(
				'label'              => esc_html__( 'Delay (s)', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'condition'          => $delay_condition,
				'default'            => 0,
				'min'  => 0,
				'max'  => 10,
				'step' => 0.1,
				'frontend_available' => true,
			)
		);
		// Stagger (optional).
		if ( $args['stagger'] ) {
			$element->add_control(
				$prefix . 'muia_motion_stagger',
				array(
					'label'              => esc_html__( 'Stagger', 'motionui-addons-for-elementor' ),
					'type'               => Controls_Manager::NUMBER,
					'condition'          => $stagger_condition,
					'default'            => 0,
					'min'  => 0,
					'max'  => 0.3,
					'step' => 0.001,
					'frontend_available' => true,
				)
			);
		}
		// Easing.
		$element->add_control( 
			$prefix . 'muia_motion_ease',
			array(
				'label'              => esc_html__( 'Easing', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'condition'          => $ease_condition,
				'default'            => 'power3.out',
				'options'            => self::get_ease_options(),
				'frontend_available' => true,
			)
		);

		// Trigger point. Both halves read "<element edge> <viewport position>",
		// which is what ScrollTrigger's `start` takes, so the value goes
		// straight through without translating. The fixed pairs cover the usual
		// places; Custom opens the field below for anything else.
		$element->add_control(
			$prefix . 'muia_motion_trigger_point',
			array(
				'label'              => esc_html__( 'Trigger Point', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'condition'          => $trigger_condition,
				'default'            => 'top 80%',
				'options'            => self::get_trigger_point_options(),
				'frontend_available' => true,
			)
		);

		$element->add_control(
			$prefix . 'muia_motion_trigger_point_custom',
			array(
				'label'              => esc_html__( 'Custom Trigger Point', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::TEXT,
				'description'        => esc_html__( 'The element edge first, then where it meets the viewport — "top 80%" starts the animation when the top of the element reaches 80% down the screen.', 'motionui-addons-for-elementor' ),
				'placeholder'        => 'top 80%',
				'default'            => 'top 80%',
				'condition'          => array_merge(
					$trigger_condition,
					array( $prefix . 'muia_motion_trigger_point' => 'custom' )
				),
				'frontend_available' => true,
			)
		);
		if(muia_has_pro()){  
			// Animate with scroll (optional).
			if ( $args['with_scroll'] ) {
				$element->add_control(
					$prefix . 'muia_motion_with_scroll',
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
			
		}
		// Trigger class name and child selector (optional).
		if ( $args['ani_class'] ) {
			$element->add_control(
				$prefix . 'muia_motion_trigger_class_name',
				array(
					'label'              => esc_html__( 'Trigger Class Name', 'motionui-addons-for-elementor' ),
					'type'               => Controls_Manager::TEXT,
					'description'        => esc_html__( 'Optional. Enter a CSS class name to use another element as the scroll trigger. If left empty, the current widget element will be used as the trigger.', 'motionui-addons-for-elementor' ),
					'placeholder'        => esc_html__( 'optional-example-trigger', 'motionui-addons-for-elementor' ),
					'sanitize_callback'  => 'sanitize_html_class',
					'frontend_available' => true,
					'condition'          => $condition
				)
			);

			$element->add_control(
				$prefix . 'muia_motion_child_element_selector',
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

		$element->add_control(
			$prefix . 'muia_motion_mobile',
			array(
				'label'              => esc_html__( 'Enable on mobile', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Show', 'motionui-addons-for-elementor' ),
				'label_off'          => esc_html__( 'Hide', 'motionui-addons-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'prefix_class' => 'muia-mobile-',
				'frontend_available' => true,
				'condition'          => $condition
			)
		);
	}

	/**
	 * Returns the list of scroll trigger points.
	 *
	 * Each value is "<element edge> <viewport position>", the form ScrollTrigger's
	 * `start` expects, so it needs no translating on the way through. 'custom'
	 * is the escape hatch and hands over to the text control beside it.
	 *
	 * @return array<string, string>
	 */
	private static function get_trigger_point_options() {
		return array(
			'top 80%'       => esc_html__( 'Default', 'motionui-addons-for-elementor' ),
			'top top'       => esc_html__( 'Top - Top', 'motionui-addons-for-elementor' ),
			'top center'    => esc_html__( 'Top - Center', 'motionui-addons-for-elementor' ),
			'top bottom'    => esc_html__( 'Top - Bottom', 'motionui-addons-for-elementor' ),
			'center top'    => esc_html__( 'Center - Top', 'motionui-addons-for-elementor' ),
			'center center' => esc_html__( 'Center - Center', 'motionui-addons-for-elementor' ),
			'center bottom' => esc_html__( 'Center - Bottom', 'motionui-addons-for-elementor' ),
			'bottom top'    => esc_html__( 'Bottom - Top', 'motionui-addons-for-elementor' ),
			'bottom center' => esc_html__( 'Bottom - Center', 'motionui-addons-for-elementor' ),
			'bottom bottom' => esc_html__( 'Bottom - Bottom', 'motionui-addons-for-elementor' ),
			'custom'        => esc_html__( 'Custom', 'motionui-addons-for-elementor' ),
		);
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
	/**
	 * Register the shared direction picker for an Elementor element.
	 *
	 * @param \Elementor\Element_Base $element The Elementor element instance.
	 * @param array                   $args {
	 *     Optional. Configuration arguments.
	 *
	 *     @type string $name      Control key. Nothing is registered without one. Default ''.
	 *     @type array  $condition Elementor condition array. Default array().
	 *     @type array  $remove    Option keys to leave out, e.g. array( 'top', 'bottom' ). Default array().
	 *     @type string $default   Preselected option. Default '', meaning none, so the
	 *                             script's own fallback stays in charge.
	 * }
	 */
	public static function get_derection_control( $element, array $args = array() ) {

		$defaults = array(
			'name'      => '',
			'condition' => array(),
			'remove'    => array('center'),
			'default'   => '',
			'title_left'   => esc_html__( 'Left', 'motionui-addons-for-elementor' ),
			'title_right'  => esc_html__( 'Right', 'motionui-addons-for-elementor' ),
			'title_top'    => esc_html__( 'Top', 'motionui-addons-for-elementor' ),
			'title_bottom' => esc_html__( 'Bottom', 'motionui-addons-for-elementor' ),
			'title_center' => esc_html__( 'Center', 'motionui-addons-for-elementor' ),
		);

		$args = wp_parse_args( $args, $defaults );
		$name = sanitize_key( $args['name'] );

		if ( ! $name ) {
			return;
		}

		$options = array(
			'left'   => array(
				'title' => $args['title_left'],
				'icon'  => 'eicon-h-align-left',
			),
			'right'  => array(
				'title' => $args['title_right'],
				'icon'  => 'eicon-h-align-right',
			),
			'top'    => array(
				'title' => $args['title_top'],
				'icon'  => 'eicon-v-align-top',
			),
			'bottom' => array(
				'title' => $args['title_bottom'],
				'icon'  => 'eicon-v-align-bottom',
			),
			'center' => array(
				'title' => $args['title_center'],
				'icon'  => 'eicon-v-align-center',
			),
		);

		$options = array_diff_key( $options, array_flip( (array) $args['remove'] ) );

		if ( ! $options ) {
			return;
		}

		$default = $args['default'];

		// Never preselect something 'remove' just took away — Elementor would
		// save a value the control cannot show.
		if ( '' !== $default && ! isset( $options[ $default ] ) ) {
			$default = key( $options );
		}

		$element->add_control(
			$name.'muia_motion_direction',
			array(
				'label'              => esc_html__( 'Direction', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::CHOOSE,
				'default'            => $default,
				'frontend_available' => true,
				'options'            => $options,
				'condition'          => $args['condition'],
				'prefix_class' => 'muia-direction-',
				'toggle'             => false,  
			)
		);
	}
	
	public static function fromTo_controls( $element, array $args = array() ) {

		$defaults = array(
			'name'         => '',
			'condition'    => array(),
			'from_label'   => esc_html__( 'From', 'motionui-addons-for-elementor' ),
			'to_label'     => esc_html__( 'To', 'motionui-addons-for-elementor' ),
			'from_var'     => '--muia-from',
			'to_var'       => '--muia-to',
			'size_units'   => array( 'px', '%', 'vh', 'vw' ),
			'range'        => array(
				'px' => array( 'min' => -500, 'max' => 500, 'step' => 1 ),
				'%'  => array( 'min' => -100, 'max' => 100, 'step' => 1 ),
				'vh' => array( 'min' => -100, 'max' => 100, 'step' => 1 ),
				'vw' => array( 'min' => -100, 'max' => 100, 'step' => 1 ),
			),
			'from_default' => array( 'unit' => '%', 'size' => 0 ),
			'to_default'   => array( 'unit' => '%', 'size' => 0 ),
			'separator'    => 'before',
		);

		$args = wp_parse_args( $args, $defaults );
		$name = sanitize_key( $args['name'] );

		$element->add_responsive_control(
			$name . 'muia_motion_from',
			array(
				'label'              => $args['from_label'],
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => $args['size_units'],
				'range'              => $args['range'],
				'default'            => $args['from_default'],
				'frontend_available' => true,
				'condition'          => $args['condition'],
				'separator'          => $args['separator'],
				'selectors'          => array(
					'{{WRAPPER}}' => $args['from_var'] . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$element->add_responsive_control(
			$name . 'muia_motion_to',
			array(
				'label'              => $args['to_label'],
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => $args['size_units'],
				'range'              => $args['range'],
				'default'            => $args['to_default'],
				'frontend_available' => true,
				'condition'          => $args['condition'],
				'selectors'          => array(
					'{{WRAPPER}}' => $args['to_var'] . ': {{SIZE}}{{UNIT}};',
				),
			)
		);
	}
}
