<?php 

namespace Themeic\MotionUI_Addons\Extensions;

class Text_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'mui_addons_text_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap">%s <i class="themeic-muia-logo"></i></div>', __('Text Animation', 'motionui-addons')),
            ]
        );
		$element->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'' => esc_html__( 'Default', 'textdomain' ),
					'none' => esc_html__( 'None', 'textdomain' ),
					'solid'  => esc_html__( 'Solid', 'textdomain' ),
					'dashed' => esc_html__( 'Dashed', 'textdomain' ),
					'dotted' => esc_html__( 'Dotted', 'textdomain' ),
					'double' => esc_html__( 'Double', 'textdomain' ),
				],
				'selectors' => [
					'{{WRAPPER}} .your-class' => 'border-style: {{VALUE}};',
				],
			]
		);
        $element->end_controls_section();
    }
}  