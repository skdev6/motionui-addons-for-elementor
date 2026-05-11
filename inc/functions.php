<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function muia_sanitize_array_recursively($array) {

	foreach ($array as $key => &$value) {
		if (is_array($value)) {
			$value = muia_sanitize_array_recursively($value);
		} else {
			$value = sanitize_text_field($value);
		}
	}

	return $array;
}
function has_muia_pro(){
	return defined('THEMEIC_MUIA_PRO_VERSION');
}
if ( ! function_exists( 'muia_get_pronotice_html' ) ) {
	function muia_get_pronotice_html( $is_thumb = true ) {
		$img_src     = esc_url( THEMEIC_MUIA_ASSETS . 'img/get-pro-sm.png' );
		$upgrade_url = esc_url( 'https://motionuiaddons.com/' );

		$img_html = $is_thumb ? sprintf(
			'<img src="%s" alt="%s" />',
			$img_src,
			esc_attr( __( 'Upgrade Notice', 'motionui-addons-for-elementor' ) )
		) : '';

		return sprintf(
			'<div class="muia-pro-notice %s">
				%s
				<div class="muia-pro-notice-content">
					<h4>%s</h4>
					<p>%s</p>
					<a target="__blank" rel="nofollow" class="elementor-button elementor-button-default" href="%s">%s</a>
				</div>
			</div>',
			$is_thumb ? 'has-thumb' : 'no-thumb',
			$img_html,
			__( 'Upgrade to premium plan and unlock every feature!', 'motionui-addons-for-elementor' ),
			__( 'Upgrade and get access to every feature.', 'motionui-addons-for-elementor' ),
			$upgrade_url,
			__( 'Upgrade MotionUI Addons', 'motionui-addons-for-elementor' )
		);
	}
}