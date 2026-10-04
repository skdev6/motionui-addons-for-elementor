<?php
/**
 * Testimonial Widget
 *
 * One list of testimonials, four card designs, shown either as a grid or as a
 * Swiper slider.
 *
 * The card designs are the markup the bundled stylesheet already draws, so each
 * one is rendered exactly as its CSS expects. Adding a fifth later is two
 * steps: an entry in get_muia_testimonial_styles() and a branch in
 * render_muia_card().
 *
 * Slide behaviour comes from the Slide_Controls trait, so this widget and the
 * others that slide stay on the same options, and muia-testimonial.js reads
 * them back off the element — every one of those controls is
 * frontend_available.
 *
 * @package     MotionUI Addons for Elementor
 * @subpackage  Widgets
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Themeic\MotionUI_Addons\Traits\Slide_Controls;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Testimonial
 *
 * @since 1.0.0
 */
class Testimonial extends Muia_Base {

    use Slide_Controls;

    /**
     * How many social links a card design can show.
     *
     * Elementor cannot nest a repeater inside a repeater, so the links are a
     * fixed set of fields per item rather than a list of their own.
     */
    const SOCIAL_SLOTS = 3;

    /**
     * The card designs, keyed by the value stored in the panel.
     *
     * `class` is the root class its CSS is written against — the fourth is
     * spelled without the prefix because that is what the stylesheet uses.
     *
     * @since  1.0.0
     * @return array<string,array>
     */
    protected function get_muia_testimonial_styles() {
        return [
            'card10' => [
                'label' => esc_html__( 'Split Author', 'motionui-addons-for-elementor' ),
                'class' => 'themeic-testimonial-card10',
            ],
            'card2'  => [
                'label' => esc_html__( 'Rated', 'motionui-addons-for-elementor' ),
                'class' => 'themeic-testimonial-card2',
            ],
            'card3'  => [
                'label' => esc_html__( 'Quoted', 'motionui-addons-for-elementor' ),
                'class' => 'themeic-testimonial-card3',
            ],
            'long'   => [
                'label' => esc_html__( 'Wide', 'motionui-addons-for-elementor' ),
                'class' => 'testimonial-card-long',
            ],
        ];
    }

    /**
     * The style currently chosen, falling back to the first one.
     *
     * @since  1.0.0
     * @param  array $settings Widget settings.
     * @return string
     */
    protected function get_muia_testimonial_style( $settings ) {

        $styles = $this->get_muia_testimonial_styles();
        $style  = isset( $settings['themeic_testimonial_style'] ) ? $settings['themeic_testimonial_style'] : '';

        return isset( $styles[ $style ] ) ? $style : key( $styles );
    }

    /**
     * A selector list that covers the same part in every card design.
     *
     * Written once here so a style control names a part — the author's name,
     * the quote — instead of four CSS paths that have to be kept in step.
     *
     * @since  1.0.0
     * @param  string $part name, designation, content, image or card.
     * @return string
     */
    protected function muia_part_selector( $part ) {

        $map = [
            'card'        => [
                '.muia-testimonial-card',
            ],
            'name'        => [
                '.themeic-testimonial-card10 .title',
                '.themeic-testimonial-card2 .meta-details .title',
                '.themeic-testimonial-card3 .avater-right-info h5',
                '.testimonial-card-long .author-name',
            ],
            'designation' => [
                '.themeic-testimonial-card10 .designation',
                '.themeic-testimonial-card2 .info',
                '.themeic-testimonial-card3 .avater-right-info p',
                '.testimonial-card-long .bottom-info span',
            ],
            'content'     => [
                '.themeic-testimonial-card10 .text-content p',
                '.themeic-testimonial-card2 .des p',
                '.themeic-testimonial-card3 .des p',
                '.testimonial-card-long .text-content p',
            ],
            'image'       => [
                '.themeic-testimonial-card10 .thumb',
                '.themeic-testimonial-card2 .author-meta img',
                '.themeic-testimonial-card3 .avater img',
                '.testimonial-card-long .thumb img',
            ],
        ];

        $parts = isset( $map[ $part ] ) ? $map[ $part ] : [];

        return '{{WRAPPER}} ' . implode( ', {{WRAPPER}} ', $parts );
    }

