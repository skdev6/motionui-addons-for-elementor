<?php
/**
 * The extension catalog.
 *
 * Every extension the dashboard lists. Extensions attach controls to Elementor's
 * own widgets rather than adding widgets of their own, which is why an entry
 * carries a `description` and no `category`.
 *
 * A plain array rather than a data file: OPcache keeps it compiled, so reading
 * it costs nothing, and the titles go through __() so they land in the
 * translation catalog.
 *
 * `is_active` here is the shipped default — the state on a fresh install.
 * Extensions_Manager::extension_map() overlays what the site has since turned
 * off, so nothing in this file needs to know about the database.
 *
 * `demo` takes a path on the product site; `tutorial` takes a full URL, since
 * tutorials live on YouTube or elsewhere. get_muia_demo_url() and
 * get_muia_tuto_url() in inc/functions.php sort out the rest, and hand back any
 * absolute URL untouched. Empty means "no link" and the dashboard hides the
 * button — except for a tutorial, which falls back to whatever
 * `muia_tutorial_default_url` supplies.
 */

defined( 'ABSPATH' ) || exit;

return array(
	'text-animation'   => array(
		'title'       => __( 'Text Animation', 'motionui-addons-for-elementor' ),
		'description' => __( 'Add entrance animations to heading and text editor widgets.', 'motionui-addons-for-elementor' ),
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-t-letter',
		'demo'        => get_muia_demo_url( '/element/text-tool-extension/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	),
	'image-animation'  => array(
		'title'       => __( 'Image Animation', 'motionui-addons-for-elementor' ),
		'description' => __( 'Add entrance animations to image widgets.', 'motionui-addons-for-elementor' ),
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-image',
		'demo'        => get_muia_demo_url( '/element/image-tool-extension/' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	),
	'advance-position' => array(
		'title'       => __( 'Advance Position', 'motionui-addons-for-elementor' ),
		'description' => __( 'Fine-tune widget positioning with advanced CSS controls.', 'motionui-addons-for-elementor' ),
		'is_active'   => true,
		'is_pro'      => false,
		'is_upcoming' => false,
		'icon'        => 'eicon-page-transition',
		'demo'        => get_muia_demo_url( '' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	),
	'motion-effects'   => array(
		'title'       => __( 'MotionUI Effects', 'motionui-addons-for-elementor' ),
		'description' => __( 'Add scroll-based animations to widgets.', 'motionui-addons-for-elementor' ),
		'is_active'   => true,
		'is_pro'      => true,
		'is_upcoming' => true,
		'icon'        => 'eicon-page-transition',
		'demo'        => get_muia_demo_url( '' ),
		'tutorial'    => get_muia_tuto_url( '' ),
	),
);
