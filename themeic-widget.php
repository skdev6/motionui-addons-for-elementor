<?php
/**
 * Widget Name: Minimal Button
 * Description: A simple button widget for MotionUI Addons.
 * Version: 1.0.0
 * Author: Themeic
 */

namespace Themeic\CustomWidget;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Themeic_Minimal_Button_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'themeic-minimal-button';
    }

    public function get_title() {
        return esc_html__( 'Minimal Button', 'motionui-addons-for-elementor' );
    }

    public function get_icon() {
        return 'eicon-button themeic-icon';
    }

    public function get_categories() {
        return [ 'motionui_addons' ];
    }

    public function get_keywords() {
        return [ 'button', 'link', 'themeic' ];
    }

    public function get_style_depends() {
        return [ 'themeic-minimal-button' ];
    }
    public function get_themeic_demo_url() {
        return 'https://themeic.com/';
    }
    public function get_themeic_tutorial_url() {
        return 'https://themeic.com/';
    }
    
    protected function register_controls() {

        // Content tab.
        $this->start_controls_section( 'section_button', [
            'label' => esc_html__( 'Button', 'motionui-addons-for-elementor' ),
        ] );

        $this->add_control( 'button_text', [
            'label'   => esc_html__( 'Text', 'motionui-addons-for-elementor' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__( 'Click Me', 'motionui-addons-for-elementor' ),
        ] );

        $this->add_control( 'button_link', [
            'label'   => esc_html__( 'Link', 'motionui-addons-for-elementor' ),
            'type'    => \Elementor\Controls_Manager::URL,
            'default' => [ 'url' => '#' ],
        ] );

        $this->add_responsive_control( 'align', [
            'label'   => esc_html__( 'Alignment', 'motionui-addons-for-elementor' ),
            'type'    => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left'   => [ 'title' => esc_html__( 'Left', 'motionui-addons-for-elementor' ), 'icon' => 'eicon-text-align-left' ],
                'center' => [ 'title' => esc_html__( 'Center', 'motionui-addons-for-elementor' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => esc_html__( 'Right', 'motionui-addons-for-elementor' ), 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [
                '{{WRAPPER}} .themeic-minimal-button-wrap' => 'text-align: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

        // Style tab.
        $this->start_controls_section( 'section_style', [
            'label' => esc_html__( 'Button Style', 'motionui-addons-for-elementor' ),
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'text_color', [
            'label'     => esc_html__( 'Text Color', 'motionui-addons-for-elementor' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .themeic-minimal-button' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'bg_color', [
            'label'     => esc_html__( 'Background Color', 'motionui-addons-for-elementor' ),
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .themeic-minimal-button' => 'background-color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'button_typography',
            'selector' => '{{WRAPPER}} .themeic-minimal-button',
        ] );

        $this->add_control( 'border_radius', [
            'label'      => esc_html__( 'Border Radius', 'motionui-addons-for-elementor' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'selectors'  => [
                '{{WRAPPER}} .themeic-minimal-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'padding', [
            'label'      => esc_html__( 'Padding', 'motionui-addons-for-elementor' ),
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'selectors'  => [
                '{{WRAPPER}} .themeic-minimal-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'button', 'class', 'themeic-minimal-button' );

        if ( ! empty( $settings['button_link']['url'] ) ) {
            $this->add_link_attributes( 'button', $settings['button_link'] );
        }
        ?>
        <div class="themeic-minimal-button-wrap">
            <a <?php $this->print_render_attribute_string( 'button' ); ?>>
                <?php echo esc_html( $settings['button_text'] ); ?>
            </a>
        </div>
        <?php
    }
}