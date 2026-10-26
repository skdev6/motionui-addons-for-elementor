<?php

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Motion Library extension.
 *
 * One Advanced-tab section on every element. The `Enable` switch gates the
 * whole section; the `Animation` select picks a preset, and choosing `Custom`
 * opens the From / To transform builder.
 *
 *   muia_motion_effect_enable  ──► everything below
 *   muia_motion_effects_name   ──► direction, child targeting, motion settings
 *   muia_motion_effects_name = custom ──► custom_motion_controls()
 */
class Motion_Effects {

	/**
	 * Wiring that only applies while this extension is switched on.
	 *
	 * Called from Extensions_Manager::init() behind $is_motion_active, so a
	 * disabled extension registers nothing and enqueues nothing.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/preview/enqueue_scripts', array( self::class, 'enqueue_preview_scripts' ) );
	}

	/**
	 * Scripts the editor preview needs to play the animations live.
	 *
	 * The front end loads the same handles on demand through the Enable
	 * switch's `assets` list.
	 *
	 * @return void
	 */
	public static function enqueue_preview_scripts() {
		wp_enqueue_script( 'gsap' );
		wp_enqueue_script( 'scroll-trigger' );
		wp_enqueue_script( 'motion-lib' );
	}

	/**
	 * Adds the Motion Library section to an element's Advanced tab.
	 *
	 * @param \Elementor\Element_Base $element Element to add controls to.
	 * @return void
	 */
	public static function register_controls( $element ) {
		$element->start_controls_section(
			'muia_addons_scroll_animation',
			array(
				'label' => sprintf(
					'<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>',
					__( 'Motion Library', 'motionui-addons-for-elementor' )
				),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		self::get_controls( $element );

		$element->end_controls_section();
	}

	/**
	 * Every control of the section, in display order.
	 *
	 * @param \Elementor\Element_Base $element Element to add controls to.
	 * @return void
	 */
	public static function get_controls( $element ) {

		// Gate for the whole section: nothing but the switch shows until it is on.
		$enabled = array( 'muia_motion_effect_enable' => 'yes' );

		// An animation (preset or custom) has been picked.
		$has_animation = array_merge( $enabled, array( 'muia_motion_effects_name!' => '' ) );

		// The Custom preset, which owns the From / To builder.
		$is_custom = array_merge( $enabled, array( 'muia_motion_effects_name' => 'custom' ) );

		self::enable_control( $element );
		self::animation_control( $element, $enabled );

		self::custom_motion_controls( $element, $is_custom );

		self::from_control( $element, $enabled );

		Motion::get_derection_control(
			$element,
			array(
				'name'      => 'motion_effect',
				'remove' => ['center-h'],
				'title_v_center' => 'Center',
				'default'   => 'bottom',
				'condition' => array_merge(
					$enabled,
					array( 'muia_motion_effects_name!' => array( '', 'custom' ) )
				),
			)
		);

		self::child_controls( $element, $has_animation );

		Motion::add_motion_settings_controls(
			$element,
			array(
				'with_scroll'       => true,
				'prefix'            => 'effect_',
				'stagger'           => true,
				'default_ease'      => 'power3.out',
				'stagger_condition' => array(
					'muia_child_selector!' => '',
				),
				'condition'         => $has_animation,
			)
		);
	}

	/**
	 * The Enable switch.
	 *
	 * Also declares the scripts Elementor loads on the front end, only for
	 * elements that have it turned on.
	 *
	 * @param \Elementor\Element_Base $element Element to add controls to.
	 * @return void
	 */
	private static function enable_control( $element ) {

		$scripts = array();

		foreach ( array( 'elementor-frontend', 'gsap', 'scroll-trigger', 'motion-lib' ) as $handle ) {
			$scripts[] = array(
				'name'       => $handle,
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'muia_motion_effect_enable',
							'operator' => '===',
							'value'    => 'yes',
						),
					),
				),
			);
		}

