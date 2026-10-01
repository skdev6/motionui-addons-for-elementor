<?php

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Text_Animation{

	/**
	 * Wiring that only applies while this extension is switched on.
	 *
	 * Called from Extensions_Manager::init() behind $is_text_active, so a
	 * disabled extension registers nothing and enqueues nothing.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/preview/enqueue_scripts', array( self::class, 'enqueue_preview_scripts' ) );
	}

	/**
	 * Load this extension's scripts outright in the editor preview.
	 *
	 * The `assets` conditions on muia_text_enable are resolved by Elementor's
	 * page assets loader, which walks the document saved element data on save
	 * and on a frontend render. The editor preview builds its elements
	 * client-side, so nothing walks them there and none of those handles are
	 * enqueued. The same handles as the control list, so the editor loads
	 * exactly what the frontend would.
	 *
	 * @return void
	 */
	public static function enqueue_preview_scripts() {
		wp_enqueue_script( 'gsap' );
		wp_enqueue_script( 'scroll-trigger' );
		wp_enqueue_script( 'split-type' );
		wp_enqueue_script( 'muia-texta' );
	}

    public static function register_controls($element){
        $element->start_controls_section(
            'muia_addons_text_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Text Animation', 'motionui-addons-for-elementor')),
            ]
        );
        $element->add_control(
            'muia_text_enable',
            [
                'label'              => __( 'Enable', 'motionui-addons-for-elementor' ),
                'type'               => Controls_Manager::SWITCHER,
                'prefix_class'       => 'muia-text-ani-',
                'render_type'        => 'template',
                'return_value'       => 'yes',
                'style_transfer'     => false,
                'frontend_available' => true,
                'assets'             => [
                    'scripts' => [
                        [
                            'name'       => 'elementor-frontend',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name'     => 'muia_text_enable',
                                        'operator' => '===',
                                        'value'    => 'yes'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'       => 'gsap',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name'     => 'muia_text_enable',
                                        'operator' => '===',
                                        'value'    => 'yes'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'       => 'scroll-trigger',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name'     => 'muia_text_enable',
                                        'operator' => '===',
                                        'value'    => 'yes'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'       => 'split-type',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name'     => 'muia_text_enable',
                                        'operator' => '===',
                                        'value'    => 'yes'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'       => 'muia-texta',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name'     => 'muia_text_enable',
                                        'operator' => '===',
                                        'value'    => 'yes'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

		// Everything below only means anything once the extension is switched
		// on, so they all carry this. Kept in one place so the key is written
		// once and merged into whatever each control already needed.
		$enabled = array( 'muia_text_enable' => 'yes' );

		$element->add_control(
			'muia_text_ani',
			array(
				'label'              => esc_html__( 'Text Animation', 'motionui-addons-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'slide',
				'frontend_available' => true,
				'prefix_class'       => 'visibility__hidden muia-text-',    
				'options'            => muia_pro_options(  
					array(
						'slide'             => esc_html__( 'Slide', 'motionui-addons-for-elementor' ),
						'reveal-alt'       => esc_html__( 'Alternative Reveal', 'motionui-addons-for-elementor' ),
						'reveal-text'      => esc_html__( 'Text Reveal', 'motionui-addons-for-elementor' ),
						'reveal-smoky'     => esc_html__( 'Smoky Reveal', 'motionui-addons-for-elementor' ),
						'reveal-popup'     => esc_html__( 'Popup Reveal', 'motionui-addons-for-elementor' ),
						'reveal-mixing'    => esc_html__( 'Mixing Reveal', 'motionui-addons-for-elementor' ),
						'reveal-scale'   => esc_html__( 'Scale', 'motionui-addons-for-elementor' ),
						'reveal-flip'      => esc_html__( 'Text Flip', 'motionui-addons-for-elementor' ),
					),
					array(
						'wave'             => esc_html__( 'Wave', 'motionui-addons-for-elementor' ),
						'scramble'         => esc_html__( 'Scramble', 'motionui-addons-for-elementor' ),
						'text-auto-scroll' => esc_html__( 'Auto Scroll', 'motionui-addons-for-elementor' ),
					)
				),
				'condition'          => $enabled,
			)
		);

		Motion::get_derection_control( $element, array(
			'name'      => 'text',
			'condition' => array_merge(
				$enabled,
				array( 'muia_text_ani' => array( 'slide', 'reveal-text' ) )
			),
			'remove'    => array( 'center-v', 'center-h' ),
			'default'   => 'bottom',
		) );

        $element->add_control(   
            'muia_text_mask',
            [
                'label'              => __( 'Mask', 'motionui-addons-for-elementor' ),
                'type'               => Controls_Manager::SWITCHER,
                'return_value'       => 'yes',
                'frontend_available' => true,
				'condition' => array_merge(
					$enabled,
					array( 'muia_text_ani' => array( 'reveal-text' ) )
				),
            ]
        );
		Motion::fromTo_controls($element, [  
			'condition' => array_merge(
				$enabled,
				array( 'muia_text_ani' => array( 'slide', 'reveal-text' ) ),
				array( 'muia_text_mask!' => 'yes' ),
			),
			'from_label'   => esc_html__( 'Space From', 'motionui-addons-for-elementor' ),
			'to_label'     => esc_html__( 'Space To', 'motionui-addons-for-elementor' ),
			'from_default' => array( 'unit' => 'px', 'size' => 50 ),
			'to_default'   => array( 'unit' => 'px', 'size' => 0 )
		]);

		$element->add_control(
			'muia_text_ani_by',
			[
				'label' => esc_html__( 'Animate By', 'motionui-addons-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'words',
				'frontend_available' => true,
				'options' => [
					'lines' => esc_html__( 'Lines', 'motionui-addons-for-elementor' ),
					'words' => esc_html__( 'Words', 'motionui-addons-for-elementor' ),
					'chars' => esc_html__( 'Characters', 'motionui-addons-for-elementor' ),
				],
				'condition' => array_merge(
					$enabled,
					array( 'muia_text_ani' => array( 'reveal-text' ) )
				),
			]
		);
		Motion::add_motion_settings_controls($element, array(
			'prefix'=>'text',
			'stagger'=> true,
			'condition'=> $enabled,
		));

		if(!muia_has_pro()){
			$element->add_control(
				'muia_pro_text_effect_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => muia_get_pronotice_html(),
					'condition' => $enabled,
				)
			);
		}
        $element->end_controls_section();
    }
}
