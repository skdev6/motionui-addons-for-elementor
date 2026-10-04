<?php 
namespace Themeic\MotionUI_Addons\Inc\Classes;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets {

    public static function register_scripts() {
		wp_register_script( 'isotope', THEMEIC_MUIA_ASSETS . 'vendor/isotope/isotope.pkgd.min.js', [], THEMEIC_MUIA_VERSION, true );
        // plugin script
        wp_register_script( 'gsap', THEMEIC_MUIA_ASSETS . 'vendor/gsap/gsap.min.js', [], THEMEIC_MUIA_VERSION, false );
        wp_register_script( 'scroll-trigger', THEMEIC_MUIA_ASSETS . 'vendor/scroll-trigger/scroll-trigger.min.js', ['gsap'], THEMEIC_MUIA_VERSION, false );
        wp_register_script( 'draw-svg', THEMEIC_MUIA_ASSETS . 'vendor/draw-svg/draw-svg.min.js', ['gsap'], THEMEIC_MUIA_VERSION, false );
        wp_register_script( 'swiper', THEMEIC_MUIA_ASSETS . 'vendor/Swiper/swiper-bundle.min.js', [], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'muia-animated-slider', THEMEIC_MUIA_ASSETS . 'js/widgets/animated-slider.js', ['muia'], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'muia-imga', THEMEIC_MUIA_ASSETS . 'js/widgets/imga.js', ['gsap', 'muia'], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'muia-texta', THEMEIC_MUIA_ASSETS . 'js/widgets/texta.js', ['gsap', 'muia'], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'muia-testimonial', THEMEIC_MUIA_ASSETS . 'js/widgets/muia-testimonial.js', ['swiper', 'muia'], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'muia-slide', THEMEIC_MUIA_ASSETS . 'js/widgets/slide.js', ['swiper', 'muia'], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'animated-headline', THEMEIC_MUIA_ASSETS . 'vendor/animated-headline/jquery.animatedheadline.min.js', [ 'jquery' ], THEMEIC_MUIA_VERSION, true );
        wp_register_script( 'split-type', THEMEIC_MUIA_ASSETS . 'vendor/split-type/split-type.min.js', [], THEMEIC_MUIA_VERSION, true );

		// CSS Register
		wp_register_style( 'animated-headline', THEMEIC_MUIA_ASSETS . 'vendor/animated-headline/jquery.animatedheadline.css', [], THEMEIC_MUIA_VERSION );
		wp_register_style( 'muia-testimonial', THEMEIC_MUIA_ASSETS . 'css/widgets/muia-testimonial.min.css', [], THEMEIC_MUIA_VERSION );
		wp_register_style( 'muia-slide', THEMEIC_MUIA_ASSETS . 'css/widgets/muia-slide.css', [], THEMEIC_MUIA_VERSION ); 

        wp_register_script(
            'muia',
            THEMEIC_MUIA_ASSETS . 'js/muia-addons.js',
            [ 'jquery', 'split-type' ],
            THEMEIC_MUIA_VERSION,
            true
        );
    }

    public static function enqueue_scripts() {  
        wp_enqueue_script( 'muia' );

		$localize_data = [
			'hasPro'  => Motionui::is_active_pro(),
			'i18n' => [],
		];

		wp_add_inline_script(
			'muia',
			'var muiaLocal = ' . wp_json_encode( $localize_data ) . ';',
			'before'
		);

    } 

    public static function enqueue_styles() {
        wp_register_style( 'swiper', THEMEIC_MUIA_ASSETS . 'vendor/Swiper/swiper-bundle.min.css', [], THEMEIC_MUIA_VERSION );
        wp_enqueue_style(
            'motionui-addons-widgets',
            THEMEIC_MUIA_ASSETS . 'css/widgets.css',
            [],
            THEMEIC_MUIA_VERSION
        );
        wp_enqueue_style(
            'themeic-icons',
            THEMEIC_MUIA_ASSETS . 'fonts/th-icon-basic.css',
            [],
            THEMEIC_MUIA_VERSION
        );
    }
	/**
	 * Enqueue editor-only CSS for the Elementor panel.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_editor_css() {
		// Bail if required constants are not defined.
		if ( ! defined( 'THEMEIC_MUIA_ASSETS' ) || ! defined( 'THEMEIC_MUIA_VERSION' ) ) {
			return;
		}

		wp_enqueue_style(
			'motionui-elementor-editor',
			THEMEIC_MUIA_ASSETS . 'css/elementor-editor.css',
			array(),
			THEMEIC_MUIA_VERSION
		);
	}
	/**
	 * Enqueue editor-only JavaScript for the Elementor panel.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public static function enqueue_editor_js() {
		// Bail if required constants are not defined.
		if ( ! defined( 'THEMEIC_MUIA_ASSETS' ) || ! defined( 'THEMEIC_MUIA_VERSION' ) ) {
			return;
		}
		
		$localize_data = [
			'placeholder_widgets' => Widgets_Manager::get_pro_widgets(),
			'hasPro'                  => Motionui::is_active_pro(),
			'proSelectClass'          => muia_pro_select_class(),
			'editor_nonce'            => wp_create_nonce('muid_editor_nonce'),
			'upgradeUrl'=>'https://motionuiaddons.com/',
			'btnText'=>'Get Pro Feature',
			'desc'=>'Take your designs further. Upgrade to MotionUI Addons Pro and unlock',
			'proImage'=>THEMEIC_MUIA_ASSETS . 'img/get-pro.svg',
			'i18n' => [
				/* translators: %s: widget name. */
				'promotionDialogHeader'     => esc_html__('%s Widget', 'motionui-addons-for-elementor'),
				/* translators: %s: widget name. */
				'promotionDialogMessage'    => esc_html__('Use %s widget with other exclusive pro widgets and 100% unique features to extend your toolbox and build sites faster and better.', 'motionui-addons-for-elementor'),
				'promotionDialogBtnTxt'    => esc_html__('Upgrade Now', 'motionui-addons-for-elementor'),
				'templatesEmptyTitle'       => esc_html__('No Templates Found', 'motionui-addons-for-elementor'),
				'templatesEmptyMessage'     => esc_html__('Try different category or sync for new templates.', 'motionui-addons-for-elementor'),
				'templatesNoResultsTitle'   => esc_html__('No Results Found', 'motionui-addons-for-elementor'),
				'templatesNoResultsMessage' => esc_html__('Please make sure your search is spelled correctly or try a different words.', 'motionui-addons-for-elementor'),
			],
		];

		wp_enqueue_script(
			'motionui-elementor-editor',
			THEMEIC_MUIA_ASSETS . 'js/elementor-editor.js',
			array(),
			THEMEIC_MUIA_VERSION,
			true
		);

		wp_localize_script(
			'motionui-elementor-editor',
			'MotionUIEditor',
			$localize_data
		);
	}
}
