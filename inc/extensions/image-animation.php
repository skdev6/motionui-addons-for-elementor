<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Image_Animation{

	public static function init(){
		add_action( 'elementor/preview/enqueue_scripts', [self, 'enqueue_preview_scripts'] );
	}

    public static function register_controls($element){
        $element->start_controls_section(
            'muia_addons_text_animation',
            [
                'label' => sprintf('<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __('Image Animations', 'motionui-addons-for-elementor')),
            ]
        );
        $element->add_control(
            'muia_img_enable',
            [
                'label'              => __( 'Enable', 'happy-elementor-addons' ),
                'type'               => \Elementor\Controls_Manager::SWITCHER,
                'prefix_class'       => 'muia-img-ani-',
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
                                        'name'     => 'muia_img_enable',
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
                                        'name'     => 'muia_img_enable',
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
                                        'name'     => 'muia_img_enable',
                                        'operator' => '===',
                                        'value'    => 'yes'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name'       => 'muia-imga',
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name'     => 'muia_img_enable',
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

		$enabled = array( 'muia_img_enable' => 'yes' );

		$element->add_control(  
			'muia_img_ani_type',
			[
				'label' => esc_html__( 'Animation', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'image-reveal',
				'frontend_available' => true,
				'prefix_class' => 'visibility__hidden muia-img-',
				'options' => [
					'image-reveal' => esc_html__( 'Image Reveal', 'motionui-addons-for-elementor' ),
					'corner-reveal' => esc_html__( 'Corner Reveal', 'motionui-addons-for-elementor' ),
					'zoom' => esc_html__( 'Zoom', 'motionui-addons-for-elementor' ),
					'image-prallax' => esc_html__( 'Image Prallax', 'motionui-addons-for-elementor' ),
				],
				'condition' => $enabled,
			]
		);
		// The shared picker from motion.php, under the muia_ani_direction name
		// the image script reads.
		Motion::get_derection_control( $element, array(
			'name'      => 'img',
			'condition' => array_merge(
				$enabled,
				array(
					'muia_img_ani_type!' => '',
				)
			),
			'default'   => 'left',
		) );
		$element->add_control(
			'muia_ani_image_space_from',
			[
				'label' => esc_html__( 'From', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'rem' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--image-animation-space-form: {{SIZE}}{{UNIT}};',
				],
				'condition' => array_merge(
					$enabled,
					[
						'muia_img_ani_type' => ['image-prallax'],
					]
				),
			]
		);
		// condition carries through to the stagger, trigger point and easing
		// controls inside, so the switch reaches all of them.
		Motion::add_motion_settings_controls($element, array(
			'prefix'=>'img',
			'with_scroll'=> true,
			'stagger'=> true,
			'stagger_condition'=>[
				'muia_img_ani_type'=>['grid-reveal', 'column-reveal']
			],
			'condition'=> array_merge(
				$enabled,
				[
					'muia_img_ani_type!' => '',
				]
			)
		));
		if(!muia_has_pro()){
			$element->add_control(
				'muia_pro_image_effect_notice',
				array(
					'type' => \Elementor\Controls_Manager::RAW_HTML,
					'raw'  => muia_get_pronotice_html(),
					'condition' => $enabled,
				)
			);
		}
        $element->end_controls_section();
    }

	public static function enqueue_preview_scripts(){
        wp_enqueue_script( 'gsap' );
        wp_enqueue_script( 'scroll-trigger' );
        wp_enqueue_script( 'muia-imga' );   
	}
}
