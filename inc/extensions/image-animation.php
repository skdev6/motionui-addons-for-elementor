<?php 

namespace Themeic\MotionUI_Addons\Inc\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Image_Animation{

	/**
	 * Wiring that only applies while this extension is switched on.
	 *
	 * Called from Extensions_Manager::init() behind $is_image_active, so a
	 * disabled extension registers nothing and enqueues nothing.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/preview/enqueue_scripts', array( self::class, 'enqueue_preview_scripts' ) );
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
				'default' => 'reveal',
				'frontend_available' => true,
				'prefix_class' => 'visibility__hidden muia-img-',
				'options' => [
					'reveal' => esc_html__( 'Image Reveal', 'motionui-addons-for-elementor' ),
					'corner-reveal' => esc_html__( 'Corner Reveal', 'motionui-addons-for-elementor' ),
					'poly-reveal' => esc_html__( 'poly Reveal', 'motionui-addons-for-elementor' ),
					'circle-reveal' => esc_html__( 'Circle Reveal', 'motionui-addons-for-elementor' ),
					'tiles-reveal' => esc_html__( 'Tiles Reveal', 'motionui-addons-for-elementor' ),
					'zoom' => esc_html__( 'Zoom', 'motionui-addons-for-elementor' ),
					'parallax' => esc_html__( 'Image Parallax', 'motionui-addons-for-elementor' ),
				],
				'condition' => $enabled,
			]
		);
		$element->add_control(
			'img_muia_num_of_tiles',
			array(
				'label'              => esc_html__( 'Number of Tiles', 'motionui-addons-for-elementor' ),
				'type'               => \Elementor\Controls_Manager::NUMBER,
				'condition'          => array_merge(
					$enabled,
					array(
						'muia_img_ani_type' => 'tiles-reveal',
					)
				),
				'default'            => 5,
				'min'  => 3,
				'max'  => 100,
				'step' => 1,
				'frontend_available' => true,
			)
		);
		// The shared picker from motion.php, under the muia_ani_direction name
		// the image script reads.
		Motion::get_derection_control( $element, array(
			'name'      => 'img',
			'remove' => array('center-v'),
			'title_h_center' => esc_html__( 'Center', 'motionui-addons-for-elementor' ),
			'condition' => array_merge(
				$enabled,
				array(
					'muia_img_ani_type' => ['reveal', 'corner-reveal', 'zoom', 'poly-reveal', 'circle-reveal'],
				)
			),
			'default'   => 'left',
		) );
		Motion::get_derection_control( $element, array(
			'name'      => 'imgtiles',
			'remove' => array(''),
			'condition' => array_merge(
				$enabled,
				array(
					'muia_img_ani_type' => ['tiles-reveal'],
				)
			),
			'default'   => 'left',
		) );

		Motion::get_derection_control( $element, array(  
			'name'      => 'img_axis',
			'remove'    => ['bottom', 'center-h', 'center-v', 'right'],
			'title_left' => esc_html__( 'Horizontal', 'motionui-addons-for-elementor' ),
			'title_top' => esc_html__( 'Vertical', 'motionui-addons-for-elementor' ),
			'condition' => array_merge(
				$enabled,
				array(
					'muia_img_ani_type' => ['parallax'],
				)
			),
			'default'   => 'left',
		) );  
		Motion::fromTo_controls( $element, array(
			'name'         => 'img',
			'condition'    => array_merge(
				$enabled,
				array(
					'muia_img_ani_type' => 'parallax',
				)
			),
			'from_label'   => esc_html__( 'Travel From', 'motionui-addons-for-elementor' ),
			'to_label'     => esc_html__( 'Travel To', 'motionui-addons-for-elementor' ),
			'from_default' => array( 'unit' => '%', 'size' => -15 ),
			'to_default'   => array( 'unit' => '%', 'size' => 15 ),
		) );

		// condition carries through to the stagger, trigger point and easing
		// controls inside, so the switch reaches all of them.
		Motion::add_motion_settings_controls($element, array(
			'prefix'=>'img',
			'with_scroll'=> true,
			'stagger'=> true,
			'stagger_condition'=>[
				'muia_img_ani_type'=>['tiles-reveal']
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
	public static function enqueue_preview_scripts() {
		wp_enqueue_script( 'gsap' );
		wp_enqueue_script( 'scroll-trigger' );
		wp_enqueue_script( 'muia-imga' );
	}
}
