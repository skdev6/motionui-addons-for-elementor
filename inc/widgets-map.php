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
 * `js` and `css` are the assets a widget needs, and Muia_Base turns them into
 * Elementor's get_script_depends() and get_style_depends(). An entry may name a
 * plain handle such as `swiper`, or a Pro module such as `spotlight-button`,
 * which resolves to `muia-spotlight-button`; whatever is not registered is
 * dropped, so naming a Pro asset costs a free-only site nothing.
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
		'keywords'    => [ 'button', 'animate button', 'hover button', 'reveal button', 'motionui', 'animation' ],
		'demo'        => get_muia_demo_url( '/element/button/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
		'js'          => [ 'gsap', 'muia-spotlight-button' ],
		'css'         => [ 'muia-spotlight-button' ],
	],
	'burger-button' => [
		'title'       => __( 'Burger Button', 'motionui-addons-for-elementor' ),
		'category'    => [ 'button' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-menu-bar',
		'keywords'    => [ 'burger', 'hamburger', 'menu', 'toggle', 'nav', 'navigation', 'motionui', 'animation' ],
		'demo'        => get_muia_demo_url( '/element/burger-button/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'animated-slider' => [   
		'title'       => __( 'Animated Slide', 'motionui-addons-for-elementor' ),
		'category'    => [ 'image' ],
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-post-slider',
		'keywords'    => [ 'slider', 'slide', 'animated slider', 'hero', 'banner', 'slideshow', 'motionui' ],
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
		'keywords'    => [ 'image' ],
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
		'keywords'    => [ 'gallery', 'slide', 'banner', 'image', 'motionui' ],
		'demo'        => get_muia_demo_url( '/element/animated-gallery/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	],
	'accordion' => [
		'title'               => __( 'Accordion', 'motionui-addons-for-elementor' ),
		'category'            => [ 'accordion' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'icon'                => 'eicon-accordion',
		'keywords'    => [ 'accordion', 'faq', 'toggle', 'nested', 'themeic', 'animation', 'motion' ],
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
		'keywords'            => [ 'before', 'after', 'compare', 'reveal', 'themeic', 'scroll', 'motion' ],
		'demo'                => get_muia_demo_url( '/element/before-after-by-scroll/' ),
		'tutorial'            => get_muia_tuto_url( '' ),
		'js'                  => [ 'gsap', 'ScrollTrigger', 'muia-before-after' ],
		'css'                 => [ 'muia-before-after' ],
	],
	'pricing-switcher' => [
		'title'               => __( 'Pricing Switcher', 'motionui-addons-for-elementor' ),
		'category'            => [ 'pricing' ],
		'is_active'           => true,
		'is_pro'              => true,
		'is_upcoming'         => false,
		'icon'                => 'eicon-price-table',
		'keywords'    => [ 'table', 'price', 'themeic', 'animation', 'motion' ],
		'demo'                => get_muia_demo_url( '/element/pricing-plan-switcher/' ),
		'tutorial'            => get_muia_tuto_url( '' ),
		'css' => ['muia-pricing-switcher'],
		'js' => ['muia-pricing-switcher', 'gsap']
	],
];
