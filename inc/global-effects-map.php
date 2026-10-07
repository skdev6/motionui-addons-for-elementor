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

use Themeic\MotionUI_Addons\Inc\Extensions;

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
		'settings' => [
			'duration' => [
				'type' => 'number',
				'default' => 0.7
			], 
			'ease'  => [
				'type' => 'number',
				'default' => 'power3.out',
				'options' => Extensions::get_ease_options()
			],
			'delay' => [
				'type' => 'number',
				'default' => 0.7
			],
		]
	),
	'preloader'       => array(
		'title'       => __( 'Preloader', 'motionui-addons-for-elementor' ),
		'description' => __( 'Show a loading screen until the page has finished loading.', 'motionui-addons-for-elementor' ),
		'is_active'   => false,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-loading',
		'demo'        => get_muia_demo_url( '' ),
		'tutorial'    => '',
		'pro_effects' => ['loading-reveal', 'bar-2-spage'],
		'effects' => [
			'classic' => [
				'loading-reveal'=> get_muia_demo_url( '' ),
				'loading-bar'=> get_muia_demo_url( '' ),
				'loading-dance'=> get_muia_demo_url( '' )
			],
			'shapes' => [
				'3-dot-shape'=> get_muia_demo_url( '' ),
				'circle-shape'=> get_muia_demo_url( '' ),
				'bar-2-spage'=> get_muia_demo_url( '' )
			],
		],
		'settings' => [
			'duration' => [
				'type' => 'number',
				'default' => 0.7
			], 
			'ease'  => [
				'type' => 'number',
				'default' => 'power3.out',
				'options' => Extensions::get_ease_options()
			],
			'delay' => [
				'type' => 'number',
				'default' => 0.7
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
				'slide-prallax'=> get_muia_demo_url( '' ),
				'slide-up'=> get_muia_demo_url( '' ),
				'slide-down'=> get_muia_demo_url( '/slide-down' )
			],
			'shapes' => [
				'wave-shape'=> get_muia_demo_url( '' ),
				'slide-wave'=> get_muia_demo_url( '' ),
				'center-wave'=> get_muia_demo_url( '' )
			],
		],
		'settings' => [
			'duration' => [
				'type' => 'number',
				'default' => 0.7
			], 
			'ease'  => [
				'type' => 'number',
				'default' => 'power3.out',
				'options' => Extensions::get_ease_options()
			],
			'delay' => [
				'type' => 'number',
				'default' => 0.7
			],
		]
	),
);
