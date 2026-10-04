<?php
/**
 * Button Controls Trait
 *
 * Provides reusable Elementor button style controls for widgets.
 *
 * @package     MotionUI_Addons
 * @subpackage  Traits
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Themeic\MotionUI_Addons\Inc\Classes\Motionui;

if ( ! defined( 'ABSPATH' ) ) exit;  

/**
 * Trait Button_Controls
 *
 * Registers reusable Elementor style and content controls for animated button elements.
 * Include this trait in any Elementor widget class that needs button styling.
 *
 * @since 1.0.0
 */
trait Button_Controls {

	use Custom_Control;

	/**
	 * Effect value that swaps in the spotlight markup.
	 *
	 * The markup lives here so any widget using this trait can offer the
	 * button; the animation and the stylesheet ship with Pro, as the
	 * `muia-spotlight-button` module. Chosen without Pro the button still
	 * renders — it simply sits there unstyled — which is why the editor
	 * disables the option (see elementor-editor.js).
	 *
	 * A static property rather than a constant: the plugin supports PHP 7.4,
	 * and constants in traits are 8.2 and later.
	 */
	private static $muia_spotlight_effect = 'muia-Spotlight-btn';
	/**
	 * Normalize and prepare CSS selectors.
	 *
	 * Ensures every selector is prefixed with {{WRAPPER}} if not already.
	 *
	 * @since  1.0.0
	 * @param  string $class_name Comma-separated CSS selector string.
	 * @return array              Array of normalized selectors.
	 */
	private function muia_prepare_selectors( $class_name ) {
		$raw = array_map( 'trim', explode( ',', $class_name ) );

		return array_map(
			function ( $selector ) {
				return ( false !== strpos( $selector, '{{WRAPPER}}' ) )
					? $selector
					: "{{WRAPPER}} {$selector}";
			},
			$raw
		);
	}

	/**
	 * Build state-based selectors (e.g. :hover, .active).
	 *
	 * @since  1.0.0
	 * @param  array  $selectors Base selectors array.
	 * @param  string $state     CSS pseudo-class or class suffix (e.g. ':hover').
	 * @return string            Comma-separated state selectors.
	 */
	private function muia_build_state_selector( $selectors, $state ) {
		return implode(
			', ',
			array_map(
				function ( $s ) use ( $state ) {
					return "{$s}{$state}";
				},
				$selectors
			)
		);
	}

