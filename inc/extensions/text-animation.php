<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

class Text_Animation{
    public static function register_controls($element){
        $element->start_controls_section(
            'mui_addons_text_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Text Animation', 'motionui-addons')),
            ]
        );
		$element->add_control(
			'muia_text_ani',
			[
				'label' => esc_html__( 'Text Animation', 'motionui-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'frontend_available' => true,  
				'prefix_class' => 'has-muia-text-animation muia-text-',
				'options' => [
					'' => esc_html__( 'None', 'motionui-addons' ),
					'reveal' => esc_html__( 'Text Reveal', 'motionui-addons' ),
					'fade-up' => esc_html__( 'Text Fade Up', 'motionui-addons' ),
				],
			]
		);
		$element->add_control(
			'muia_text_ani_by',
			[
				'label' => esc_html__( 'Animate By', 'motionui-addons' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'words',
				'frontend_available' => true,  
				'options' => [
					'lines' => esc_html__( 'Lines', 'motionui-addons' ),
					'words' => esc_html__( 'Words', 'motionui-addons' ),
					'chars' => esc_html__( 'Characters', 'motionui-addons' ),
				],
				'condition' => [
					'muia_text_ani!' => '',
				],
			]
		);
        $element->end_controls_section();
    }
}  