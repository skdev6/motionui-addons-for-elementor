<?php
/**
 * Slide Controls Trait
 *
 * @package     CsfCore
 * @subpackage  Traits
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace CsfCore\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trait Slide_Controls
 *
 * Reusable slider controls and markup for any widget built on
 * `ThemeicComponents.themeicSlide()`.
 *
 * The JS reads its configuration from three places, and every control here
 * feeds one of them:
 *
 * 1. CSS custom properties on `.themeic-slide-wrapper`, read through
 *    `readPx()` — `--slide-per-view`, `--slide-gap`, `--slide-speed`,
 *    `--thum-width-percent`, `--slide-h` and `--slide-loop-extra`. Elementor
 *    writes these through `selectors`, so they stay responsive for free.
 * 2. `data-slide-settings` on the wrapper: a JSON object of everything that
 *    cannot be a custom property — the effect, looping and autoplay. See
 *    `muia_slide_settings()`.
 * 3. The markup itself — the presence of the arrow buttons, and
 *    `data-pagination` on the pagination element.
 *
 * Anything the JS cannot act on is deliberately absent: there is no vertical
 * mode and no scroll-direction option.
 *
 * Usage:
 *
 *     use Slide_Controls;
 *
 *     protected function register_controls() {
 *         $this->muia_slide_controls();
 *         $this->muia_slide_nav_controls();
 *         $this->muia_slide_style_controls();
 *     }
 *
 *     protected function render() {
 *         $s = $this->get_settings_for_display();
 *         ?>
 *         <div class="themeic-slide-wrapper" <?php $this->muia_slide_wrapper_attributes( 'slide', $s ); ?>>
 *             <div class="swiper <?php echo esc_attr( $this->muia_slide_track_classes( 'slide', $s ) ); ?>">
 *                 <div class="swiper-wrapper"><!-- slides --></div>
 *             </div>
 *             <?php $this->muia_render_slide_nav( 'slide', $s ); ?>
 *         </div>
 *         <?php
 *     }
 *
 * Every method takes a `$prefix` so one widget can host more than one slider.
 *
 * @since 1.0.0
 */
trait Slide_Controls {