		$element->add_control(
			'muia_motion_effect_enable',
			array(
				'label'              => __( 'Enable', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'prefix_class'       => 'muia-motion-lib-',
				'render_type'        => 'template',
				'return_value'       => 'yes',
				'style_transfer'     => false,
				'frontend_available' => true,
				'assets'             => array( 'scripts' => $scripts ),
			)
		);
	}

	/**
	 * The Animation picker.
	 *
	 * Its value ends up on the element as a `muia-motion-effect-{value}` class.
	 *
	 * @param \Elementor\Element_Base $element   Element to add controls to.
	 * @param array                   $condition Controls condition.
	 * @return void
	 */
	private static function animation_control( $element, array $condition ) {
		$element->add_control(
			'muia_motion_effects_name',
			array(
				'label'              => esc_html__( 'Animation', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'frontend_available' => true,
				'render_type'        => 'template',
				'options'            => muia_pro_options ( array(
					'fade'    => esc_html__( 'Fade', 'motionui-addons-for-elementor' ),
					'slide'   => esc_html__( 'Slide', 'motionui-addons-for-elementor' ),
					'zoom'    => esc_html__( 'Zoom', 'motionui-addons-for-elementor' ),
					'flip'    => esc_html__( 'Flip', 'motionui-addons-for-elementor' ),
					'rotate'  => esc_html__( 'Rotate', 'motionui-addons-for-elementor' ),
					'skew'    => esc_html__( 'Skew', 'motionui-addons-for-elementor' ),
					'bounce'  => esc_html__( 'Bounce', 'motionui-addons-for-elementor' ),
					'elastic' => esc_html__( 'Elastic', 'motionui-addons-for-elementor' ),
					'blur'    => esc_html__( 'Blur', 'motionui-addons-for-elementor' ),
					'clip'    => esc_html__( 'Clip', 'motionui-addons-for-elementor' ),
					
				), array(
					'custom'  => esc_html__( 'Custom', 'motionui-addons-for-elementor' )
				)),
				'default'            => 'fade',
				'prefix_class'       => 'visibility__hidden muia-motion-effect-',
				'condition'          => $condition,
			)
		);
	}

	/**
	 * Travel distance for the Fade and Slide presets.
	 *
	 * @param \Elementor\Element_Base $element Element to add controls to.
	 * @param array                   $enabled Controls condition for the enable switch.
	 * @return void
	 */
	private static function from_control( $element, array $enabled ) {
		$element->add_responsive_control(
			'muia_ani_from',
			array(
				'label'              => __( 'From', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
				'frontend_available' => true,
				'range'              => array(
					'px' => array( 'min' => 0, 'max' => 1000 ),
				),
				'default'            => array(
					'unit' => 'px',
					'size' => 100,
				),
				'condition'          => array_merge(
					$enabled,
					array( 'muia_motion_effects_name' => array( 'fade', 'slide' ) )
				),
			)
		);
	}

	/**
	 * Child targeting: animate descendants instead of the element itself.
	 *
	 * @param \Elementor\Element_Base $element   Element to add controls to.
	 * @param array                   $condition Controls condition.
	 * @return void
	 */
	private static function child_controls( $element, array $condition ) {
		$element->add_control(
			'muia_animate_for_child',
			array(
				'label'              => __( 'Animate Inner Elements', 'motionui-addons-for-elementor' ),
				'description'        => esc_html__( 'Animate elements inside this one instead of the whole block.', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => $condition,
			)
		);

		$element->add_control(
			'muia_child_selector',
			array(
				'label'             => esc_html__( 'Inner Element Selector', 'motionui-addons-for-elementor' ),
				'type'              => Controls_Manager::TEXT,
				'description'       => esc_html__( 'CSS selector of the inner elements to animate, e.g. ".card" or ".title, .text". Several matches animate one after another using Stagger. Leave empty to animate the whole block.', 'motionui-addons-for-elementor' ),
				'placeholder'       => '.class-name, .class-2',
				'sanitize_callback' => 'sanitize_text_field',
				'frontend_available' => true,
				'render_type'       => 'template',
				'condition'         => array_merge( $condition, array( 'muia_animate_for_child' => 'yes' ) ),
			)
		);
	}

	/**
	 * The Custom preset: a From tab and a To tab of transform values.
	 *
	 * Both tabs use the same controls; the To tab's IDs and CSS variables carry
	 * a `_to` / `-to` suffix, which the script reads as the end state.
	 *
	 * @param \Elementor\Element_Base $element   Element to add controls to.
	 * @param array                   $condition Controls condition (enabled + Custom).
	 * @return void
	 */
	public static function custom_motion_controls( $element, array $condition ) {

		$element->start_controls_tabs( 'muia_scroll_ani_tabs', array( 'condition' => $condition ) );

		$element->start_controls_tab(
			'muia_scroll_ani_from',
			array(
				'label'     => __( 'From', 'motionui-addons-for-elementor' ),
				'condition' => $condition,
			)
		);
		self::transform_group( $element, '', $condition );
		$element->end_controls_tab();

		$element->start_controls_tab(
			'muia_scroll_ani_to',
			array(
				'label'     => __( 'To', 'motionui-addons-for-elementor' ),
				'condition' => $condition,
			)
		);
		self::transform_group( $element, '_to', $condition );
		$element->end_controls_tab();

		$element->end_controls_tabs();
	}

	/**
	 * One state (From or To) of the custom builder: translate, rotation,
	 * scale, skew and opacity.
	 *
	 * @param \Elementor\Element_Base $element   Element to add controls to.
	 * @param string                  $id_suffix '' for From, '_to' for To.
	 * @param array                   $condition Controls condition.
	 * @return void
	 */
	private static function transform_group( $element, $id_suffix, array $condition ) {

		$var = '' === $id_suffix ? '' : '-to';

		// Translate.
		self::popover_toggle( $element, 'muia_scroll_ani_translate_toggle' . $id_suffix, __( 'Translate', 'motionui-addons-for-elementor' ), $condition );
		$element->start_popover();
		foreach ( array( 'x' => __( 'Translate X', 'motionui-addons-for-elementor' ), 'y' => __( 'Translate Y', 'motionui-addons-for-elementor' ) ) as $axis => $label ) {
			self::slider(
				$element,
				'muia_scroll_' . $axis . $id_suffix,
				$label,
				'--muia-' . $axis . $var,
				$condition,
				array(
					'size_units' => array( 'px', '%', 'vw', 'vh', 'em', 'rem' ),
					'range'      => array( 'px' => array( 'min' => -1000, 'max' => 1000 ) ),
					'unit'       => true,
				)
			);
		}
		$element->end_popover();

		// Rotation.
		self::popover_toggle( $element, 'muia_scroll_ani_rotate_toggle' . $id_suffix, __( 'Rotation', 'motionui-addons-for-elementor' ), $condition );
		$element->start_popover();
		$element->add_control(
			'muia_scroll_ani_rotate_hr' . $id_suffix,
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => $condition,
			)
		);
		foreach ( array(
			'x' => __( 'Rotation X', 'motionui-addons-for-elementor' ),
			'y' => __( 'Rotation Y', 'motionui-addons-for-elementor' ),
			'z' => __( 'Rotation (Z)', 'motionui-addons-for-elementor' ),
		) as $axis => $label ) {
			self::slider(
				$element,
				'muia_scroll_rotate_' . $axis . $id_suffix,
				$label,
				'--muia-rotate-' . $axis . $var,
				$condition,
				array(
					'size_units' => array( 'deg' ),
					'range'      => array( 'deg' => array( 'min' => -180, 'max' => 180 ) ),
					'unit'       => true,
				)
			);
		}
		$element->end_popover();

		// Scale.
		self::popover_toggle( $element, 'muia_scroll_ani_scale_toggle' . $id_suffix, __( 'Scale', 'motionui-addons-for-elementor' ), $condition );
		$element->start_popover();
		$element->add_control(
			'muia_scroll_ani_scale_hr' . $id_suffix,
			array(
				'type'      => Controls_Manager::DIVIDER,
				'condition' => $condition,
			)
		);
		foreach ( array(
			'x' => __( 'Scale (X)', 'motionui-addons-for-elementor' ),
			'y' => __( 'Scale Y', 'motionui-addons-for-elementor' ),
		) as $axis => $label ) {
			self::slider(
				$element,
				'muia_scroll_scale_' . $axis . $id_suffix,
				$label,
				'--muia-scale-' . $axis . $var,
				$condition,
				array(
					'size_units' => array( 'px' ),
					'range'      => array( 'px' => array( 'min' => 0, 'max' => 5, 'step' => 0.1 ) ),
					'default'    => array( 'size' => 1 ),
				)
			);
		}
		$element->end_popover();

		// Skew.
		self::popover_toggle( $element, 'muia_scroll_ani_skew_toggle' . $id_suffix, __( 'Skew', 'motionui-addons-for-elementor' ), $condition );
		$element->start_popover();
		foreach ( array(
			'x' => __( 'Skew X', 'motionui-addons-for-elementor' ),
			'y' => __( 'Skew Y', 'motionui-addons-for-elementor' ),
		) as $axis => $label ) {
			self::slider(
				$element,
				'muia_scroll_skew_' . $axis . $id_suffix,
				$label,
				'--muia-skew-' . $axis . $var,
				$condition,
				array(
					'size_units' => array( 'deg' ),
					'range'      => array( 'deg' => array( 'min' => -180, 'max' => 180 ) ),
					'unit'       => true,
				)
			);
		}
		$element->end_popover();

		// Opacity.
		self::slider(
			$element,
			'muia_scroll_opacity' . $id_suffix,
			esc_html__( 'Opacity', 'motionui-addons-for-elementor' ),
			'--muia-opacity' . $var,
			$condition,
			array(
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ) ),
			)
		);
	}

