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
 *
 * `demo` takes a path on the product site; `tutorial` takes a full URL, since
 * tutorials live on YouTube or elsewhere. get_muia_demo_url() and
 * get_muia_tuto_url() in inc/functions.php sort out the rest, and hand back any
 * absolute URL untouched. Empty means "no link" and the dashboard hides the
 * button — except for a tutorial, which falls back to whatever
 * `muia_tutorial_default_url` supplies.
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
		'demo'        => get_muia_demo_url( '/element/button/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'burger-button' => [
		'title'       => __( 'Burger Button', 'motionui-addons-for-elementor' ),
		'category'    => [ 'button' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-menu-bar',
		'demo'        => get_muia_demo_url( '/element/burger-button/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'animated-slider' => [
		'title'       => __( 'Animated Slider', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-post-slider',
		'demo'        => get_muia_demo_url( '/element/animated-slider/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'animated-image' => [
		'title'       => __( 'Image', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-image',
		'demo'        => get_muia_demo_url( '/element/animated-image/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'animated-gallery' => [
		'title'       => __( 'Gallery', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-gallery-justified',
		'demo'        => get_muia_demo_url( '/element/animated-gallery/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'spotlight-button' => [
		'title'               => __( 'Spotlight Button', 'motionui-addons-for-elementor' ),
		'category'            => [ 'button' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'icon'                => 'eicon-button',
		'demo'                => get_muia_demo_url( '/element/spotlight-button-effect/' ),
		'tutorial'            => get_muia_tuto_url( '' ),
	],
	'accordion' => [
		'title'               => __( 'Accordion', 'motionui-addons-for-elementor' ),
		'category'            => [ 'accordion' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'icon'                => 'eicon-accordion',
		'demo'                => get_muia_demo_url( '/element/animate-accordion/' ),
		'tutorial'            => get_muia_tuto_url( '' ),
	],
	'before-after-scroll' => [
		'title'               => __( 'Before After by Scroll', 'motionui-addons-for-elementor' ),
		'category'            => [ 'scroll' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'icon'                => 'eicon-image-before-after',
		'demo'                => get_muia_demo_url( '/element/before-after-by-scroll/' ),
		'tutorial'            => get_muia_tuto_url( '' ),
	],
	'pricing-switcher' => [
		'title'               => __( 'Pricing Switcher', 'motionui-addons-for-elementor' ),
		'category'            => [ 'pricing' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'icon'                => 'eicon-price-table',
		'demo'                => get_muia_demo_url( '/element/pricing-plan-switcher/' ),
		'tutorial'            => get_muia_tuto_url( '' ),
	],
];