    /**
	 * Register common button style controls in an Elementor widget.
	 * @since  1.0.0
	 *
	 * @param  string $id_prefix Unique prefix for control IDs. Default 'muia_btn'.
	 * @param  array  $args
	 * @return void
	 */
	public function _register_muia_btn_style_controls( $id_prefix = 'muia_btn', $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'           => esc_html__( 'Button Style', 'motionui-addons-for-elementor' ),
				'selectors'       => '.muia-btn',
				'bg_color'        => true,
				'color'           => true,
				'padding'         => true,
				'margin'          => false,
				'border'          => true,
				'active'          => false,
				'active_selector' => '',
				'is_variable'     => true,
				'active_tab'       => true,
				'condition'       => array(),
				'is_tab'          => true,
				'selectors_with_prefix' => true,
			)
		);
		
		$is_tab_style = $args['active_tab'];
		// Caller should pass a pre-translated string; we escape it here for safe output.
		$section_label = esc_html( $args['title'] );

		// Shorthand flag — used throughout to decide CSS var vs direct property.
		$is_var = (bool) $args['is_variable'];

		// Prepare selectors.
		$selectors       = $this->muia_prepare_selectors( $args['selectors_with_prefix'] ? ".themeic-$id_prefix" : ''. $args['selectors'] );   
		$selector        = implode( ', ', $selectors );
		$hover_selector  = $this->muia_build_state_selector( $selectors, ':not(.hvr-none):hover' );
		$is_tab = $args['is_tab'];

		if ( ! empty( $args['active_selector'] ) ) {
			$active_selector = implode(
				', ',
				array_map( 'trim', explode( ',', $args['active_selector'] ) )
			);
		} else {
			$active_selector = $this->muia_build_state_selector( $selectors, '.active' );
		}

		$has_tabs = ( $args['bg_color'] || $args['color'] || $args['border'] );

		// -------------------------------------------------------------------------
		// Section
		// -------------------------------------------------------------------------
		if($is_tab){
			$this->start_controls_section(  
				"{$id_prefix}_style",
				array(
					'label' => $section_label,
					'tab'   => Controls_Manager::TAB_STYLE,
					'condition' => $args['condition']
				)
			);
		}
		// -------------------------------------------------------------------------
		// Typography
		// -------------------------------------------------------------------------
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => "{$id_prefix}_typography",
				'selector' => $selector,
			)
		);

		// -------------------------------------------------------------------------
		// Margin
		// -------------------------------------------------------------------------
		if ( $args['margin'] ) {
			$this->add_responsive_control(
				"{$id_prefix}_margin",
				array(
					'label'      => esc_html__( 'Margin', 'motionui-addons-for-elementor' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
					'selectors'  => array(
						$selector => 'margin : {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
		}

		// -------------------------------------------------------------------------
		// Padding
		// -------------------------------------------------------------------------
		if ( $args['padding'] ) {
			$this->add_responsive_control(
				"{$id_prefix}_padding",
				array(
					'label'      => esc_html__( 'Padding', 'motionui-addons-for-elementor' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
					'selectors'  => array(
						$selector => ( $is_var ? '--padding' : 'padding' ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
		}

		// -------------------------------------------------------------------------
		// State Tabs
		// -------------------------------------------------------------------------
		if ( $has_tabs ) {

			if($is_tab_style){
				$this->start_controls_tabs( "{$id_prefix}_tabs" );

				// -- Normal Tab -------------------------------------------------------
				$this->start_controls_tab(
					"{$id_prefix}_tab_normal",
					array( 'label' => esc_html__( 'Normal', 'motionui-addons-for-elementor' ) )
				);
			}
			if ( $args['color'] ) {
				$this->add_control(
					"{$id_prefix}_color",
					array(
						'label'     => esc_html__( 'Text Color', 'motionui-addons-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => ( $is_var ? '--color' : 'color' ) . ': {{VALUE}};',
						),
					)
				);
			}

			if ( $args['bg_color'] ) {
				$this->add_control(
					"{$id_prefix}_bg_color",
					array(
						'label'     => esc_html__( 'Background Color', 'motionui-addons-for-elementor' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => ( $is_var ? '--bg-color' : 'background-color' ) . ': {{VALUE}};',
						),
					)
				);
			}

			if ( $args['border'] ) {
				
				$this->_add_muia_border_controls( $id_prefix, $selector, $is_var );

				$this->add_responsive_control(
					"{$id_prefix}_border_radius",
					array(
						'label'      => esc_html__( 'Border Radius', 'motionui-addons-for-elementor' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
						'selectors'  => array(
							$selector => ( $is_var ? '--border-radius' : 'border-radius' ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; --border-radius-top: {{TOP}}{{UNIT}};',
						),  
					)
				);
			}
		
			if($is_tab_style) $this->end_controls_tab();

			if($is_tab_style){
				// -- Hover Tab --------------------------------------------------------
				$this->start_controls_tab(
					"{$id_prefix}_tab_hover",
					array( 'label' => esc_html__( 'Hover', 'motionui-addons-for-elementor' ) )
				);

				if ( $args['color'] ) {
					
					$color_hover_selectors = $is_var
						? array(
							$selector => '--hover-color: {{VALUE}};',
						)
						: array(
							$hover_selector => 'color: {{VALUE}};',
							$selector       => '--hover-color: {{VALUE}};',
						);

					$this->add_control(
						"{$id_prefix}_color_hover",
						array(
							'label'     => esc_html__( 'Text Color', 'motionui-addons-for-elementor' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => $color_hover_selectors,
						)
					);
				}

				if ( $args['bg_color'] ) {
					
					$bg_hover_selectors = $is_var
						? array(
							$selector => '--hover-bg-color: {{VALUE}};',
						)
						: array(
							$hover_selector => 'background-color: {{VALUE}};',
							$selector       => '--hover-bg-color: {{VALUE}};',
						);

					$this->add_control(
						"{$id_prefix}_bg_color_hover",
						array(
							'label'     => esc_html__( 'Background Color', 'motionui-addons-for-elementor' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => $bg_hover_selectors,
						)
					);
				}

				if ( $args['border'] ) {
					$border_hover_selectors = $is_var
						? array(
							$selector => '--hover-border-color: {{VALUE}};',
						)
						: array(
							$hover_selector => 'border-color: {{VALUE}};',
							$selector       => '--hover-border-color: {{VALUE}};',
						);

					$this->add_control(
						"{$id_prefix}_border_color_hover",
						array(
							'label'     => esc_html__( 'Border Color', 'motionui-addons-for-elementor' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => $border_hover_selectors,
						)
					);
				}

				$this->end_controls_tab();

				// -- Active Tab -------------------------------------------------------
				if ( $args['active'] ) {

					$this->start_controls_tab(
						"{$id_prefix}_tab_active",
						array( 'label' => esc_html__( 'Active', 'motionui-addons-for-elementor' ) )
					);

					if ( $args['color'] ) {
						$this->add_control(
							"{$id_prefix}_color_active",
							array(
								'label'     => esc_html__( 'Text Color', 'motionui-addons-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => array(
									$active_selector => ( $is_var ? '--active-color' : 'color' ) . ': {{VALUE}};',
								),
							)
						);
					}

					if ( $args['bg_color'] ) {
						$this->add_control(
							"{$id_prefix}_bg_color_active",
							array(
								'label'     => esc_html__( 'Background Color', 'motionui-addons-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => array(
									$active_selector => ( $is_var ? '--active-bg-color' : 'background-color' ) . ': {{VALUE}};',
								),
							)
						);
					}

					if ( $args['border'] ) {
						$this->add_control(
							"{$id_prefix}_border_color_active",
							array(
								'label'     => esc_html__( 'Border Color', 'motionui-addons-for-elementor' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => array(
									$active_selector => ( $is_var ? '--active-border-color' : 'border-color' ) . ': {{VALUE}};',
								),
							)
						);
					}

					$this->end_controls_tab();
				}

				$this->end_controls_tabs();
			}
		}
		if($is_tab) $this->end_controls_section();
	}

	/**
	 * Register common button content controls in an Elementor widget.
	 *
	 * @since  1.0.0
	 * @param  string $id_prefix Unique prefix for control IDs. Default 'muia_btn'.
	 * @param  string $title     Section title. Default 'Animated Button'.
	 * @return void
	 */
	public function _register_muia_btn_content_controls( $id_prefix = 'muia_btn', $args = array(), ?Repeater $repeater = null ) {

		$args = wp_parse_args(
			$args,
			array(
				'default_btn_type'   => 'muia-btn-normal',
				'default_btn_effect' => 'muia-btn-wave',
				'show_content'       => true,
				'align'              => true,
				'is_in_tab'          => true,
				'title'              => esc_html__( 'Button Content', 'motionui-addons-for-elementor' ),
				'condition'          => array(),
			)
		);

		// Use repeater if passed, otherwise use $this (widget).
		// This allows the same method to register controls in both contexts.
		$control_manager = $repeater instanceof Repeater ? $repeater : $this;

		$is_content_controls = $args['show_content'];
		$is_align            = $args['align'];
		$is_in_tab           = $args['is_in_tab'];

		// Section wrapping only makes sense on the widget, not inside a repeater.
		$is_in_tab = $is_in_tab && ! ( $repeater instanceof Repeater );

		if ( $is_in_tab ) {
			$this->start_controls_section(
				"{$id_prefix}_muia_button_content",
				array(
					'label'     => $args['title'],
					'tab'       => Controls_Manager::TAB_CONTENT,
					'condition' => $args['condition'],
				)
			);
		}

		$control_manager->add_control(
			"{$id_prefix}_btn_type",
			array(
				'label'   => esc_html__( 'Type', 'motionui-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $args['default_btn_type'],
				'options' => array(
					'muia-btn-normal'               => esc_html__( 'Normal', 'motionui-addons-for-elementor' ),
					'muia-btn-circle'               => esc_html__( 'Circle', 'motionui-addons-for-elementor' ),
					'muia-btn-separate-circle-icon' => esc_html__( 'Separate Circle Icon', 'motionui-addons-for-elementor' ),
				),
			)
		);

		$control_manager->add_responsive_control(
			$id_prefix . 'circle_btn_size',
			array(
				'label'      => esc_html__( 'Circle Size', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em', 'px', '%', 'rem', 'custom' ),
				'range'      => array(
					'em' => array( 'min' => 0, 'max' => 100, 'step' => 0.01 ),
					'px' => array( 'min' => 0, 'max' => 1000, 'step' => 5 ),
					'%'  => array( 'min' => 0, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'em' ),
				'selectors'  => array(
					// Repeater items use a different selector context.
					$repeater instanceof Repeater
						? "{{WRAPPER}} {{CURRENT_ITEM}} .themeic-{$id_prefix}.muia-btn"
						: "{{WRAPPER}} .themeic-{$id_prefix}.muia-btn" => '--circle-btn-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					"{$id_prefix}_btn_type" => 'muia-btn-circle',
				),
			)
		);

		$control_manager->add_control(
			"{$id_prefix}_btn_effect",
			array(
				'label'   => esc_html__( 'Effect', 'motionui-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $args['default_btn_effect'],
				// How the editor finds the Pro choices below.
				'classes' => muia_pro_select_class(),
				'options' => muia_pro_options(
					array(
						'muia-btn-default' => esc_html__( 'Normal', 'motionui-addons-for-elementor' ),
						'muia-btn-wave'    => esc_html__( 'Wave', 'motionui-addons-for-elementor' ),
						'muia-btn-reveal'  => esc_html__( 'Reveal', 'motionui-addons-for-elementor' ),
					),
					array(
						self::$muia_spotlight_effect => esc_html__( 'Spotlight', 'motionui-addons-for-elementor' ),
					)
				),
			)
		);

		$this->_muia_spotlight_controls( $id_prefix, $control_manager );

		if ( Motionui::is_active_pro() ) {
			$control_manager->add_control(
				"{$id_prefix}_muia_magnetic_effect",
				array(
					'label'        => esc_html__( 'Magnetic Effect', 'motionui-addons-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_block'  => false,
					'return_value' => 'yes',
					'separator'    => 'after',
				)
			);
		}

		if ( $is_content_controls ) {
			$control_manager->add_control(
				"{$id_prefix}_button_text",
				array(
					'label'       => esc_html__( 'Text', 'motionui-addons-for-elementor' ),
					'label_block' => true,
					'type'        => Controls_Manager::TEXT,
					'default'     => esc_html__( 'Button Text', 'motionui-addons-for-elementor' ),
					'dynamic'     => array( 'active' => true ),
				)
			);
			$control_manager->add_control( 
				"{$id_prefix}_button_link",
				array(
					'label'         => esc_html__( 'Link', 'motionui-addons-for-elementor' ),
					'type'          => Controls_Manager::URL,
					'placeholder'   => esc_html__( 'https://your-link.com', 'motionui-addons-for-elementor' ),
					'show_external' => true,
					'default'       => array(
						'url'         => '#',
						'is_external' => false,
						'nofollow'    => true,
					),
					'dynamic'       => array( 'active' => true )
				)
			);
			if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {  
				$control_manager->add_control(
					"{$id_prefix}_muia_dynamic_post_url",
					[
						'label'       => esc_html__( 'Dynamic Post Url', 'motionui-addons-for-elementor' ),
						'type'        => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Yes', 'motionui-addons-for-elementor' ),
						'label_off' => esc_html__( 'No', 'motionui-addons-for-elementor' ),
						'return_value' => 'yes',
						'default' => 'no',
					]
				);
				$control_manager->add_control(
					"{$id_prefix}_muia_dynamic_url_form_meta_key",
					[
						'label'       => esc_html__( 'Dynamic Url', 'motionui-addons-for-elementor' ),
						'type'        => Controls_Manager::TEXT,
						'placeholder' => esc_html__( 'Meta Key', 'motionui-addons-for-elementor' ),
						'condition'   =>[
							"{$id_prefix}_muia_dynamic_post_url!" => ['yes']
						]
					]
				);
			}
		}

		$control_manager->add_control(
			"{$id_prefix}_icon",
			array(
				'label'       => esc_html__( 'Icon', 'motionui-addons-for-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
			)
		);

		$control_manager->add_control(
			"{$id_prefix}_icon_position_style",
			array(
				'label'       => esc_html__( 'Icon Position', 'motionui-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'motionui-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'motionui-addons-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => false,
				'condition' => array( "{$id_prefix}_icon[value]!" => '' ),
			)
		);

		$control_manager->add_responsive_control(
			$id_prefix . 'space_between_text_icon',
			array(
				'label'      => esc_html__( 'Icon Spacing', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem', 'custom' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 1000, 'step' => 5 ),
					'%'  => array( 'min' => 0, 'max' => 100 ),
				),
				'selectors'  => array(
					$repeater instanceof Repeater
					? "{{WRAPPER}} {{CURRENT_ITEM}} .themeic-{$id_prefix}.muia-btn"
					: "{{WRAPPER}} .themeic-{$id_prefix}.muia-btn" => '--icon-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( "{$id_prefix}_icon[value]!" => '' ),
			)
		);

		$control_manager->add_responsive_control(
			$id_prefix . 'btn_icon_rotation',
			array(
				'label'      => esc_html__( 'Icon Rotation', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array( 'min' => -360, 'max' => 360, 'step' => 1 ),
				),
				'selectors'  => array(
					$repeater instanceof Repeater
					? "{{WRAPPER}} {{CURRENT_ITEM}} .themeic-{$id_prefix}.muia-btn"
					: "{{WRAPPER}} .themeic-{$id_prefix}.muia-btn" => '--icon-rotation: {{SIZE}}deg;',
				),
				'condition'  => array( "{$id_prefix}_icon[value]!" => '' ),
			)
		);

		$control_manager->add_responsive_control(
			$id_prefix . 'btn_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'motionui-addons-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'em', 'px', '%', 'rem', 'custom' ),
				'range'      => array(
					'em' => array( 'min' => 0, 'max' => 15, 'step' => 0.01 ),
					'px' => array( 'min' => 0, 'max' => 1000, 'step' => 1 ),
					'%'  => array( 'min' => 0, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'em' ),
				'selectors'  => array(
					$repeater instanceof Repeater
					? "{{WRAPPER}} {{CURRENT_ITEM}} .themeic-{$id_prefix}.muia-btn .muia-btn-icon-inner"
					: "{{WRAPPER}} .themeic-{$id_prefix}.muia-btn .muia-btn-icon-inner" => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( "{$id_prefix}_icon[value]!" => '' ),
			)
		);

		// Align and Pro notice only make sense on the widget, not in a repeater.
		if ( ! ( $repeater instanceof Repeater ) ) {

			if ( $is_align ) {
				$this->add_responsive_control(
					"{$id_prefix}_align_x",
					array(
						'label'       => esc_html__( 'Alignment', 'motionui-addons-for-elementor' ),
						'type'        => Controls_Manager::CHOOSE,
						'label_block' => false,
						'separator'   => 'before',
						'options'     => array(
							'left'   => array(
								'title' => esc_html__( 'Left', 'motionui-addons-for-elementor' ),
								'icon'  => 'eicon-h-align-left',
							),
							'center' => array(
								'title' => esc_html__( 'Center', 'motionui-addons-for-elementor' ),
								'icon'  => 'eicon-h-align-center',
							),
							'right'  => array(
								'title' => esc_html__( 'Right', 'motionui-addons-for-elementor' ),
								'icon'  => 'eicon-h-align-right',
							),
						),
						'toggle'    => true,
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-container'            => 'text-align: {{VALUE}};',
							'{{WRAPPER}}:not(:has(.elementor-widget-container))' => 'text-align: {{VALUE}};',
						),
					)
				);
			}

			if ( ! Motionui::is_active_pro() ) {
				$this->add_control(
					"{$id_prefix}_muia_pro_btn_notice",
					array(
						'separator' => 'before',
						'type'      => Controls_Manager::RAW_HTML,
						'raw'       => muia_get_pronotice_html( false ),
					)
				);
			}
		}

		if ( $is_in_tab ) {
			$this->end_controls_section();
		}
	}

	/**
	 * Render the animated button HTML.
	 *
	 * Call this from the widget's render() method.
	 *
	 * @since  1.0.0
	 * @param  string $id_prefix Unique prefix matching the registered controls. Default 'muia_btn'.
	 * @return void
	 */
	public function _render_muia_btn( $id_prefix = 'muia_btn', $args = array(), $repeater_key = null) {

		if ( ! empty( $args['widget_id'] ) && ! empty( $args['page_id'] ) && isset( $args['muia_is_ajax_widget'] ) && $args['muia_is_ajax_widget'] ) {
			$remote_settings = $this->muia_get_widget_settings(
				(int) $args['page_id'],
				(string) $args['widget_id']
			);
			// Only override if we actually got settings back.
			if ( null !== $remote_settings ) {
				$settings = $remote_settings;
			}else{
				return;
			}
		}else{
			$settings = $this->get_settings_for_display();
		}

		if($repeater_key !== null && !empty($settings[$repeater_key])){  
			foreach ($settings[$repeater_key] as $key => $btn) {
				echo '<div class="elementor-repeater-item-'.esc_attr( $btn['_id'] ).'">';
				$this->muia_get_btn_output($id_prefix, $btn, $args);
				echo '</div>';
			}
		}else{
			$this->muia_get_btn_output($id_prefix, $settings, $args);
		}
	}
	/**
	 * The handful of options only the spotlight button has.
	 *
	 * Carried over from the Spotlight Button widget this replaced, so nothing
	 * it could do was lost when it became an effect. Colour, radius, padding
	 * and typography are not here: those come from the shared Button Style
	 * section, which the spotlight stylesheet reads.
	 *
	 * @since  1.0.0
	 * @param  string $id_prefix       Control ID prefix.
	 * @param  object $control_manager Widget or repeater to add the controls to.
	 * @return void
	 */
	protected function _muia_spotlight_controls( $id_prefix, $control_manager ) {  

		$only_spotlight = array( "{$id_prefix}_btn_effect" => self::$muia_spotlight_effect );

		$control_manager->add_control(
			"{$id_prefix}_spotlight_text_reveal",
			array(
				'label'        => esc_html__( 'Text Reveal', 'motionui-addons-for-elementor' ),
				'description'  => esc_html__( 'Rolls the label up on hover.', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => $only_spotlight,
			)
		);

		$control_manager->add_control(
			"{$id_prefix}_spotlight_zoom",
			array(
				'label'        => esc_html__( 'Zoom On Hover', 'motionui-addons-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => $only_spotlight,
			)
		);
	}

	/**
	 * Is this button set to the spotlight effect?
	 *
	 * @param  string $id_prefix Control ID prefix.
	 * @param  array  $settings  Widget settings.
	 * @return bool
	 */
	protected function muia_is_spotlight_btn( $id_prefix, $settings ) {

		return isset( $settings[ "{$id_prefix}_btn_effect" ] )
			&& self::$muia_spotlight_effect === $settings[ "{$id_prefix}_btn_effect" ];
	}

	public function muia_get_btn_output($id_prefix, $settings, $args = array()){

		$btn_type = ! empty( $settings[ "{$id_prefix}_btn_type" ] )
			? $settings[ "{$id_prefix}_btn_type" ]
			: 'muia-btn-solid';

		$btn_effect = ! empty( $settings[ "{$id_prefix}_btn_effect" ] )
			? $settings[ "{$id_prefix}_btn_effect" ]
			: 'muia-btn-reveal';

		// A site that saved a Pro effect and then lost Pro keeps the value in
		// the database — Elementor builds one control stack per widget type, so
		// the select cannot offer it back for that one element — but there is
		// no stylesheet to render it with any more. Fall back here rather than
		// output markup nothing styles; the saved value is untouched and
		// returns with Pro.
		if ( self::$muia_spotlight_effect === $btn_effect && ! muia_has_pro() ) {
			$btn_effect = 'muia-btn-default';
		}

		$magnetic_effect = ( isset( $settings[ "{$id_prefix}_muia_magnetic_effect" ] )
			&& 'yes' === $settings[ "{$id_prefix}_muia_magnetic_effect" ] )
			? 'muia-magnetic'
			: '';

		$button_text = ! empty( $settings[ "{$id_prefix}_button_text" ] )
			? $settings[ "{$id_prefix}_button_text" ]
			: '';
			
		$dynamic_url = ! empty( $settings[ "{$id_prefix}_muia_dynamic_url_form_meta_key" ] )
			? $settings[ "{$id_prefix}_muia_dynamic_url_form_meta_key" ]
			: '';
		
		$post_url = ! empty( $settings[ "{$id_prefix}_muia_dynamic_post_url" ] ) && $settings[ "{$id_prefix}_muia_dynamic_post_url" ] === 'yes'
			? get_the_permalink()
			: '';   
		
        $is_stroke_icon = ! empty( $settings[ "{$id_prefix}_is_stroke_icon" ] ) && $settings[ "{$id_prefix}_is_stroke_icon" ] === 'yes'
			? 'muia-btn-stroke-icon'
			: '';

		$link_data = isset( $settings[ "{$id_prefix}_button_link" ] )
			? $settings[ "{$id_prefix}_button_link" ]
			: array();

		$button_url    = ! empty( $link_data['url'] ) ? $link_data['url'] : '';
		$button_target = ! empty( $link_data['is_external'] ) ? '_blank' : '_self';
		$button_rel    = ! empty( $link_data['nofollow'] ) ? 'nofollow' : '';

		$button_url = !empty($dynamic_url) ? muia_get_dynamic_meta($dynamic_url) : $button_url;   
		$button_url = !empty($post_url) ? $post_url : $button_url;   

		if ( '_blank' === $button_target ) {
			$button_rel = trim( $button_rel . ' noreferrer' );
		}

		$icon = ! empty( $settings[ "{$id_prefix}_icon" ] )
			? $settings[ "{$id_prefix}_icon" ]
			: array();

		$icon_position = ! empty( $settings[ "{$id_prefix}_icon_position_style" ] )
			? $settings[ "{$id_prefix}_icon_position_style" ]
			: 'left';

		$args = wp_parse_args(
			$args,
			array(
				'title'      => $button_text,
				'url'        => $button_url,
				'url_target' => $button_target,
				'url_rel'    => $button_rel,
				'class'      => '',
			)
		);

        $btn_classes = implode(' ', array_filter([   
			"themeic-$id_prefix",
            'muia-btn',
            $btn_type,
            $btn_effect,
            $magnetic_effect,
            $is_stroke_icon,
            $icon_position == 'left' ? 'muia-icon-pos-left' : '',
            $icon_position == 'right' ? 'muia-icon-pos-right' : '',
			$args['class']
        ]));   

		$is_nothing = empty( $icon['value'] ) && empty( $args['title'] ) && empty( $args['url'] );

		if($is_nothing) return;

		// The spotlight button is a different shape of markup, not a modifier
		// class on this one, so it takes over from here. Normalised above, so
		// reaching this means Pro is present to style it.
		if ( self::$muia_spotlight_effect === $btn_effect ) {
			$this->muia_get_spotlight_btn_output(
				$id_prefix,
				$settings,
				$args,
				array(
					'icon'          => $icon,
					'icon_position' => $icon_position,
					'magnetic'      => $magnetic_effect,
				)
			);

			return;
		}

		echo $magnetic_effect !=='' ? '<div class="muia-btn-wrap">' : '';
		?>
		<a
			href="<?php echo esc_url( $args['url'] ); ?>"
			target="<?php echo esc_attr( $args['url_target'] ); ?>"
			rel="<?php echo esc_attr( $args['url_rel'] ); ?>"
			class="<?php echo esc_attr( $btn_classes ); ?>"
		>

            <?php if ( ! empty( $icon['value'] ) && 'left' === $icon_position ) : ?>    
                <span class="muia-btn-icon-wrap muia-btn-icon-wrap-left"> 
					<?php echo $btn_type === 'muia-btn-separate-circle-icon' ? '<span class="muia-icon-ani"></span>' : ''; ?>
                    <span class="muia-btn-icon-inner">
                        <?php
                            Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true', 'class' => 'muia-btn-icon icon-primary' ) ); 
                            Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true', 'class' => 'muia-btn-icon icon-secondary' ) );
                        ?>
                    </span>
                </span>
            <?php endif; ?>
            
			<?php if ( ! empty( $args['title'] ) ) : ?>
				<span class="muia-btn-text-wrap">
					<span class="muia-btn-text"><?php echo esc_html( $args['title'] ); ?></span>
				</span>
			<?php endif; ?>

            <?php if ( ! empty( $icon['value'] ) && 'right' === $icon_position ) : ?> 
                <span class="muia-btn-icon-wrap muia-btn-icon-wrap-right">
					<?php echo $btn_type === 'muia-btn-separate-circle-icon' ? '<span class="muia-icon-ani"></span>' : ''; ?>
                    <span class="muia-btn-icon-inner">
                        <?php
                            Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true', 'class' => 'muia-btn-icon icon-primary' ) ); 
                            Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true', 'class' => 'muia-btn-icon icon-secondary' ) );
                        ?>
                    </span>
                </span>
            <?php endif; ?>
			<span class="muia-btn-ani"> </span>
			
		</a>
		<?php
		echo $magnetic_effect !=='' ? '</div>' : '';  
	}
	/**
	 * Render the spotlight button.
	 *
	 * Markup moved here from the Spotlight Button widget so every widget using
	 * this trait can offer it. The animation and the stylesheet stay in Pro as
	 * the `muia-spotlight-button` module, enqueued below only when one of these
	 * is actually on the page — a page with no spotlight button loads neither.
	 *
	 * Two classes matter. `themeic-{$id_prefix}` is what the shared Button
	 * Style controls write their custom properties to, and
	 * `themeic-spotlight-btn` is what the Pro stylesheet and script look for.
	 * `muia-btn` is deliberately absent: the generic button styles would fight
	 * the spotlight ones.
	 *
	 * @since  1.0.0
	 * @param  string $id_prefix Control ID prefix.
	 * @param  array  $settings  Widget settings.
	 * @param  array  $args      Resolved title/url/target/rel/class/tag.
	 * @param  array  $parts     icon, icon_position and magnetic, already read.
	 * @return void
	 */
	protected function muia_get_spotlight_btn_output( $id_prefix, $settings, $args, $parts = array() ) {

		$parts = wp_parse_args(
			$parts,
			array(
				'icon'          => array(),
				'icon_position' => 'left',
				'magnetic'      => '',
			)
		);

		$this->muia_enqueue_spotlight_assets();

		$round = ! empty( $settings[ "{$id_prefix}_spotlight_round" ] )
			? $settings[ "{$id_prefix}_spotlight_round" ]
			: 'round-pill';

		$reveal = ! isset( $settings[ "{$id_prefix}_spotlight_text_reveal" ] )
			|| 'yes' === $settings[ "{$id_prefix}_spotlight_text_reveal" ];

		$zoom = ! isset( $settings[ "{$id_prefix}_spotlight_zoom" ] )
			|| 'yes' === $settings[ "{$id_prefix}_spotlight_zoom" ];

		$title    = isset( $args['title'] ) ? $args['title'] : '';
		$url      = isset( $args['url'] ) ? $args['url'] : '';
		$has_icon = ! empty( $parts['icon']['value'] );

		// A link renders as <a>, otherwise as a real <button>.
		$tag = '' !== $url ? 'a' : 'button';

		$classes = implode(
			' ',
			array_filter(
				array(
					"themeic-{$id_prefix}",
					'themeic-spotlight-btn',
					$round,
					$zoom ? '' : 'btn-no-zoom',
					$parts['magnetic'],
					isset( $args['class'] ) ? $args['class'] : '',
				)
			)
		);
		?>
		<div class="themeic-spotlight-btn-wrap">
			<<?php echo esc_html( $tag ); ?>
				<?php if ( 'a' === $tag ) : ?>
					href="<?php echo esc_url( $url ); ?>"
					target="<?php echo esc_attr( isset( $args['url_target'] ) ? $args['url_target'] : '_self' ); ?>"
					rel="<?php echo esc_attr( isset( $args['url_rel'] ) ? $args['url_rel'] : '' ); ?>"
				<?php else : ?>
					type="button"
				<?php endif; ?>
				class="<?php echo esc_attr( $classes ); ?>"
			>

				<?php if ( $has_icon && 'left' === $parts['icon_position'] ) : ?>
					<span class="btn-icon">
						<?php Icons_Manager::render_icon( $parts['icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( '' !== $title ) : ?>
					<span class="btn-text"<?php echo $reveal ? ' data-text="' . esc_attr( $title ) . '"' : ''; ?>><?php echo esc_html( $title ); ?></span>
				<?php endif; ?>

				<?php if ( $has_icon && 'right' === $parts['icon_position'] ) : ?>
					<span class="btn-icon">
						<?php Icons_Manager::render_icon( $parts['icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>

			</<?php echo esc_html( $tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Bring in the Pro module this button needs, once per request.
	 *
	 * Registered by Pro as `muia-spotlight-button`; absent when Pro is not
	 * installed, which is why nothing is assumed. Enqueueing at render time
	 * rather than through get_script_depends() is what keeps it off pages that
	 * have no spotlight button: the effect is chosen per button, and
	 * get_script_depends() can only answer per widget type.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	protected function muia_enqueue_spotlight_assets() {

		static $done = false;

		if ( $done ) {
			return;
		}

		$done = true;

		if ( wp_style_is( 'muia-spotlight-button', 'registered' ) ) {
			wp_enqueue_style( 'muia-spotlight-button' );
		}

		if ( wp_script_is( 'muia-spotlight-button', 'registered' ) ) {
			wp_enqueue_script( 'muia-spotlight-button' );
		}
	}

	protected function muia_get_widget_settings( int $page_id, string $widget_id ): ?array {
		if ( ! $page_id || ! $widget_id ) {
			return null;
		}

		$document = \Elementor\Plugin::$instance->documents->get( $page_id );

		if ( ! $document ) {
			return null;
		}

		$found = $this->muia_find_widget_in_elements(
			$document->get_elements_data(),
			$widget_id
		);

		return $found ? ( $found['settings'] ?? null ) : null;
	}
	protected function muia_find_widget_in_elements( array $elements, string $widget_id ): ?array {
		foreach ( $elements as $element ) {
			if ( isset( $element['id'] ) && $element['id'] === $widget_id ) {
				return $element;
			}
			if ( ! empty( $element['elements'] ) ) {
				$found = $this->muia_find_widget_in_elements( $element['elements'], $widget_id );
				if ( $found ) {
					return $found;
				}
			}
		}
		return null;
	}
}