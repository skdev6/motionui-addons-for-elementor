<?php
/**
 * The global effects catalog.
 *
 * Site-wide effects that run on the front end, rather than controls attached
 * to Elementor widgets. Each entry is rendered as a switch card.
 *
 * `is_active` here is the shipped default. Unlike extensions, global effects
 * change how every page looks, so they ship switched off and
 * Global_Effects_Manager::global_effects_map() overlays the ones the site has
 * turned on.
 */

defined( 'ABSPATH' ) || exit;

return array(
	'smooth-scroll'   => array(
		'title'       => __( 'Smooth Scroll', 'motionui-addons-for-elementor' ),
		'description' => __( 'Eased, inertia-style scrolling across the whole site.', 'motionui-addons-for-elementor' ),
		'is_active'   => false,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-scroll',
		'demo'        => '',
		'tutorial'    => '',
	),
	'preloader'       => array(
		'title'       => __( 'Preloader', 'motionui-addons-for-elementor' ),
		'description' => __( 'Show a loading screen until the page has finished loading.', 'motionui-addons-for-elementor' ),
		'is_active'   => false,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-loading',
		'demo'        => get_muia_demo_url( '/all-preloader' ),
		'tutorial'    => '',
		'pro_effects' => ['loading-reveal', 'bar-2-spage'],
		'effects' => [
			'classic' => [
				'loading-reveal'=> get_muia_demo_url( '/loading-reveal' ),
				'loading-bar'=> get_muia_demo_url( '/loading-bar' )
				'loading-dance'=> get_muia_demo_url( '/loading-dance' )
			],
			'shapes' => [
				'3-dot-shape'=> get_muia_demo_url( '/loading-reveal' ),
				'circle-shape'=> get_muia_demo_url( '/loading-bar' )
				'bar-2-spage'=> get_muia_demo_url( '/loading-dance' )
			],
		]
	),
	'page-transition' => array(
		'title'       => __( 'Page Transition', 'motionui-addons-for-elementor' ),
		'description' => __( 'Fade between pages when visitors follow internal links.', 'motionui-addons-for-elementor' ),
		'is_active'   => false,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-page-transition',
		'demo'        => '',
		'tutorial'    => '',
		'pro_effects' => ['slide-up', 'center-wave'],
		'effects' => [
			'slide' => [
				'slide-prallax'=> get_muia_demo_url( '/slide-prallax' ),
				'slide-up'=> get_muia_demo_url( '/slide-up' )
				'slide-down'=> get_muia_demo_url( '/slide-down' )
			],
			'shapes' => [
				'wave-shape'=> get_muia_demo_url( '/loading-reveal' ),
				'slide-wave'=> get_muia_demo_url( '/loading-bar' )
				'center-wave'=> get_muia_demo_url( '/loading-dance' )
			],
		]
	),
);