	/**
	 * A popover toggle that the script reads through frontend_available.
	 *
	 * @param \Elementor\Element_Base $element   Element to add the control to.
	 * @param string                  $id        Control ID.
	 * @param string                  $label     Control label.
	 * @param array                   $condition Controls condition.
	 * @return void
	 */
	private static function popover_toggle( $element, $id, $label, array $condition ) {
		$element->add_control(
			$id,
			array(
				'label'              => $label,
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => $condition,
			)
		);
	}

	/**
	 * A responsive slider that writes one CSS custom property on the wrapper.
	 *
	 * @param \Elementor\Element_Base $element   Element to add the control to.
	 * @param string                  $id        Control ID.
	 * @param string                  $label     Control label.
	 * @param string                  $css_var   Custom property the value is written to.
	 * @param array                   $condition Controls condition.
	 * @param array                   $args      size_units, range, optional default and
	 *                                           `unit` (append {{UNIT}} to the value).
	 * @return void
	 */
	private static function slider( $element, $id, $label, $css_var, array $condition, array $args ) {
		$control = array(
			'label'      => $label,
			'type'       => Controls_Manager::SLIDER,
			'condition'  => $condition,
			'size_units' => $args['size_units'],
			'range'      => $args['range'],
			'selectors'  => array(
				'{{WRAPPER}}' => $css_var . ': {{SIZE}}' . ( ! empty( $args['unit'] ) ? '{{UNIT}}' : '' ) . ';',
			),
		);

		if ( isset( $args['default'] ) ) {
			$control['default'] = $args['default'];
		}

		$element->add_responsive_control( $id, $control );
	}
}
