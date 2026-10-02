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
				'default'            => 'reveal-text',
				'frontend_available' => true,
				'prefix_class'       => 'visibility__hidden muia-text-',  
				'options'            => muia_pro_options(  
					array(
						'reveal-text'      => esc_html__( 'Text Reveal', 'motionui-addons-for-elementor' ),
						'reveal-alt'       => esc_html__( 'Alternative Reveal', 'motionui-addons-for-elementor' ),
						'reveal-smoky'     => esc_html__( 'Smoky Reveal', 'motionui-addons-for-elementor' ),
						// 'reveal-popup'     => esc_html__( 'Popup Reveal', 'motionui-addons-for-elementor' ),
						// 'reveal-mixing'    => esc_html__( 'Mixing Reveal', 'motionui-addons-for-elementor' ),
						// 'reveal-scale'   => esc_html__( 'Scale', 'motionui-addons-for-elementor' ),
						// 'reveal-flip'      => esc_html__( 'Text Flip', 'motionui-addons-for-elementor' ),
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
		$element->add_control(
			'muia_text_v_offset',
			[
				'label' => esc_html__( 'Vertical Offset', 'motionui-addons-for-elementor' ),
				'frontend_available' => true,
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 500,
				'step' => 1,
				'default' => 70,
				'condition' => array_merge(
					$enabled,
					array( 'muia_text_ani' => array( 'reveal-smoky' ) )
				),
			]
		);
		$element->add_control(    
			'muia_text_scale_from',
			[
				'label' => esc_html__( 'Scale From', 'motionui-addons-for-elementor' ),
				'frontend_available' => true,
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'step' => 0.01,
				'default' => 2.1,
				'condition' => array_merge(
					$enabled,
					array( 'muia_text_ani' => array( 'reveal-smoky' ) )
				),
			]
		);
		$element->add_control(
			'muia_text_blur',
			[
				'label' => esc_html__( 'Blur', 'motionui-addons-for-elementor' ),
				'frontend_available' => true,
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 100,
				'step' => 1,
				'default' => 70,
				'condition' => array_merge(
					$enabled,
					array( 'muia_text_ani' => array( 'reveal-smoky' ) )
				),
			]
		);
		$element->add_control(
			'muia_text_stagger_from',
			[
				'label' => esc_html__( 'Stagger From', 'motionui-addons-for-elementor' ),
				'frontend_available' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				// 'solid' is not one of the options below, so nothing was
				// preselected and GSAP received a value it cannot read.
				'default' => 'start',
				'options' => [
					'random' => esc_html__( 'Random', 'motionui-addons-for-elementor' ),
					'start' => esc_html__( 'Start', 'motionui-addons-for-elementor' ),
					'end'  => esc_html__( 'End', 'motionui-addons-for-elementor' ),
					'center' => esc_html__( 'Center', 'motionui-addons-for-elementor' ),
					'edges' => esc_html__( 'Edges', 'motionui-addons-for-elementor' ),
				],
				'condition' => array_merge(
					$enabled,
					array( 'muia_text_ani' => array( 'reveal-smoky' ) )
				),
			]
		);
		Motion::get_derection_control( $element, array( 
			'name'      => 'text',
			'condition' => array_merge(
				$enabled,
				array( 'muia_text_ani' => array( 'reveal-text' ) )
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
				array( 'muia_text_ani' => array( 'reveal-text' ) ),
				array( 'muia_text_mask!' => 'yes' ),
			),
			'from_label'   => esc_html__( 'Space From', 'motionui-addons-for-elementor' ),
			'from_default' => array( 'unit' => 'px', 'size' => 50 ),
			'is_to' => false, 
			'range'        => array(
				'px' => array( 'min' => 0, 'max' => 500, 'step' => 1 ),
				'%'  => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
				'vh' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
				'vw' => array( 'min' => 0, 'max' => 100, 'step' => 1 ),
			),
		]);
		Motion::fromTo_controls($element, [ 
			'name' => 'text_offset',
			'condition' => array_merge(
				$enabled,
				array( 'muia_text_ani' => array( 'reveal-alt' ) ),
			),
			'from_label'   => esc_html__( 'Even Vertical Offset', 'motionui-addons-for-elementor' ),
			'to_label'   => esc_html__( 'Odd Vertical Offset', 'motionui-addons-for-elementor' ),
			'from_default' => array( 'unit' => 'px', 'size' => -20 ),
			'to_default'   => array( 'unit' => 'px', 'size' => 80 ),
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
					array( 'muia_text_ani' => array( 'reveal-text', 'reveal-smoky' ) )
				),
			]
		);
		Motion::add_motion_settings_controls($element, array( 
			'prefix'=>'text',
			'stagger'=> true,
			'condition'=> $enabled,
			'default_duration'=> 0.8,
			'default_stagger'=> 0.04,
			'default_ease' => 'power4.out'
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
