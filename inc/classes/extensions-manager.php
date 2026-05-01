<?php 

namespace Themeic\MotionUI_Addons\Inc\Classes;

use Themeic\MotionUI_Addons\Inc\Extensions as Extensions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Extensions_Manager{

    const WIDGET_DB_KEY = 'muia_inactive_extensions';

    public static function init(){
		// Enqueue Editor CSS
		add_action('elementor/editor/after_enqueue_styles', [__CLASS__, 'enqueue_editor_css']);
		// Add Extension to Text Widgets
		foreach ( self::get_text_widgets() as $widget ) {
			add_action( 'elementor/element/' . $widget['name'] . '/' . $widget['section'] . '/after_section_end', [Extensions\Text_Animation::class, 'register_controls'], 10, 2 );
		}
		// Add Extension to Image Widgets
		foreach ( self::get_img_widgets() as $widget ) {
			add_action( 'elementor/element/' . $widget['name'] . '/' . $widget['section'] . '/after_section_end', [Extensions\Image_Animation::class, 'register_controls'], 10, 2 );
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
			[
				'name'    => 'muia-animated-image',
				'section' => 'section_content',
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
    public static function extension_map() {
        $get_inactive_extensions = get_option(self::WIDGET_DB_KEY, []);
        $local_extensions = self::local_extensions_map();

        foreach ($get_inactive_extensions as $key) {
            if (isset($local_extensions[$key])) {
                $local_extensions[$key]['is_active'] = false;
            }
        }

        return $local_extensions;
    }
    public static function local_extensions_map(){
        return [
            'scroll-animation' => [
                'title'       => __( 'Scroll Animation', 'motionui-addons-for-elementor' ),
                'description' => __( 'Animate elements as they enter the viewport on scroll.', 'motionui-addons-for-elementor' ),
                'is_active'   => true,   // default state (used on first install)
                'is_pro'      => false,
                'is_upcoming' => false,
                'icon'        => 'eicon-scroll',
                'demo'        => '',
                'tutorial'    => '',
            ],
            'text-animation' => [
                'title'       => __( 'Text Animation', 'motionui-addons-for-elementor' ),
                'description' => __( 'Add entrance animations to heading and text editor widgets.', 'motionui-addons-for-elementor' ),
                'is_active'   => true,
                'is_pro'      => false,
                'is_upcoming' => false,
                'icon'        => 'eicon-t-letter',
                'demo'        => '',
                'tutorial'    => '',
            ],
            'image-animation' => [
                'title'       => __( 'Image Animation', 'motionui-addons-for-elementor' ),
                'description' => __( 'Add entrance animations to image widgets.', 'motionui-addons-for-elementor' ),
                'is_active'   => true,
                'is_pro'      => false,
                'is_upcoming' => false,
                'icon'        => 'eicon-image',
                'demo'        => '',
                'tutorial'    => '',
            ],
            'advance-position' => [
                'title'       => __( 'Advance Position', 'motionui-addons-for-elementor' ),
                'description' => __( 'Fine-tune widget positioning with advanced CSS controls.', 'motionui-addons-for-elementor' ),
                'is_active'   => true,
                'is_pro'      => false,
                'is_upcoming' => false,
                'icon'        => 'eicon-page-transition',
                'demo'        => '',
                'tutorial'    => '',
            ],
            'motion' => [
                'title'       => __( 'Motion Effects', 'motionui-addons-for-elementor' ),
                'description' => __( 'Apply continuous motion and parallax effects to any widget.', 'motionui-addons-for-elementor' ),
                'is_active'   => true,
                'is_pro'      => false,
                'is_upcoming' => false,
                'icon'        => 'eicon-animation',
                'demo'        => '',
                'tutorial'    => '',
            ],
        ]; 
    }
    public static function save_extensions($extensions = []){
        update_option( self::WIDGET_DB_KEY, $extensions);
    }
}   