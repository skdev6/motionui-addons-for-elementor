<?php 

namespace Themeic\MotionUI_Addons\Inc\Classes;

use Themeic\MotionUI_Addons\Inc\Extensions as Extensions;

class Extensions_Manager{
    public static $logo = '';

    public static function init(){
		// Enqueue Editor CSS
		add_action('elementor/editor/after_enqueue_styles', [__CLASS__, 'enqueue_editor_css']);
		// Add Extension to Text Widgets
		foreach ( self::get_text_widgets() as $widget ) {
			add_action( 'elementor/element/' . $widget['name'] . '/' . $widget['section'] . '/after_section_end', [Extensions\Text_Animation::class, 'register_controls'], 10, 2 );
		}
		// Add Advance Position Extension to All Widgets
		add_action( 'elementor/element/common/_section_style/after_section_end', [ Extensions\Scroll_Animation::class, 'register_controls' ], 1 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ Extensions\Scroll_Animation::class, 'register_controls' ], 1 );
		// Add Advance Position Extension to All Widgets
		add_action( 'elementor/element/common/_section_style/after_section_end', [ Extensions\Advance_Position::class, 'register_controls' ], 1 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ Extensions\Advance_Position::class, 'register_controls' ], 1 );
		// Add Advance Position Extension to All Widgets
		add_action( 'elementor/element/common/_section_style/after_section_end', [ Extensions\Motion::class, 'register_controls' ], 1 );
		add_action( 'elementor/element/container/section_layout/after_section_end', [ Extensions\Motion::class, 'register_controls' ], 1 );
    }
	public static function get_text_widgets(){
		return [
			[
				'name'    => 'heading',
				'section' => 'section_title',
			],
			[
				'name'    => 'text-editor',
				'section' => 'section_editor',
			], 
        ];
	}
	public static function get_img_widgets(){
		return [
			[
				'name'    => 'image',
				'section' => 'section_image',
			],
        ];
	}
	public static function enqueue_editor_css() {
        wp_enqueue_style(
            'motionui-elementor-editor', 
            THEMEIC_MUIA_ASSETS . 'css/elementor-editor.css', 
            [],                    // dependencies (array, not null)
            THEMEIC_MUIA_VERSION
        );
    }
}