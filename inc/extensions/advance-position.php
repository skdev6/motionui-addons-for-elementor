<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Advance_Position{

    /**
     * Registers advanced positioning controls for Elementor elements.
     *
     * This method adds a new section in the Elementor's advanced tab that allows users
     * to set custom positioning properties including position type (static, sticky, relative, fixed, absolute),
     * and responsive controls for top, right, bottom, left, and from center positioning.
     *
     * @param Element_Base $element The Elementor element to add controls to.
     */
    public static function register_controls(Element_Base $element){
        $element->start_controls_section(
            'mui_addons_advance_position',
            array(
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Advance Position', 'motionui-addons-for-elementor')),
                'tab' => Controls_Manager::TAB_ADVANCED,
            )
        );
        $element->add_responsive_control( 
            'mui_addons_position_type',
            array(
                'label'       => __('Position Type', 'motionui-addons-for-elementor' ),
                'label_block' => true,
                'type'        => Controls_Manager::SELECT,
                'options'     => array(
                    ''         => __('Default', 'motionui-addons-for-elementor' ),
                    'static'   => __('Static', 'motionui-addons-for-elementor' ),
                    'sticky'   => __('Sticky', 'motionui-addons-for-elementor' ),
                    'relative' => __('Relative', 'motionui-addons-for-elementor' ),
                    'fixed' => __('fixed', 'motionui-addons-for-elementor' ),
                    'absolute' => __('Absolute', 'motionui-addons-for-elementor' )
                ),
                'default'      => '',
                'selectors'    => array(
                    '{{WRAPPER}}' => 'position:{{VALUE}};',
                )
            )
        );

        $element->add_responsive_control(
            'mui_addons_position_top',
            array(
                'label'      => __('Top', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', '%', 'vw', 'vh', 'custom'),
                'range'      => array(
                    'px' => array(
                        'min'  => -2000,
                        'max'  => 2000,
                        'step' => 1
                    ),
                    '%' => array(
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1
                    ),
                    'em' => array(
                        'min'  => -150,
                        'max'  => 150,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}}' => 'top:{{SIZE}}{{UNIT}};'
                ),
                'condition' => array(
                    'mui_addons_position_type' => array('relative', 'absolute', 'sticky', 'fixed')
                )
            )
        );

        $element->add_responsive_control(
            'mui_addons_position_right',
            array(
                'label'      => __('Right', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', '%', 'vw', 'vh', 'custom'),
                'range'      => array(
                    'px' => array(
                        'min'  => -2000,
                        'max'  => 2000,
                        'step' => 1
                    ),
                    '%' => array(
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1
                    ),
                    'em' => array(
                        'min'  => -150,
                        'max'  => 150,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}}' => 'right:{{SIZE}}{{UNIT}};'
                ),
                'condition' => array(
                    'mui_addons_position_type' => array('relative', 'absolute', 'sticky', 'fixed')
                ),
                'return_value' => ''
            )
        );
        $element->add_responsive_control(
            'mui_addons_position_bottom',
            array(
                'label'      => __('Bottom', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', '%', 'vw', 'vh', 'custom'),
                'range'      => array(
                    'px' => array(
                        'min'  => -2000,
                        'max'  => 2000,
                        'step' => 1
                    ),
                    '%' => array(
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1
                    ),
                    'em' => array(
                        'min'  => -150,
                        'max'  => 150,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}}' => 'bottom:{{SIZE}}{{UNIT}};'
                ),
                'condition' => array(
                    'mui_addons_position_type' => array('relative', 'absolute', 'sticky', 'fixed')
                )
            )
        );
        $element->add_responsive_control(
            'mui_addons_position_left',
            array(
                'label'      => __('Left', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', '%', 'vw', 'vh', 'custom'),
                'range'      => array(
                    'px' => array(
                        'min'  => -2000,
                        'max'  => 2000,
                        'step' => 1
                    ),
                    '%' => array(
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1
                    ),
                    'em' => array(
                        'min'  => -150,
                        'max'  => 150,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}}' => 'left:{{SIZE}}{{UNIT}};'
                ),
                'condition' => array(
                    'mui_addons_position_type' => array('relative', 'absolute', 'sticky', 'fixed')
                )
            )
        );

        $element->add_responsive_control(
            'mui_addons_position_from_center',
            array(
                'label'      => __('From Center', 'motionui-addons-for-elementor' ),
                'description' => __('Please avoid using "From Center" and "Left" options at the same time.', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', 'em', '%', 'vw', 'vh', 'custom'),
                'range'      => array(
                    'px' => array(
                        'min'  => -1000,
                        'max'  => 1000,
                        'step' => 1
                    ),
                    '%' => array(
                        'min'  => -100,
                        'max'  => 100,
                        'step' => 1
                    ),
                    'em' => array(
                        'min'  => -150,
                        'max'  => 150,
                        'step' => 1
                    )
                ),
                'selectors' => array(
                    '{{WRAPPER}}' => 'left:calc( 50% + {{SIZE}}{{UNIT}} );'
                ),
                'condition' => array(
                    'mui_addons_position_type' => array('relative', 'absolute', 'sticky', 'fixed')
                )
            )
        );
        $element->end_controls_section();
    }
}  