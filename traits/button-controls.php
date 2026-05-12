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
			)
		);

		// Caller should pass a pre-translated string; we escape it here for safe output.
		$section_label = esc_html( $args['title'] );

		// Shorthand flag — used throughout to decide CSS var vs direct property.
		$is_var = (bool) $args['is_variable'];

		// Prepare selectors.
		$selectors       = $this->muia_prepare_selectors( $args['selectors'] );
		$selector        = implode( ', ', $selectors );
		$hover_selector  = $this->muia_build_state_selector( $selectors, ':not(.hvr-none):hover' );

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
		$this->start_controls_section(
			"{$id_prefix}_style",
			array(
				'label' => $section_label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

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

			$this->start_controls_tabs( "{$id_prefix}_tabs" );

			// -- Normal Tab -------------------------------------------------------
			$this->start_controls_tab(
				"{$id_prefix}_tab_normal",
				array( 'label' => esc_html__( 'Normal', 'motionui-addons-for-elementor' ) )
			);

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

			$this->end_controls_tab();

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

		$this->end_controls_section();
	}

	/**
	 * Register common button content controls in an Elementor widget.
	 *
	 * @since  1.0.0
	 * @param  string $id_prefix Unique prefix for control IDs. Default 'muia_btn'.
	 * @param  string $title     Section title. Default 'Animated Button'.
	 * @return void
	 */
	public function _register_muia_btn_content_controls( $id_prefix = 'muia_btn', $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'default_btn_type'  => 'muia-btn-normal',
				'default_btn_effect'=> 'muia-btn-wave',
				'show_content'      => true
			)
		);

		$is_content_cntrols = $args['show_content'];

		$this->start_controls_section(
			"{$id_prefix}_muia_button_content",
			array(
				'label' => esc_html__( 'Button Content', 'motionui-addons-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			"{$id_prefix}_btn_type",
			array(
				'label'   => esc_html__( 'Type', 'motionui-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $args['default_btn_type'],
				'options' => array(
					'muia-btn-normal'          => esc_html__( 'Normal', 'motionui-addons-for-elementor' ),
					'muia-btn-circle'        => esc_html__( 'Circle', 'motionui-addons-for-elementor' ),
					'muia-btn-separate-circle-icon'   => esc_html__( 'Separate Circle Icon', 'motionui-addons-for-elementor' ),
				),
			)
		);
		$this->add_responsive_control(      
			'circle_btn_size',
			[
				'label' => esc_html__( 'Circle Size', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px', '%', 'rem', 'custom' ],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.01,   
					],  
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
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}} .muia-btn' => '--circle-btn-size: {{SIZE}}{{UNIT}};',
				],
                'condition' => array(
                    "{$id_prefix}_btn_type" => 'muia-btn-circle',
                ),  
			]
		);
		$this->add_control(     
			"{$id_prefix}_btn_effect",
			array(
				'label'   => esc_html__( 'Effect', 'motionui-addons-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $args['default_btn_effect'],
				'options' => array(
					'muia-btn-wave'          => esc_html__( 'Wave', 'motionui-addons-for-elementor' ),
					'muia-btn-reveal'        => esc_html__( 'Reveal', 'motionui-addons-for-elementor' ),
					'muia-btn-reveal-random' => esc_html__(
						! Motionui::is_active_pro() ? 'Reveal Random (Pro ✦)' : 'Reveal Random',
						'motionui-addons-for-elementor'
					),
					// 'muia-btn-symbolab'      => esc_html__( ! Motionui::is_active_pro() ? 'Symbolab (Pro ✦)' : 'Symbolab', 'motionui-addons-for-elementor' ),
				),
			)
		);
		if(Motionui::is_active_pro()){
			$this->add_control(
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
		if($is_content_cntrols):
		$this->add_control(
			"{$id_prefix}_button_text",
			array(
				'label'       => esc_html__( 'Text', 'motionui-addons-for-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Button Text', 'motionui-addons-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
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
				'dynamic'       => array(
					'active' => true,
				),
			)
		);
		endif;
		$this->add_control(
			"{$id_prefix}_icon",
			array(
				'label'       => esc_html__( 'Icon', 'motionui-addons-for-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
			)
		);
		$this->add_control(
			"{$id_prefix}_is_stroke_icon",
			array(
				'label'       => esc_html__( 'Is it a Stroke icon ? ', 'motionui-addons-for-elementor' ),
				'label_block' => false,
				'type'        => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
                'condition' => array(
                    "{$id_prefix}_icon[value]!" => '',
                ),  
			)
		);
		$this->add_control(  
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
				'default' => 'left',
				'toggle'  => false,
                'condition' => array(
                    "{$id_prefix}_icon[value]!" => '',
                ),
			)
		);
		$this->add_responsive_control(   
			'space_between_text_icon',
			[
				'label' => esc_html__( 'Icon Spacing', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
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
				'selectors' => [
					'{{WRAPPER}} .muia-btn' => '--icon-gap: {{SIZE}}{{UNIT}};',
				],
                'condition' => array(
                    "{$id_prefix}_icon[value]!" => '',
                ),  
			]
		);
		$this->add_responsive_control(      
			'btn_icon_rotation',
			[
				'label' => esc_html__( 'Icon rotation', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [ 
					'px' => [
						'min' => -360,
						'max' => 360,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .muia-btn' => '--icon-rotation: {{SIZE}}deg;',
				],
                'condition' => array(
                    "{$id_prefix}_icon[value]!" => '',
                ),  
			]
		);
		$this->add_responsive_control(        
			'btn_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px', '%', 'rem', 'custom' ],
				'range' => [
					'em' => [  
						'min' => 0,
						'max' => 15,
						'step' => 0.01,   
					],  
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [   
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}} .muia-btn-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
				],
                'condition' => array(
                    "{$id_prefix}_icon[value]!" => '',  
                ),  
			]
		);
		$this->add_responsive_control(
			"{$id_prefix}_align_x",
			array(
				'label'       => esc_html__( 'Alignment', 'motionui-addons-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
                'separator'    => 'before', 
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
		if(!Motionui::is_active_pro()){
			$this->add_control(
				'muia_pro_btn_notice',
				array(
				'separator'    => 'before', 
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => muia_get_pronotice_html(false),
				)
			);
		}
		$this->end_controls_section();
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
	public function _render_muia_btn( $id_prefix = 'muia_btn', $args = array() ) {

		$settings = $this->get_settings_for_display();

		$btn_type = ! empty( $settings[ "{$id_prefix}_btn_type" ] )
			? $settings[ "{$id_prefix}_btn_type" ]
			: 'muia-btn-solid';

		$btn_effect = ! empty( $settings[ "{$id_prefix}_btn_effect" ] )
			? $settings[ "{$id_prefix}_btn_effect" ]
			: 'muia-btn-reveal';

		$magnetic_effect = ( isset( $settings[ "{$id_prefix}_muia_magnetic_effect" ] )
			&& 'yes' === $settings[ "{$id_prefix}_muia_magnetic_effect" ] )
			? 'muia-magnetic'
			: '';

		$button_text = ! empty( $settings[ "{$id_prefix}_button_text" ] )
			? $settings[ "{$id_prefix}_button_text" ]
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

		// Append noreferrer when opening in a new tab — security best practice.
		if ( '_blank' === $button_target ) {
			$button_rel = trim( $button_rel . ' noreferrer' );
		}

		$icon = ! empty( $settings[ "{$id_prefix}_icon" ] )
			? $settings[ "{$id_prefix}_icon" ]
			: array();

		$icon_position = ! empty( $settings[ "{$id_prefix}_icon_position_style" ] )
			? $settings[ "{$id_prefix}_icon_position_style" ]
			: 'left';

        $btn_classes = implode(' ', array_filter([
            'muia-btn',
            $btn_type,
            $btn_effect,
            $magnetic_effect,
            $is_stroke_icon,
            $icon_position == 'left' ? 'muia-icon-pos-left' : '',
            $icon_position == 'right' ? 'muia-icon-pos-right' : '',
        ]));

		$args = wp_parse_args(
			$args,
			array(
				'title'      => $button_text,
				'url'        => $button_url,
				'url_target' => $button_target,
				'url_rel'    => $button_rel,
			)
		);

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
}