    /**
     * Register widget controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function register_controls() {

        $this->_register_muia_testimonial_content_controls();
        $this->_register_muia_testimonial_layout_controls();

        // From the trait, so the slide options match the other sliders. Only
        // shown once the view is set to Slide.
        $this->muia_slide_controls( 'muia_slide', [
            'condition'     => [ 'themeic_testimonial_view' => 'slide' ],
            'height'        => false,
            'thumb_width'   => false,
            'gap_default'   => 24,
            'speed_default' => 300,
        ] );
        $this->muia_slide_nav_controls( 'muia_slide', [
            'condition' => [ 'themeic_testimonial_view' => 'slide' ],
        ] );

        $this->_register_muia_testimonial_style_controls();
        $this->muia_slide_style_controls( 'muia_slide', [
            'condition' => [ 'themeic_testimonial_view' => 'slide' ],
        ] );
    }

    /**
     * Content controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_testimonial_content_controls() {

        /* -------------------------------------------------- Testimonials */
        $this->start_controls_section(
            'themeic_section_testimonial_content',
            [
                'label' => esc_html__( 'Testimonials', 'motionui-addons-for-elementor' ),
            ]
        );

        $styles  = $this->get_muia_testimonial_styles();
        $options = [];

        foreach ( $styles as $key => $style ) {
            $options[ $key ] = $style['label'];
        }

