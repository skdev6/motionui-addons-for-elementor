<?php
/**
 * The widget catalog.
 *
 * Every widget shipped with the plugin, plus the library widgets sold
 * separately so the dashboard can list them.
 *
 * A plain array rather than a data file: OPcache keeps it compiled, so reading
 * it costs nothing, and the titles go through __() so they land in the
 * translation catalog.
 *
 * Adding a widget: add an entry keyed by the slug that maps to its class name —
 * glow-button => Glow_Button.
 */

defined( 'ABSPATH' ) || exit;

return [
	'animated-button' => [
		'title'       => __( 'Button', 'motionui-addons-for-elementor' ),
		'category'    => [ 'button' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-button',
		'demo'        => '',
		'tutorial'    => '',
	],
	'burger-button' => [
		'title'       => __( 'Burger Button', 'motionui-addons-for-elementor' ),
		'category'    => [ 'button' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-menu-bar',
		'demo'        => '',
		'tutorial'    => '',
	],
	'animated-slider' => [
		'title'       => __( 'Slider', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-post-slider',
		'demo'        => '',
		'tutorial'    => '',
	],
	'animated-image' => [
		'title'       => __( 'Image', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-image',
		'demo'        => '',
		'tutorial'    => '',
	],
	'animated-gallery' => [
		'title'       => __( 'Gallery', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-gallery-justified',
		'demo'        => '',
		'tutorial'    => '',
	],
	'spotlight-button' => [
		'title'               => __( 'Spotlight Button', 'motionui-addons-for-elementor' ),
		'category'            => [ 'button' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'is_in_custom_widget' => true,
		'icon'                => 'eicon-button',
		'demo'                => 'https://motionuiaddons.com/',
		'tutorial'            => 'https://motionuiaddons.com/',
	],
	'accordion' => [
		'title'               => __( 'Accordion', 'motionui-addons-for-elementor' ),
		'category'            => [ 'accordion' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'is_in_custom_widget' => true,
		'icon'                => 'eicon-accordion',
		'demo'                => 'https://motionuiaddons.com/',
		'tutorial'            => 'https://motionuiaddons.com/',
	],
	'before-after-scroll' => [
		'title'               => __( 'Before After by Scroll', 'motionui-addons-for-elementor' ),
		'category'            => [ 'scroll' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'is_in_custom_widget' => true,
		'icon'                => 'eicon-image-before-after',
		'demo'                => 'https://motionuiaddons.com/',
		'tutorial'            => 'https://motionuiaddons.com/',
	],
	'pricing-switcher' => [
		'title'               => __( 'Pricing Switcher', 'motionui-addons-for-elementor' ),
		'category'            => [ 'pricing' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'is_in_custom_widget' => true,
		'icon'                => 'eicon-price-table',
		'demo'                => 'https://motionuiaddons.com/',
		'tutorial'            => 'https://motionuiaddons.com/',
	],
];
