<?php
/**
 * Burger Button Widget
 *
 * Two toggle-button styles in one widget: an SVG stroke burger that morphs
 * into a cross, and a three-bar burger that folds into a cross. Both share
 * the sliding Menu/Close label.
 *
 * @package     MotionUI_Addons
 * @subpackage  Widgets
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Burger_Button
 *
 * @since 1.0.0
 */
class Burger_Button extends Muia_Base {

    /**
     * Retrieve widget keywords.
     */
    public function get_keywords() {
        return [ 'burger', 'hamburger', 'menu', 'toggle', 'nav', 'navigation', 'motionui', 'animation' ];
    }

    /**
     * Register widget controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function register_controls() {
        $this->_register_muia_burger_content_controls();
        $this->_register_muia_burger_style_controls();
    }

    /**
     * Content controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_burger_content_controls() {

        /* -------------------------------------------------- Button */
        $this->start_controls_section(
            'themeic_section_burger_content',
            [
                'label' => esc_html__( 'Burger Button', 'motionui-addons-for-elementor' ),
            ]
        );

        $this->add_control(
            'themeic_burger_type',
            [
                'label'   => esc_html__( 'Style', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'stroke',
                'options' => [
                    'stroke' => esc_html__( 'Stroke Burger', 'motionui-addons-for-elementor' ),
                    'bars'   => esc_html__( 'Bar Burger', 'motionui-addons-for-elementor' ),
                ],
            ]
        );

        $this->add_control(
            'themeic_burger_line_style',
            [
                'label'     => esc_html__( 'Line Style', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => [
                    ''             => esc_html__( 'Equal Lines', 'motionui-addons-for-elementor' ),
                    'btn-style-2'  => esc_html__( 'Short Middle', 'motionui-addons-for-elementor' ),
                    'btn-style-3'  => esc_html__( 'Stepped', 'motionui-addons-for-elementor' ),
                ],
                'condition' => [ 'themeic_burger_type' => 'stroke' ],
            ]
        );

        $this->add_control(
            'themeic_burger_aspect',
            [
                'label'     => esc_html__( 'Icon Shape', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => [
                    ''              => esc_html__( 'Square', 'motionui-addons-for-elementor' ),
                    'btn-aspect-2'  => esc_html__( 'Wide', 'motionui-addons-for-elementor' ),
                ],
                'condition' => [ 'themeic_burger_type' => 'stroke' ],
            ]
        );

        $this->add_control(
            'themeic_show_label',
            [
                'label'        => esc_html__( 'Show Label', 'motionui-addons-for-elementor' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'motionui-addons-for-elementor' ),
                'label_off'    => esc_html__( 'Hide', 'motionui-addons-for-elementor' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            ]
        );

        $this->add_control(
            'themeic_menu_text',
            [
                'label'       => esc_html__( 'Closed Label', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Menu', 'motionui-addons-for-elementor' ),
                'placeholder' => esc_html__( 'Menu', 'motionui-addons-for-elementor' ),
                'dynamic'     => [ 'active' => true ],
                'condition'   => [ 'themeic_show_label' => 'yes' ],
            ]
        );

        $this->add_control(
            'themeic_close_text',
            [
                'label'       => esc_html__( 'Open Label', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Close', 'motionui-addons-for-elementor' ),
                'placeholder' => esc_html__( 'Close', 'motionui-addons-for-elementor' ),
                'dynamic'     => [ 'active' => true ],
                'description' => esc_html__( 'Slides up to replace the closed label once the button is toggled.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_show_label' => 'yes' ],
            ]
        );

        $this->add_control(
            'themeic_label_position',
            [
                'label'     => esc_html__( 'Label Position', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'after',
                'options'   => [
                    'before' => esc_html__( 'Before Icon', 'motionui-addons-for-elementor' ),
                    'after'  => esc_html__( 'After Icon', 'motionui-addons-for-elementor' ),
                ],
                'condition' => [ 'themeic_show_label' => 'yes' ],
            ]
        );

        $this->add_control(
            'themeic_burger_target',
            [
                'label'       => esc_html__( 'Toggle Target', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => '#main-menu',
                'description' => esc_html__( 'Optional CSS selector. The is-open class is added to it alongside the button, so a menu can open with the same click.', 'motionui-addons-for-elementor' ),
                'separator'   => 'before',
            ]
        );

        $this->add_responsive_control(
            'themeic_burger_align',
            [
                'label'     => esc_html__( 'Alignment', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'motionui-addons-for-elementor' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'motionui-addons-for-elementor' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'motionui-addons-for-elementor' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .themeic-burger-btn-wrap' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Style controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_burger_style_controls() {

        /* -------------------------------------------------- Button */
        $this->start_controls_section(
            'themeic_section_burger_style',
            [
                'label' => esc_html__( 'Button', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // font-size is the root of both buttons: the icon box, its lines and
        // the gap are all sized in em, so this one slider scales the whole
        // control. Both classes are targeted because only one ever renders.
        $this->add_responsive_control(
            'themeic_burger_size',
            [
                'label'      => esc_html__( 'Size', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px'  => [ 'min' => 10, 'max' => 120 ],
                    'em'  => [ 'min' => 0.5, 'max' => 8, 'step' => 0.1 ],
                    'rem' => [ 'min' => 0.5, 'max' => 8, 'step' => 0.1 ],
                ],
                // Matches the 35px the stylesheet already applies.
                'default'    => [
                    'unit' => 'px',
                    'size' => 35,
                ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-burger-btn, {{WRAPPER}} .themeic-stroke-burger-menu-btn' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'themeic_burger_gap',
            [
                'label'      => esc_html__( 'Icon Spacing', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'em', 'px' ],
                'range'      => [
                    'em' => [ 'min' => 0, 'max' => 3, 'step' => 0.05 ],
                    'px' => [ 'min' => 0, 'max' => 60 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-burger-btn, {{WRAPPER}} .themeic-stroke-burger-menu-btn' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'themeic_tabs_burger_colors' );

        $this->start_controls_tab(
            'themeic_tab_burger_normal',
            [
                'label' => esc_html__( 'Closed', 'motionui-addons-for-elementor' ),
            ]
        );

        // The SVG strokes and the bars both paint with currentColor, so one
        // control colours the icon and the label together.
        $this->add_control(
            'themeic_burger_color',
            [
                'label'     => esc_html__( 'Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .themeic-burger-btn, {{WRAPPER}} .themeic-stroke-burger-menu-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'themeic_tab_burger_open',
            [
                'label' => esc_html__( 'Open', 'motionui-addons-for-elementor' ),
            ]
        );

        $this->add_control(
            'themeic_burger_open_color',
            [
                'label'     => esc_html__( 'Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .themeic-burger-btn.is-open, {{WRAPPER}} .themeic-stroke-burger-menu-btn.is-open' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'themeic_burger_padding',
            [
                'label'      => esc_html__( 'Padding', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-burger-btn, {{WRAPPER}} .themeic-stroke-burger-menu-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator'  => 'before',
            ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------- Icon */
        $this->start_controls_section(
            'themeic_section_burger_icon_style',
            [
                'label' => esc_html__( 'Icon', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Each style names its thickness variable differently; both are set so
        // the control keeps working when the style is switched.
        $this->add_responsive_control(
            'themeic_burger_thickness',
            [
                'label'      => esc_html__( 'Line Thickness', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range'      => [
                    'px' => [ 'min' => 1, 'max' => 12, 'step' => 0.5 ],
                    'em' => [ 'min' => 0.02, 'max' => 0.4, 'step' => 0.01 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-stroke-burger-menu-btn' => '--border-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .themeic-burger-btn'             => '--border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'themeic_burger_icon_width',
            [
                'label'      => esc_html__( 'Icon Width', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'em', 'px' ],
                'range'      => [
                    'em' => [ 'min' => 0.5, 'max' => 4, 'step' => 0.05 ],
                    'px' => [ 'min' => 10, 'max' => 120 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-burger-btn .burger-icon' => '--icon-width: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [ 'themeic_burger_type' => 'bars' ],
            ]
        );

        $this->add_responsive_control(
            'themeic_burger_icon_height',
            [
                'label'      => esc_html__( 'Icon Height', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'em', 'px' ],
                'range'      => [
                    'em' => [ 'min' => 0.4, 'max' => 3, 'step' => 0.05 ],
                    'px' => [ 'min' => 8, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-burger-btn .burger-icon' => '--icon-height: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [ 'themeic_burger_type' => 'bars' ],
            ]
        );

        $this->add_control(
            'themeic_burger_line_radius',
            [
                'label'      => esc_html__( 'Line Radius', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px'  => [ 'min' => 0, 'max' => 20 ],
                    'em'  => [ 'min' => 0, 'max' => 2, 'step' => 0.05 ],
                    'rem' => [ 'min' => 0, 'max' => 20, 'step' => 0.1 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .themeic-burger-btn' => '--line-round: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [ 'themeic_burger_type' => 'bars' ],
            ]
        );

        $this->add_responsive_control(
            'themeic_burger_icon_scale',
            [
                'label'       => esc_html__( 'Icon Scale', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::SLIDER,
                'size_units'  => [ 'em' ],
                'range'       => [
                    'em' => [ 'min' => 0.5, 'max' => 3, 'step' => 0.01 ],
                ],
                'default'     => [
                    'unit' => 'em',
                    'size' => 1.566,
                ],
                'description' => esc_html__( 'The box the morphing strokes are drawn in.', 'motionui-addons-for-elementor' ),
                'selectors'   => [
                    '{{WRAPPER}} .themeic-stroke-burger-menu-btn .stroke-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition'   => [ 'themeic_burger_type' => 'stroke' ],
            ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------- Label */
        $this->start_controls_section(
            'themeic_section_burger_label_style',
            [
                'label'     => esc_html__( 'Label', 'motionui-addons-for-elementor' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [ 'themeic_show_label' => 'yes' ],
            ]
        );

        // Set on the wrapper rather than on the two labels, so both the closed
        // and the open text share one set of values and stay the same height —
        // the slide effect depends on them matching.
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'themeic_burger_label_typography',
                'selector' => '{{WRAPPER}} .menu-text-wrap',
            ]
        );

        $this->add_control(
            'themeic_burger_label_color',
            [
                'label'     => esc_html__( 'Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .menu-text-wrap' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     *
     * @since  1.0.0
     * @return void
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $is_stroke  = 'bars' !== $settings['themeic_burger_type'];
        $has_label  = 'yes' === $settings['themeic_show_label'];
        $menu_text  = isset( $settings['themeic_menu_text'] ) ? $settings['themeic_menu_text'] : '';
        $close_text = isset( $settings['themeic_close_text'] ) ? $settings['themeic_close_text'] : '';

        $this->add_render_attribute( 'button', [
            'class'         => $is_stroke ? 'themeic-stroke-burger-menu-btn' : 'themeic-burger-btn',
            'type'          => 'button',
            'aria-expanded' => 'false',
            // Read by the frontend script, which toggles is-open here and on
            // the target, and keeps aria-expanded in step.
            'data-muia-burger' => '',
        ] );

        if ( $is_stroke ) {
            foreach ( [ 'themeic_burger_line_style', 'themeic_burger_aspect' ] as $modifier ) {
                if ( ! empty( $settings[ $modifier ] ) ) {
                    $this->add_render_attribute( 'button', 'class', $settings[ $modifier ] );
                }
            }
        }

        if ( ! empty( $settings['themeic_burger_target'] ) ) {
            $this->add_render_attribute( 'button', 'data-muia-target', $settings['themeic_burger_target'] );
        }

        // With no visible label the button still needs an accessible name.
        if ( ! $has_label ) {
            $this->add_render_attribute( 'button', 'aria-label', esc_attr__( 'Toggle menu', 'motionui-addons-for-elementor' ) );
        }
        ?>
        <div class="themeic-burger-btn-wrap">
            <button <?php $this->print_render_attribute_string( 'button' ); ?>>

                <?php if ( $has_label && 'before' === $settings['themeic_label_position'] ) : ?>
                    <?php $this->render_burger_label( $menu_text, $close_text ); ?>
                <?php endif; ?>

                <?php if ( $is_stroke ) : ?>
                    <span class="stroke-wrap" aria-hidden="true">
                        <svg viewBox="0 0 100 100">
                            <path class="stroke-line stroke-line-top" d="m 70,33 h -40 c 0,0 -8.5,-0.149796 -8.5,8.5 0,8.649796 8.5,8.5 8.5,8.5 h 20 v -20"></path>
                            <path class="stroke-line stroke-line-middle" d="m 70,50 h -40"></path>
                            <path class="stroke-line stroke-line-bottom" d="m 30,67 h 40 c 0,0 8.5,0.149796 8.5,-8.5 0,-8.649796 -8.5,-8.5 -8.5,-8.5 h -20 v 20"></path>
                        </svg>
                    </span>
                <?php else : ?>
                    <span class="burger-icon" aria-hidden="true">
                        <span></span><span></span><span></span>
                    </span>
                <?php endif; ?>

                <?php if ( $has_label && 'before' !== $settings['themeic_label_position'] ) : ?>
                    <?php $this->render_burger_label( $menu_text, $close_text ); ?>
                <?php endif; ?>

            </button>
        </div>
        <?php
    }

    /**
     * The sliding Menu/Close label.
     *
     * @since  1.0.0
     * @param  string $menu_text  Label shown while closed.
     * @param  string $close_text Label shown while open.
     * @return void
     */
    protected function render_burger_label( $menu_text, $close_text ) {
        ?>
        <span class="menu-text-wrap">
            <span class="menu-text"><?php echo esc_html( $menu_text ); ?></span>
            <span class="close-text"><?php echo esc_html( $close_text ); ?></span>
        </span>
        <?php
    }
}
