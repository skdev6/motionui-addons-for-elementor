<?php

namespace Themeic\MotionUI_Addons\Inc\Extensions;

use Elementor\Element_Base;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Dynamic_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_CSS {

	const CONTROL_ID = 'muia_custom_css';

	/**
	 * Hooks the stylesheet output. Called from Extensions_Manager::init()
	 * behind the extension's active switch.
	 */
	public static function init() {
		add_action( 'elementor/element/parse_css', array( __CLASS__, 'add_post_css' ), 10, 2 );
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_editor_js' ) );
	}

	/**
	 * Live preview script for the editor. Loaded only while the extension
	 * is active, since init() is only called then.
	 */
	public static function enqueue_editor_js() {
		wp_enqueue_script(
			'muia-custom-css-editor',
			THEMEIC_MUIA_ASSETS . 'js/custom-css-editor.js',
			array( 'elementor-editor' ),
			THEMEIC_MUIA_VERSION,
			true
		);
	}

	/**
	 * Adds a Custom CSS section to the Advanced tab of an element.
	 *
	 * @param Element_Base $element The Elementor element to add controls to.
	 */
	public static function register_controls( Element_Base $element ) {
		$element->start_controls_section(
			'muia_custom_css_section',
			array(
				'label' => sprintf( '<div class="el-editor-logo-wrap"><i class="themeic-muia-logo"></i>%s</div>', __( 'Custom CSS', 'motionui-addons-for-elementor' ) ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			self::CONTROL_ID,
			array(
				'type'        => Controls_Manager::CODE,
				'label'       => __( 'Custom CSS', 'motionui-addons-for-elementor' ),
				'language'    => 'css',
				'render_type' => 'ui',
				'show_label'  => false,
				'separator'   => 'none',
			)
		);

		$element->add_control(
			'muia_custom_css_description',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Use "selector" to target this element. Example: selector .child { color: red; }', 'motionui-addons-for-elementor' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Prints the element's custom CSS into the post stylesheet, with
	 * "selector" swapped for the element's unique selector.
	 *
	 * @param \Elementor\Core\Files\CSS\Base $post_css Stylesheet being built.
	 * @param Element_Base                   $element  Element being parsed.
	 */
	public static function add_post_css( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$css = trim( (string) $element->get_settings( self::CONTROL_ID ) );

		if ( '' === $css ) {
			return;
		}

		// The sheet is printed inside a <style> tag, so no markup may get through.
		$css = wp_strip_all_tags( $css );
		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );

		$post_css->get_stylesheet()->add_raw_css( $css );
	}
}
