<?php
/**
 * Custom Controls Trait
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

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trait Custom_Controls
 *
 * Registers reusable Elementor style and content controls for animated button elements.
 * Include this trait in any Elementor widget class that needs button styling.
 *
 * @since 1.0.0
 */
trait Custom_Control{     
    public function _add_muia_border_controls( $prefix = '', $selector = '', $is_var = false ) {  
		$this->add_responsive_control(
			$prefix . 'border',
			[  
				'label' => esc_html__( 'Border Style', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'motionui-addons-for-elementor' ),
					'none' => esc_html__( 'None', 'motionui-addons-for-elementor' ),
					'solid'  => esc_html__( 'Solid', 'motionui-addons-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'motionui-addons-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'motionui-addons-for-elementor' ),
					'double' => esc_html__( 'Double', 'motionui-addons-for-elementor' ),
				],
				'selectors' => [
					$selector => ( $is_var ? '--border-style' : 'border-style' ) . ': {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			$prefix . 'border_width',
			[
				'label' => esc_html__( 'Border Width', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$selector => ( $is_var ? '--border-width' : 'border-width' ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    $prefix . 'border!' => ['none', ''],
                ],
			]
		);
        $this->add_responsive_control(   
            $prefix . 'border_color',
            [
                'label' => esc_html__( 'Border Color', 'motionui-addons-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    $selector => ( $is_var ? '--border-color' : 'border-color' ) . ': {{VALUE}};',
                ],
                'condition' => [
                    $prefix . 'border!' => ['none', ''],  
                ],
            ]
        );
    }
}