        $this->add_control(
            'themeic_testimonial_style',
            [
                'label'   => esc_html__( 'Card Style', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::SELECT,
                'default' => key( $styles ),
                'options' => $options,
            ]
        );

        $this->add_control(
            'themeic_testimonial_view',
            [
                'label'              => esc_html__( 'View Mode', 'motionui-addons-for-elementor' ),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'grid',
                'options'            => [
                    'grid'  => esc_html__( 'Grid', 'motionui-addons-for-elementor' ),
                    'slide' => esc_html__( 'Slide', 'motionui-addons-for-elementor' ),
                ],
                'frontend_available' => true,
                // The two views are different markup, not a class swap.
                'render_type'        => 'template',
            ]
        );

        $item = new Repeater();

        $item->add_control(
            'item_image',
            [
                'label'   => esc_html__( 'Photo', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [ 'url' => Utils::get_placeholder_image_src() ],
                'dynamic' => [ 'active' => true ],
            ]
        );

        $item->add_control(
            'item_name',
            [
                'label'       => esc_html__( 'Name', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Nancy Smith', 'motionui-addons-for-elementor' ),
                'label_block' => true,
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $item->add_control(
            'item_designation',
            [
                'label'       => esc_html__( 'Designation', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 2,
                'default'     => esc_html__( 'Chief Marketing Officer', 'motionui-addons-for-elementor' ),
                'description' => esc_html__( 'A line break here is kept, which is what the Quoted card does with a company name.', 'motionui-addons-for-elementor' ),
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $item->add_control(
            'item_content',
            [
                'label'   => esc_html__( 'Testimonial', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::TEXTAREA,
                'rows'    => 5,
                'default' => esc_html__( 'Exactly what was ordered, and the test of accuracy after the first delivery was over 99.7%. That is what we needed, so we are pleased.', 'motionui-addons-for-elementor' ),
                'dynamic' => [ 'active' => true ],
            ]
        );

        // Elementor evaluates a repeater control's condition against the item,
        // never against the widget, so these cannot be hidden when a design
        // does not draw them. The labels say which design uses what instead.
        $item->add_control(
            'item_rating',
            [
                'label'       => esc_html__( 'Rating', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::SELECT,
                'default'     => '5',
                'options'     => [
                    '0' => esc_html__( 'No stars', 'motionui-addons-for-elementor' ),
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                ],
                'description' => esc_html__( 'Drawn by the Rated card.', 'motionui-addons-for-elementor' ),
            ]
        );

        for ( $slot = 1; $slot <= self::SOCIAL_SLOTS; $slot++ ) {

            $item->add_control(
                'item_social_' . $slot . '_icon',
                [
                    'label'       => sprintf(
                        /* translators: %d: the social link's position, 1 to 3. */
                        esc_html__( 'Social Icon %d', 'motionui-addons-for-elementor' ),
                        $slot
                    ),
                    'type'        => Controls_Manager::ICONS,
                    'skin'        => 'inline',
                    'label_block' => false,
                    'description' => 1 === $slot
                        ? esc_html__( 'Drawn by the Split Author card.', 'motionui-addons-for-elementor' )
                        : '',
                    'separator'   => 1 === $slot ? 'before' : '',
                ]
            );

            $item->add_control(
                'item_social_' . $slot . '_url',
                [
                    'label'         => esc_html__( 'Link', 'motionui-addons-for-elementor' ),
                    'type'          => Controls_Manager::URL,
                    'placeholder'   => 'https://',
                    'show_external' => true,
                    'condition'     => [ 'item_social_' . $slot . '_icon[value]!' => '' ],
                ]
            );
        }

        $this->add_control(
            'themeic_testimonial_items',
            [
                'label'       => esc_html__( 'Items', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $item->get_controls(),
                'default'     => [
                    [ 'item_name' => esc_html__( 'Nancy Smith', 'motionui-addons-for-elementor' ) ],
                    [ 'item_name' => esc_html__( 'Amitai Davidson', 'motionui-addons-for-elementor' ) ],
                    [ 'item_name' => esc_html__( 'Jems Bond', 'motionui-addons-for-elementor' ) ],
                ],
                'title_field' => '{{{ item_name }}}',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Layout controls.
     *
     * The grid's own rules are declared through these selectors rather than in
     * the stylesheet, so the card CSS stays the card CSS and a rebuild of it
     * cannot take the layout with it.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_testimonial_layout_controls() {

        /* -------------------------------------------------- Layout */
        $this->start_controls_section(
            'themeic_section_testimonial_layout',
            [
                'label' => esc_html__( 'Layout', 'motionui-addons-for-elementor' ),
                'condition'      => [ 'themeic_testimonial_view' => 'grid' ],
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_columns',
            [
                'label'          => esc_html__( 'Columns', 'motionui-addons-for-elementor' ),
                'type'           => Controls_Manager::NUMBER,
                'min'            => 1,
                'max'            => 6,
                'default'        => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'selectors'      => [
                    '{{WRAPPER}} .muia-testimonial-grid' => 'display: grid; grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
                ]
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_gap',
            [
                'label'      => esc_html__( 'Gap', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px'  => [ 'min' => 0, 'max' => 100 ],
                    'em'  => [ 'min' => 0, 'max' => 6, 'step' => 0.1 ],
                    'rem' => [ 'min' => 0, 'max' => 6, 'step' => 0.1 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 24 ],
                'selectors'  => [
                    '{{WRAPPER}} .muia-testimonial-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();
    }  

    /**
     * Style controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_testimonial_style_controls() {

        /* -------------------------------------------------- Card */
        $this->start_controls_section(
            'themeic_section_testimonial_card_style',
            [
                'label' => esc_html__( 'Card', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'themeic_testimonial_card_bg',
            [
                'label'     => esc_html__( 'Background', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $this->muia_part_selector( 'card' ) => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_card_padding',
            [
                'label'      => esc_html__( 'Padding', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', 'rem' ],
                'selectors'  => [
                    $this->muia_part_selector( 'card' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_card_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    $this->muia_part_selector( 'card' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------- Text */
        $this->start_controls_section(
            'themeic_section_testimonial_text_style',
            [
                'label' => esc_html__( 'Text', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'themeic_testimonial_content_typography',
                'label'    => esc_html__( 'Testimonial', 'motionui-addons-for-elementor' ),
                'selector' => $this->muia_part_selector( 'content' ),
            ]
        );

        $this->add_control(
            'themeic_testimonial_content_color',
            [
                'label'     => esc_html__( 'Testimonial Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $this->muia_part_selector( 'content' ) => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'themeic_testimonial_name_typography',
                'label'     => esc_html__( 'Name', 'motionui-addons-for-elementor' ),
                'selector'  => $this->muia_part_selector( 'name' ),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'themeic_testimonial_name_color',
            [
                'label'     => esc_html__( 'Name Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $this->muia_part_selector( 'name' ) => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'themeic_testimonial_designation_typography',
                'label'     => esc_html__( 'Designation', 'motionui-addons-for-elementor' ),
                'selector'  => $this->muia_part_selector( 'designation' ),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'themeic_testimonial_designation_color',
            [
                'label'     => esc_html__( 'Designation Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    $this->muia_part_selector( 'designation' ) => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------- Photo & stars */
        $this->start_controls_section(
            'themeic_section_testimonial_media_style',
            [
                'label' => esc_html__( 'Photo & Stars', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_image_size',
            [
                'label'      => esc_html__( 'Photo Size', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 30, 'max' => 260 ] ],
                'selectors'  => [
                    $this->muia_part_selector( 'image' ) => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_image_radius',
            [
                'label'      => esc_html__( 'Photo Radius', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 130 ],
                    '%'  => [ 'min' => 0, 'max' => 50 ],
                ],
                'selectors'  => [
                    $this->muia_part_selector( 'image' ) => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // The stars are inline SVG painted with a fill, so the colour has to
        // reach the path rather than the list item.
        $this->add_control(
            'themeic_testimonial_star_color',
            [
                'label'     => esc_html__( 'Star Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .star__wrap svg path' => 'fill: {{VALUE}};',
                ],
                'condition' => [ 'themeic_testimonial_style' => 'card2' ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'themeic_testimonial_star_size',
            [
                'label'      => esc_html__( 'Star Size', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 8, 'max' => 48 ] ],
                'selectors'  => [
                    '{{WRAPPER}} .star__wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
                ],
                'condition'  => [ 'themeic_testimonial_style' => 'card2' ],
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Render the widget output on the frontend.
     *
     * @since  1.0.0
     * @return void
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        $items    = ! empty( $settings['themeic_testimonial_items'] ) ? $settings['themeic_testimonial_items'] : [];

        if ( empty( $items ) ) {
            return;
        }

        $style   = $this->get_muia_testimonial_style( $settings );
        $is_slide = 'slide' === ( isset( $settings['themeic_testimonial_view'] ) ? $settings['themeic_testimonial_view'] : 'grid' );

        $this->add_render_attribute( 'testimonial', 'class', [
            'muia-testimonial',
            'muia-testimonial--' . $style,
            $is_slide ? 'muia-testimonial--slide' : 'muia-testimonial--grid',
        ] );
        ?>
        <div <?php $this->print_render_attribute_string( 'testimonial' ); ?>>
            <?php if ( $is_slide ) : ?>
                <?php $this->render_muia_slider( $settings, $items, $style ); ?>
            <?php else : ?>
                <div class="muia-testimonial-grid">
                    <?php foreach ( $items as $item ) : ?>
                        <?php $this->render_muia_card( $style, $item ); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * The Swiper markup.
     *
     * Only the shell is built here. Every option it runs on is read back off
     * the element by muia-testimonial.js, which is why the slide controls are
     * frontend_available.
     *
     * @since  1.0.0
     * @param  array  $settings Widget settings.
     * @param  array  $items    Repeater items.
     * @param  string $style    Card style key.
     * @return void
     */
    protected function render_muia_slider( $settings, $items, $style ) {  
        ?>
        <div class="themeic-slide-wrapper" <?php $this->muia_slide_wrapper_attributes( 'muia_slide', $settings ); ?>>
            <div class="swiper">
                <div class="swiper-wrapper">
                    <?php foreach ( $items as $item ) : ?>
                        <div class="swiper-slide">
                            <?php $this->render_muia_card( $style, $item ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $this->muia_render_slide_nav( 'muia_slide', $settings ); ?>
        </div>
        <?php
    } 

    /**
     * One card, in whichever design is chosen.
     *
     * @since  1.0.0
     * @param  string $style Card style key.
     * @param  array  $item  Repeater item.
     * @return void
     */
    protected function render_muia_card( $style, $item ) {

        $styles = $this->get_muia_testimonial_styles();

        $classes = [ 'muia-testimonial-card', $styles[ $style ]['class'] ];

        if ( ! empty( $item['_id'] ) ) {
            $classes[] = 'elementor-repeater-item-' . $item['_id'];
        }

        $class = esc_attr( implode( ' ', $classes ) );

        switch ( $style ) {

            case 'card2':
                $this->render_muia_card2( $class, $item );
                break;

            case 'card3':
                $this->render_muia_card3( $class, $item );
                break;

            case 'long':
                $this->render_muia_card_long( $class, $item );
                break;

            case 'card10':
            default:
                $this->render_muia_card10( $class, $item );
                break;
        }
    }

    /* ---------------------------------------------------------- the designs */

    /**
     * Split Author: photo and author on the left, the quote on the right.
     *
     * @since  1.0.0
     * @param  string $class Root classes.
     * @param  array  $item  Repeater item.
     * @return void
     */
    protected function render_muia_card10( $class, $item ) {
        ?>
        <div class="<?php echo $class; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in render_muia_card(). ?>">
            <div class="author-col">
                <div class="author-wrapper">
                    <div class="author-content">
                        <div class="quote-thumb">
                            <span class="quote-icon"></span>
                            <?php $this->render_muia_photo( $item, 'thumb' ); ?>
                        </div>
                        <?php $this->render_muia_name( $item, 'h4', 'title' ); ?>
                        <?php $this->render_muia_designation( $item, 'p', 'designation' ); ?>
                    </div>
                    <?php $this->render_muia_socials( $item ); ?>
                </div>
            </div>
            <div class="text-col">
                <div class="text-content">
                    <?php $this->render_muia_content( $item ); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Rated: stars, the quote, then the author along the bottom.
     *
     * @since  1.0.0
     * @param  string $class Root classes.
     * @param  array  $item  Repeater item.
     * @return void
     */
    protected function render_muia_card2( $class, $item ) {
        ?>
        <div class="<?php echo $class; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in render_muia_card(). ?>">
            <?php $this->render_muia_stars( $item ); ?>
            <div class="des">
                <?php $this->render_muia_content( $item ); ?>
            </div>
            <div class="author-meta">
                <span class="img"><?php $this->render_muia_photo( $item ); ?></span>
                <div class="meta-details">
                    <?php $this->render_muia_name( $item, 'span', 'title' ); ?>
                    <?php $this->render_muia_designation( $item, 'span', 'info' ); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Quoted: author across the top, an oversized quote mark behind the text.
     *
     * @since  1.0.0
     * @param  string $class Root classes.
     * @param  array  $item  Repeater item.
     * @return void
     */
    protected function render_muia_card3( $class, $item ) {
        ?>
        <div class="<?php echo $class; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in render_muia_card(). ?>">
            <div class="avater-info d-flex align-items-center">
                <div class="avater">
                    <?php $this->render_muia_photo( $item ); ?>
                </div>
                <div class="avater-right-info">
                    <?php $this->render_muia_name( $item, 'h5' ); ?>
                    <?php $this->render_muia_designation( $item, 'p' ); ?>
                </div>
            </div>
            <div class="des">
                <span class="quote-icon">&ldquo;</span>
                <?php $this->render_muia_content( $item ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Wide: photo beside a long quote, author under a rule.
     *
     * @since  1.0.0
     * @param  string $class Root classes.
     * @param  array  $item  Repeater item.
     * @return void
     */
    protected function render_muia_card_long( $class, $item ) {
        ?>
        <div class="<?php echo $class; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in render_muia_card(). ?>">
            <div class="thumb">
                <?php $this->render_muia_photo( $item ); ?>
            </div>
            <div class="text-content">
                <?php $this->render_muia_content( $item ); ?>
                <div class="bottom-info">
                    <?php $this->render_muia_name( $item, 'h6', 'author-name' ); ?>
                    <?php $this->render_muia_designation( $item, 'span' ); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /* ---------------------------------------------------------- the pieces */

    /**
     * The author's photo.
     *
     * @since  1.0.0
     * @param  array  $item  Repeater item.
     * @param  string $class Optional class for the img itself.
     * @return void
     */
    protected function render_muia_photo( $item, $class = '' ) {

        $url = ! empty( $item['item_image']['url'] ) ? $item['item_image']['url'] : '';

        if ( '' === $url ) {
            return;
        }

        printf(
            '<img%1$s src="%2$s" alt="%3$s" loading="lazy">',
            $class ? ' class="' . esc_attr( $class ) . '"' : '',
            esc_url( $url ),
            esc_attr( isset( $item['item_name'] ) ? $item['item_name'] : '' )
        );
    }

    /**
     * The author's name.
     *
     * @since  1.0.0
     * @param  array  $item  Repeater item.
     * @param  string $tag   Element to wrap it in.
     * @param  string $class Optional class.
     * @return void
     */
    protected function render_muia_name( $item, $tag, $class = '' ) {

        $name = isset( $item['item_name'] ) ? $item['item_name'] : '';

        if ( '' === $name ) {
            return;
        }

        printf(
            '<%1$s%2$s>%3$s</%1$s>',
            esc_attr( $tag ),
            $class ? ' class="' . esc_attr( $class ) . '"' : '',
            esc_html( $name )
        );
    }

    /**
     * The author's role.
     *
     * Line breaks the author typed are kept, which is what the Quoted card
     * does with a company name on its own line.
     *
     * @since  1.0.0
     * @param  array  $item  Repeater item.
     * @param  string $tag   Element to wrap it in.
     * @param  string $class Optional class.
     * @return void
     */
    protected function render_muia_designation( $item, $tag, $class = '' ) {

        $role = isset( $item['item_designation'] ) ? $item['item_designation'] : '';

        if ( '' === trim( $role ) ) {
            return;
        }

        printf(
            '<%1$s%2$s>%3$s</%1$s>',
            esc_attr( $tag ),
            $class ? ' class="' . esc_attr( $class ) . '"' : '',
            nl2br( esc_html( $role ) )
        );
    }

    /**
     * The quote itself.
     *
     * @since  1.0.0
     * @param  array $item Repeater item.
     * @return void
     */
    protected function render_muia_content( $item ) {

        $text = isset( $item['item_content'] ) ? $item['item_content'] : '';

        if ( '' === trim( $text ) ) {
            return;
        }

        echo '<p>' . nl2br( esc_html( $text ) ) . '</p>';
    }

    /**
     * The rating, as filled stars.
     *
     * @since  1.0.0
     * @param  array $item Repeater item.
     * @return void
     */
    protected function render_muia_stars( $item ) {

        $rating = isset( $item['item_rating'] ) ? (int) $item['item_rating'] : 0;
        $rating = max( 0, min( 5, $rating ) );

        if ( 0 === $rating ) {
            return;
        }

        $star = '<svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
              . '<path fill-rule="evenodd" clip-rule="evenodd" d="M9.4272 1.2044C10.0821 -0.0681325 11.9179 -0.0681325 12.5728 1.2044L14.7793 5.49233C14.8159 5.56348 14.8847 5.613 14.9643 5.62556L19.7606 6.38205C21.1832 6.60643 21.7501 8.3328 20.7328 9.343L17.2992 12.7525C17.2423 12.809 17.2161 12.8889 17.2287 12.9677L17.9855 17.7262C18.2098 19.1365 16.7249 20.2039 15.4405 19.5559L11.1145 17.3734C11.0426 17.3371 10.9574 17.3371 10.8855 17.3734L6.55953 19.5559C5.27505 20.2039 3.79024 19.1365 4.01453 17.7262L4.77134 12.9677C4.78387 12.8889 4.75766 12.809 4.70079 12.7525L1.26718 9.343C0.249929 8.3328 0.816808 6.60643 2.23941 6.38205L7.03567 5.62556C7.1153 5.613 7.18407 5.56348 7.22069 5.49233L9.4272 1.2044Z" fill="#FFB800"/>'
              . '</svg>';

        echo '<ul class="star__wrap" role="img" aria-label="'
            . esc_attr( sprintf(
                /* translators: %d: the rating, 1 to 5. */
                _n( '%d star', '%d stars', $rating, 'motionui-addons-for-elementor' ),
                $rating
            ) )
            . '">';

        for ( $i = 0; $i < $rating; $i++ ) {
            echo '<li>' . $star . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- a literal above.
        }

        echo '</ul>';
    }

    /**
     * The author's social links.
     *
     * @since  1.0.0
     * @param  array $item Repeater item.
     * @return void
     */
    protected function render_muia_socials( $item ) {

        $links = [];

        for ( $slot = 1; $slot <= self::SOCIAL_SLOTS; $slot++ ) {

            $icon = isset( $item[ 'item_social_' . $slot . '_icon' ] ) ? $item[ 'item_social_' . $slot . '_icon' ] : [];

            if ( empty( $icon['value'] ) ) {
                continue;
            }

            $links[] = [
                'icon' => $icon,
                'url'  => isset( $item[ 'item_social_' . $slot . '_url' ] ) ? $item[ 'item_social_' . $slot . '_url' ] : [],
            ];
        }

        if ( empty( $links ) ) {
            return;
        }
        ?>
        <ul class="social-icons">
            <?php foreach ( $links as $index => $link ) : ?>
                <?php
                $key = 'social_' . $item['_id'] . '_' . $index;

                $this->add_render_attribute( $key, 'href', ! empty( $link['url']['url'] ) ? $link['url']['url'] : '#' );

                if ( ! empty( $link['url']['is_external'] ) ) {
                    $this->add_render_attribute( $key, 'target', '_blank' );
                }

                if ( ! empty( $link['url']['nofollow'] ) ) {
                    $this->add_render_attribute( $key, 'rel', 'nofollow' );
                }
                ?>
                <li>
                    <a <?php $this->print_render_attribute_string( $key ); ?>>
                        <?php Icons_Manager::render_icon( $link['icon'], [ 'aria-hidden' => 'true' ] ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }
}
