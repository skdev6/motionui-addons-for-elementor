<?php
/**
 * Slide Controls Trait
 *
 * @package     MotionUI_Addons
 * @subpackage  Traits
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Traits;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Slide_Controls {

    public function muia_slide_settings_controls( $prefix = 'muia_slide_', $args = array() ) {    

        $args = wp_parse_args( $args, array(
            'title'                  => esc_html__( 'Slide Settings', 'motionui-addons-for-elementor' ),
            'condition'              => array(),
            'speed'                  => false,
            'autoplay'               => true,
            'autoplay_speed'         => true,
            'loop'                   => true,
            'vertical'               => true,
            'transition'             => true,
            'navigation'             => true,
            'arrow_icons'            => true,
            'scroll_direction'       => false,
            'scroll_direction_default'       => 'yes',
            'speed_default'          => 300,
            'autoplay_default'       => 'yes',
            'autoplay_speed_default' => 2,
            'loop_default'           => 'yes',
            'navigation_default'     => 'arrow',
            'transition_default'     => 'slide',
        ) );

        $this->start_controls_section(
            $prefix . 'section_settings',
            [
                'label'     => $args['title'],
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => $args['condition'],
            ]
        );

        if ( $args['speed'] ) {
            $this->add_control(
                $prefix . 'speed',
                [
                    'label'              => esc_html__( 'Animation Speed', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::NUMBER,
                    'min'                => 100,
                    'step'               => 10,
                    'max'                => 60000,
                    'default'            => $args['speed_default'],
                    'description'        => esc_html__( 'Slide speed in milliseconds', 'motionui-addons-for-elementor' ),
                    'frontend_available' => true,
                    'label_block' => true
                ]
            );
        }

        if ( $args['autoplay'] ) {
            $this->add_control(
                $prefix . 'autoplay',
                [
                    'label'              => esc_html__( 'Autoplay?', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::SWITCHER,
                    'label_on'           => esc_html__( 'Yes', 'motionui-addons-for-elementor' ),
                    'label_off'          => esc_html__( 'No', 'motionui-addons-for-elementor' ),
                    'return_value'       => 'yes',
                    'default'            => $args['autoplay_default'],
                    'frontend_available' => true,
                    
                ]
            );
        }
        if ( $args['scroll_direction'] ) {
            $this->add_control(
                $prefix . 'scroll_direction',
                [
                    'label'              => esc_html__( 'Change direction with scroll?', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::SWITCHER,
                    'label_on'           => esc_html__( 'Yes', 'motionui-addons-for-elementor' ),
                    'label_off'          => esc_html__( 'No', 'motionui-addons-for-elementor' ),
                    'return_value'       => 'yes',
                    'default'            => $args['scroll_direction_default'],
                    'frontend_available' => true,
                    'condition'=>[
                        $prefix . 'autoplay'=>'yes'
                    ]
                    
                ]
            );
        }

        if ( $args['autoplay_speed'] ) {
            $this->add_control(
                $prefix . 'autoplay_speed',
                [
                    'label'              => esc_html__( 'Autoplay Speed', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::NUMBER,
                    'min'                => 0.01,
                    'step'               => 0.1,
                    'max'                => 100,
                    'default'            => $args['autoplay_speed_default'],
                    'description'        => esc_html__( 'Autoplay speed in second', 'motionui-addons-for-elementor' ),
                    'condition'          => $args['autoplay'] ? [ $prefix . 'autoplay' => 'yes' ] : [],
                    'frontend_available' => true
                ]
            );
        }

        if ( $args['loop'] ) {
            $this->add_control(
                $prefix . 'loop',
                [
                    'label'              => esc_html__( 'Infinite Loop?', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::SWITCHER,
                    'label_on'           => esc_html__( 'Yes', 'motionui-addons-for-elementor' ),
                    'label_off'          => esc_html__( 'No', 'motionui-addons-for-elementor' ),
                    'return_value'       => 'yes',
                    'default'            => $args['loop_default'],
                    'frontend_available' => true,
                    
                ]
            );
        }

        if ( $args['vertical'] ) {
            $this->add_control(
                $prefix . 'vertical',
                [
                    'label'              => esc_html__( 'Vertical Mode?', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::SWITCHER,
                    'label_on'           => esc_html__( 'Yes', 'motionui-addons-for-elementor' ),
                    'label_off'          => esc_html__( 'No', 'motionui-addons-for-elementor' ),
                    'return_value'       => 'yes',
                    'frontend_available' => true,
                    'style_transfer'     => true,
                    
                ]
            );
        }

        if ( $args['transition'] ) {
            $this->add_control(
                $prefix . 'slides_transition',
                [
                    'label'              => esc_html__( 'Transition', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::SELECT,
                    'default'            => $args['transition_default'],
                    'options'            => [
                        'slide' => esc_html__( 'Slide', 'motionui-addons-for-elementor' ),
                        'fade'  => esc_html__( 'Fade', 'motionui-addons-for-elementor' ),
                    ],
                    'frontend_available' => true,
                    'style_transfer'     => true,
                    'render_type'        => 'template',
                ]
            );
        }

        if ( $args['navigation'] ) {
            $this->add_control(
                $prefix . 'navigation',
                [
                    'label'              => esc_html__( 'Navigation', 'motionui-addons-for-elementor' ),
                    'type'               => Controls_Manager::SELECT,
                    'options'            => [
                        'none'  => esc_html__( 'None', 'motionui-addons-for-elementor' ),
                        'arrow' => esc_html__( 'Arrow', 'motionui-addons-for-elementor' ),
                        'dots'  => esc_html__( 'Dots', 'motionui-addons-for-elementor' ),
                        'both'  => esc_html__( 'Arrow & Dots', 'motionui-addons-for-elementor' ),
                    ],
                    'default'            => $args['navigation_default'],
                    'frontend_available' => true,
                    'style_transfer'     => true,
                    
                ]
            );
        }

        if ( $args['arrow_icons'] ) {
            $arrow_condition = $args['navigation']
                ? [ $prefix . 'navigation' => [ 'arrow', 'both' ] ]
                : [];

            $this->add_control(
                $prefix . 'arrow_prev_icon',
                [
                    'label'       => esc_html__( 'Previous Icon', 'motionui-addons-for-elementor' ),
                    'label_block' => false,
                    'type'        => Controls_Manager::ICONS,
                    'skin'        => 'inline',
                    'default'     => [
                        'value'   => 'fas fa-chevron-left',
                        'library' => 'fa-solid',
                    ],
                    'condition'   => $arrow_condition,
                ]
            );

            $this->add_control(
                $prefix . 'arrow_next_icon',
                [
                    'label'       => esc_html__( 'Next Icon', 'motionui-addons-for-elementor' ),
                    'label_block' => false,
                    'type'        => Controls_Manager::ICONS,
                    'skin'        => 'inline',
                    'default'     => [
                        'value'   => 'fas fa-chevron-right',
                        'library' => 'fa-solid',
                    ],
                    'condition'   => $arrow_condition,
                ]
            );
        }

        $this->end_controls_section();
    }
}