	/**
	 * Slider layout and behaviour controls (Content tab).
	 *
	 * @param string $prefix Control ID prefix.
	 * @param array  $args   Overrides. Set any feature flag to false to omit
	 *                       that control; `*_default` keys change defaults.
	 * @return void
	 */
	public function muia_slide_controls( $prefix = 'slide', $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'title'                => esc_html__( 'Slider', 'csf-core' ),
				'condition'            => array(),
				'wrapper'              => '{{WRAPPER}} .themeic-slide-wrapper',
				'per_view'             => true,
				'gap'                  => true,
				'height'               => true,
				'speed'                => true,
				'effect'               => true,
				'thumb_width'          => true,
				'loop'                 => true,
				'autoplay'             => true,
				'per_view_default'     => 3,
				'gap_default'          => 16,
				'height_default'       => 32,
				'speed_default'        => 600,
				'thumb_width_default'  => 50,
				'effect_default'       => 'slide',
				'loop_default'         => 'yes',
				'autoplay_default'     => '',
				'autoplay_delay_default' => 3,
			)
		);

		$wrapper = $args['wrapper'];

		$this->start_controls_section(
			$prefix . '_section_slider',
			array(
				'label'     => $args['title'],
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => $args['condition'],
			)
		);

		if ( $args['effect'] ) {
			$this->add_control(
				$prefix . '_effect',
				array(
					'label'       => esc_html__( 'Effect', 'csf-core' ),
					'description' => esc_html__( 'Overlap stacks the inactive slides behind the active one. The rest are Swiper transitions.', 'csf-core' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $args['effect_default'],
					'options'     => $this->muia_slide_effect_options(),
					'render_type' => 'template',
				)
			);
		}

		if ( $args['per_view'] ) {
			$this->add_responsive_control(
				$prefix . '_per_view',
				array(
					'label'       => esc_html__( 'Slides Per View', 'csf-core' ),
					'description' => esc_html__( 'In overlap mode this sets how wide the active slide is: 3 makes it a third of the widget.', 'csf-core' ),
					'type'        => Controls_Manager::SELECT,
					// Cast: option keys are strings, and widgets pass ints.
					'default'     => (string) $args['per_view_default'],
					'options' => [
						'1' => esc_html__( '1', 'csf-core' ),
						'2'  => esc_html__( '2', 'csf-core' ),
						'3' => esc_html__( '3', 'csf-core' ),
						'4' => esc_html__( '4', 'csf-core' ),
						'5' => esc_html__( '5', 'csf-core' ),
						'6' => esc_html__( '6', 'csf-core' ),
						'7' => esc_html__( '7', 'csf-core' ),
						'8' => esc_html__( '8', 'csf-core' ),
						'9' => esc_html__( '9', 'csf-core' ),
					],
					'selectors'   => array(
						$wrapper => '--slide-per-view: {{VALUE}};',
					),
				)
			);
		}

		if ( $args['thumb_width'] ) {
			$this->add_responsive_control(
				$prefix . '_thumb_width',
				array(
					'label'       => esc_html__( 'Collapsed Slide Width', 'csf-core' ),
					'description' => esc_html__( 'Width of the stacked slides, as a percentage of the active one.', 'csf-core' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => (string) $args['thumb_width_default'],
					'options' => [
						'20'  => esc_html__( '20%', 'csf-core' ),
						'30' => esc_html__( '30%', 'csf-core' ),
						'40' => esc_html__( '40%', 'csf-core' ),
						'50' => esc_html__( '50%', 'csf-core' ),
						'60' => esc_html__( '60%', 'csf-core' ),
						'70' => esc_html__( '70%', 'csf-core' ),
						'80' => esc_html__( '80%', 'csf-core' ),
						'90' => esc_html__( '90%', 'csf-core' ),
					],
					'selectors'   => array(
						// Unitless: readPx() parses the number and clamps it.
						$wrapper => '--thum-width-percent: {{VALUE}};',
					),
					'condition'   => $args['effect'] ? array( $prefix . '_effect' => 'overlay' ) : array(),
				)
			);
		}

		if ( $args['gap'] ) {
			$this->add_responsive_control(
				$prefix . '_gap',
				array(
					'label'      => esc_html__( 'Gap Between Slides', 'csf-core' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'rem' ),
					'range'      => array(
						'px'  => array(
							'min' => 0,
							'max' => 120,
						),
						'rem' => array(
							'min'  => 0,
							'max'  => 8,
							'step' => 0.1,
						),
					),
					'default'    => array(
						'unit' => 'px',
						'size' => $args['gap_default'],
					),
					'selectors'  => array(
						$wrapper => '--slide-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);
		}

		if ( $args['height'] ) {
			$this->add_responsive_control(
				$prefix . '_height',
				array(
					'label'      => esc_html__( 'Slide Height', 'csf-core' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'rem', 'px', 'vh' , 'custom'),
					'range'      => array(
						'rem' => array(
							'min'  => 8,
							'max'  => 60,
							'step' => 0.5,
						),
						'px'  => array(
							'min' => 120,
							'max' => 900,
						),
						'vh'  => array(
							'min' => 20,
							'max' => 100,
						),
					),
					'default'    => array(
						'unit' => 'rem',
						'size' => $args['height_default'],
					),
					'selectors'  => array(
						$wrapper => '--slide-h: {{SIZE}}{{UNIT}};',
					),
				)
			);
		}

		if ( $args['speed'] ) {
			$this->add_control(
				$prefix . '_speed',
				array(
					'label'       => esc_html__( 'Transition Speed', 'csf-core' ),
					'description' => esc_html__( 'Milliseconds.', 'csf-core' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'px' ),
					'range'       => array(
						'px' => array(
							'min'  => 100,
							'max'  => 3000,
							'step' => 50,
						),
					),
					'default'     => array( 'size' => $args['speed_default'] ),
					'selectors'   => array(
						$wrapper => '--slide-speed: {{SIZE}}ms;',
					),
				)
			);
		}

		if ( $args['loop'] ) {
			$this->add_control(
				$prefix . '_loop',
				array(
					'label'        => esc_html__( 'Infinite Loop', 'csf-core' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'csf-core' ),
					'label_off'    => esc_html__( 'No', 'csf-core' ),
					'return_value' => 'yes',
					'default'      => $args['loop_default'],
					'render_type'  => 'template',
				)
			);

			$this->add_responsive_control(
				$prefix . '_loop_extra',
				array(
					'label'       => esc_html__( 'Extra Looped Slides', 'csf-core' ),
					'description' => esc_html__( 'How many clones Swiper keeps either side of the track. Leave empty to work it out from Slides Per View.', 'csf-core' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'custom' ),
					'range'       => array(
						'custom' => array(
							'min'  => 0,
							'max'  => 12,
							'step' => 1,
						),
					),
					'selectors'   => array(
						// Unitless: readPx() parses the bare number, and an
						// unset token is what tells the JS to derive one.
						$wrapper => '--slide-loop-extra: {{SIZE}};',
					),
					'condition'   => array( $prefix . '_loop' => 'yes' ),
				)
			);
		}

		if ( $args['autoplay'] ) {
			$this->add_control(
				$prefix . '_autoplay',
				array(
					'label'        => esc_html__( 'Autoplay', 'csf-core' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'csf-core' ),
					'label_off'    => esc_html__( 'No', 'csf-core' ),
					'return_value' => 'yes',
					'default'      => $args['autoplay_default'],
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				$prefix . '_autoplay_delay',
				array(
					'label'       => esc_html__( 'Autoplay Delay', 'csf-core' ),
					'description' => esc_html__( 'Seconds each slide stays put.', 'csf-core' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => array( 'custom' ),
					'range'       => array(
						'custom' => array(
							'min'  => 0.5,
							'max'  => 15,
							'step' => 0.5,
						),
					),
					'default'     => array( 'size' => $args['autoplay_delay_default'] ),
					'condition'   => array( $prefix . '_autoplay' => 'yes' ),
					'render_type' => 'template',
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Arrow and pagination controls (Content tab).
	 *
	 * @param string $prefix Control ID prefix.
	 * @param array  $args   Overrides.
	 * @return void
	 */
	public function muia_slide_nav_controls( $prefix = 'slide', $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'title'                   => esc_html__( 'Navigation', 'csf-core' ),
				'condition'               => array(),
				'arrows'                  => true,
				'pagination'              => true,
				'arrows_default'          => 'yes',
				'pagination_default'      => 'yes',
				'pagination_type_default' => 'progressbar',
			)
		);

		$this->start_controls_section(
			$prefix . '_section_nav',
			array(
				'label'     => $args['title'],
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => $args['condition'],
			)
		);

		if ( $args['arrows'] ) {
			$this->add_control(
				$prefix . '_arrows',
				array(
					'label'        => esc_html__( 'Arrows', 'csf-core' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'csf-core' ),
					'label_off'    => esc_html__( 'Hide', 'csf-core' ),
					'return_value' => 'yes',
					'default'      => $args['arrows_default'],
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				$prefix . '_prev_icon',
				array(
					'label'       => esc_html__( 'Previous Icon', 'csf-core' ),
					'description' => esc_html__( 'Leave empty to use the built-in arrow.', 'csf-core' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'condition'   => array( $prefix . '_arrows' => 'yes' ),
				)
			);

			$this->add_control(
				$prefix . '_next_icon',
				array(
					'label'       => esc_html__( 'Next Icon', 'csf-core' ),
					'description' => esc_html__( 'Leave empty to use the built-in arrow.', 'csf-core' ),
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'condition'   => array( $prefix . '_arrows' => 'yes' ),
				)
			);
		}

		if ( $args['pagination'] ) {
			$this->add_control(
				$prefix . '_pagination',
				array(
					'label'        => esc_html__( 'Pagination', 'csf-core' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Show', 'csf-core' ),
					'label_off'    => esc_html__( 'Hide', 'csf-core' ),
					'return_value' => 'yes',
					'default'      => $args['pagination_default'],
					'separator'    => 'before',
					'render_type'  => 'template',
				)
			);

			$this->add_control(
				$prefix . '_pagination_type',
				array(
					'label'       => esc_html__( 'Pagination Type', 'csf-core' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => $args['pagination_type_default'],
					'options'     => array(
						'progressbar' => esc_html__( 'Progress Bar', 'csf-core' ),
						'bullets'     => esc_html__( 'Bullets', 'csf-core' ),
						'fraction'    => esc_html__( 'Fraction', 'csf-core' ),
					),
					'condition'   => array( $prefix . '_pagination' => 'yes' ),
					'render_type' => 'template',
				)
			);
		}

		$this->add_responsive_control(
			$prefix . '_nav_align',
			array(
				'label'     => esc_html__( 'Alignment', 'csf-core' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'csf-core' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'csf-core' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'csf-core' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .themeic-slide-nav' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Arrow and pagination styling (Style tab).
	 *
	 * @param string $prefix Control ID prefix.
	 * @param array  $args   Overrides.
	 * @return void
	 */
	public function muia_slide_style_controls( $prefix = 'slide', $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'title'     => esc_html__( 'Slider Navigation', 'csf-core' ),
				'condition' => array(),
			)
		);

		// Both classes, to outrank the `.arrow-circle-btn:hover` rule in
		// _buttons.scss. That stylesheet styles the button with plain
		// properties, not custom properties, so these write the same.
		$arrow       = '{{WRAPPER}} .arrow-circle-btn.themeic-slide-btn';
		$arrow_hover = '{{WRAPPER}} .arrow-circle-btn.themeic-slide-btn:hover, {{WRAPPER}} .arrow-circle-btn.themeic-slide-btn:focus-visible';
		$pagination  = '{{WRAPPER}} .themeic-slide-pagination';

		$this->start_controls_section(
			$prefix . '_section_nav_style',
			array(
				'label'     => $args['title'],
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $args['condition'],
			)
		);

		$this->add_control(
			$prefix . '_arrows_heading',
			array(
				'label' => esc_html__( 'Arrows', 'csf-core' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			$prefix . '_arrow_radius',
			array(
				'label'       => esc_html__( 'Border Radius', 'csf-core' ),
				'description' => esc_html__( '50% keeps the button a circle.', 'csf-core' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( '%', 'px', 'rem' ),
				'selectors'   => array(
					$arrow => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => $prefix . '_arrow_border',
				'selector' => $arrow,
			)
		);
		$this->start_controls_tabs( $prefix . '_arrow_tabs' );

		$this->start_controls_tab(
			$prefix . '_arrow_tab_normal',
			array( 'label' => esc_html__( 'Normal', 'csf-core' ) )
		);

		$this->add_control(
			$prefix . '_arrow_color',
			array(
				'label'     => esc_html__( 'Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $arrow => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			$prefix . '_arrow_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $arrow => 'background-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$prefix . '_arrow_tab_hover',
			array( 'label' => esc_html__( 'Hover', 'csf-core' ) )
		);

		$this->add_control(
			$prefix . '_arrow_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $arrow_hover => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			$prefix . '_arrow_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $arrow_hover => 'background-color: {{VALUE}};' ),
			)
		);

		// The border group writes border-color on the non-hover selector, which
		// outranks the stylesheet's `.arrow-circle-btn:hover` rule. Without a
		// hover colour here, setting a border would freeze it on hover.
		$this->add_control(
			$prefix . '_arrow_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $arrow_hover => 'border-color: {{VALUE}};' ),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			$prefix . '_pagination_heading',
			array(
				'label'     => esc_html__( 'Pagination', 'csf-core' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$prefix . '_pagination_color',
			array(
				'label'     => esc_html__( 'Track / Inactive Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$pagination . '.swiper-pagination-progressbar' => 'background-color: {{VALUE}};',
					$pagination . ' .swiper-pagination-bullet'     => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$prefix . '_pagination_active_color',
			array(
				'label'     => esc_html__( 'Fill / Active Color', 'csf-core' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$pagination . ' .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
					$pagination . ' .swiper-pagination-bullet-active'    => 'background-color: {{VALUE}};',
					$pagination . '.swiper-pagination-fraction'          => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			$prefix . '_pagination_height',
			array(
				'label'      => esc_html__( 'Progress Bar Height', 'csf-core' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors'  => array(
					$pagination . '.swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array( $prefix . '_pagination_type' => 'progressbar' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => $prefix . '_pagination_typography',
				'selector'  => $pagination . '.swiper-pagination-fraction',
				'condition' => array( $prefix . '_pagination_type' => 'fraction' ),
			)
		);

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * Render helpers
	 * ------------------------------------------------------------------ */

	/**
	 * The transitions the Effect control offers.
	 *
	 * `overlay` is this plugin's own stacking layout, not a Swiper effect —
	 * themeicSlide() places the slides itself for that one. Everything else is
	 * a Swiper effect, and every one of them ships in the bundled Swiper 11
	 * build along with the CSS it needs.
	 *
	 * @return array<string, string>
	 */
	public function muia_slide_effect_options() {

		return array(
			'slide'     => esc_html__( 'Slide', 'csf-core' ),
			'overlay'   => esc_html__( 'Overlap', 'csf-core' ),
			'fade'      => esc_html__( 'Fade', 'csf-core' ),
			'cube'      => esc_html__( 'Cube', 'csf-core' ),
			'coverflow' => esc_html__( 'Coverflow', 'csf-core' ),
			'flip'      => esc_html__( 'Flip', 'csf-core' ),
			'cards'     => esc_html__( 'Cards', 'csf-core' ),
			'creative'  => esc_html__( 'Creative', 'csf-core' ),
		);
	}

	/**
	 * The slider options that cannot be expressed as CSS.
	 *
	 * Slides per view, gap, speed and height reach the JS as custom properties
	 * written by Elementor from the `selectors` above; only what is left over
	 * travels in the data attribute.
	 *
	 * @param string $prefix   Control ID prefix.
	 * @param array  $settings Widget settings.
	 * @return array
	 */
	public function muia_slide_settings( $prefix = 'slide', $settings = array() ) {

		$effect = ! empty( $settings[ $prefix . '_effect' ] ) ? $settings[ $prefix . '_effect' ] : 'slide';

		// A widget that hides the Effect control, or a stale saved value, must
		// not reach Swiper as an effect name it does not know.
		if ( ! array_key_exists( $effect, $this->muia_slide_effect_options() ) ) {
			$effect = 'slide';
		}

		$data = array(
			'effect'   => $effect,
			'loop'     => isset( $settings[ $prefix . '_loop' ] ) && 'yes' === $settings[ $prefix . '_loop' ],
			'autoplay' => isset( $settings[ $prefix . '_autoplay' ] ) && 'yes' === $settings[ $prefix . '_autoplay' ],
		);

		if ( $data['autoplay'] ) {
			$delay = isset( $settings[ $prefix . '_autoplay_delay' ]['size'] )
				? (float) $settings[ $prefix . '_autoplay_delay' ]['size']
				: 3;

			$data['autoplayDelay'] = (int) round( $delay * 1000 );
		}

		return $data;
	}

	/**
	 * Print the data attributes `main.js` reads when it boots the slider.
	 *
	 * @param string $prefix   Control ID prefix.
	 * @param array  $settings Widget settings.
	 * @return void
	 */
	public function muia_slide_wrapper_attributes( $prefix = 'slide', $settings = array() ) {

		printf(
			' data-slider="themeic" data-slide-settings="%s"',
			esc_attr( wp_json_encode( $this->muia_slide_settings( $prefix, $settings ) ) )
		);
	}

	/**
	 * Extra classes for the `.swiper` track element.
	 *
	 * @param string $prefix   Control ID prefix.
	 * @param array  $settings Widget settings.
	 * @return string
	 */
	public function muia_slide_track_classes( $prefix = 'slide', $settings = array() ) {

		$classes = array();

		// themeicSlide() opts into looping off this class.
		if ( isset( $settings[ $prefix . '_loop' ] ) && 'yes' === $settings[ $prefix . '_loop' ] ) {
			$classes[] = 'is-slide-loop';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Render the arrow and pagination row.
	 *
	 * themeicSlide() only wires up the modules whose elements exist, so a
	 * slider with both switches off simply gets no nav markup.
	 *
	 * @param string $prefix   Control ID prefix.
	 * @param array  $settings Widget settings.
	 * @return void
	 */
	public function muia_render_slide_nav( $prefix = 'slide', $settings = array() ) {

		$show_arrows     = isset( $settings[ $prefix . '_arrows' ] ) && 'yes' === $settings[ $prefix . '_arrows' ];
		$show_pagination = isset( $settings[ $prefix . '_pagination' ] ) && 'yes' === $settings[ $prefix . '_pagination' ];

		if ( ! $show_arrows && ! $show_pagination ) {
			return;
		}

		$pagination_type = ! empty( $settings[ $prefix . '_pagination_type' ] )
			? $settings[ $prefix . '_pagination_type' ]
			: 'progressbar';
		?>
		<div class="themeic-slide-nav">

			<?php if ( $show_arrows ) : ?>

				<button class="arrow-circle-btn themeic-slide-btn themeic-slide-prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'csf-core' ); ?>">
					<?php $this->muia_slide_arrow_icon( $settings, $prefix . '_prev_icon', 'prev' ); ?>
				</button>

				<button class="arrow-circle-btn themeic-slide-btn themeic-slide-next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'csf-core' ); ?>">
					<?php $this->muia_slide_arrow_icon( $settings, $prefix . '_next_icon', 'next' ); ?>
				</button>

			<?php endif; ?>

			<?php if ( $show_pagination ) : ?>
				<div class="themeic-slide-pagination" data-pagination="<?php echo esc_attr( $pagination_type ); ?>"></div>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Print an arrow icon, falling back to the built-in SVG.
	 *
	 * @param array  $settings    Widget settings.
	 * @param string $control_id  Icon control ID.
	 * @param string $direction   'prev' or 'next'.
	 * @return void
	 */
	protected function muia_slide_arrow_icon( $settings, $control_id, $direction ) {

		if ( ! empty( $settings[ $control_id ]['value'] ) ) {
			Icons_Manager::render_icon( $settings[ $control_id ], array( 'aria-hidden' => 'true' ) );
			return;
		}

		$path = 'prev' === $direction
			? 'M6 1L1 6L6 11M1 6H15'
			: 'M10 1L15 6L10 11M15 6H1';

		printf(
			'<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="%s" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			esc_attr( $path )
		);
	}
}