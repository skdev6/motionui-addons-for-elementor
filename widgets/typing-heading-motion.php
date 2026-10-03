<?php
/**
 * Typing Heading Motion Widget
 *
 * A heading whose last few words cycle: typed out letter by letter, rotated,
 * clipped, pushed and so on. The animation itself is the bundled
 * jquery.animatedheadline plugin — this widget builds the markup it expects,
 * hands it its options as JSON, and exposes the parts worth styling.
 *
 * Markup the plugin needs:
 *
 *     <div class="muia-animated-headline" data-muia-headline="{…}">
 *         <h2 class="ah-headline">
 *             <span>static text</span>
 *             <span class="ah-words-wrapper">
 *                 <b class="is-visible">first word</b>
 *                 <b>second word</b>
 *             </span>
 *         </h2>
 *     </div>
 *
 * The plugin is called on the outer div rather than the heading, because it
 * looks for `.ah-headline` *inside* the element it is given.
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

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Typing_Heading_Motion
 *
 * @since 1.0.0
 */
class Typing_Heading_Motion extends Muia_Base {

    /**
     * The animations the bundled stylesheet can draw.
     *
     * Keyed by the class the plugin puts on `.ah-headline`, which is also the
     * `animationType` option it is given.
     *
     * @since  1.0.0
     * @return array<string,string>
     */
    protected function get_muia_headline_types() {
        return [
            'rotate-1'    => esc_html__( 'Rotate', 'motionui-addons-for-elementor' ),
            'type'        => esc_html__( 'Typing', 'motionui-addons-for-elementor' ),
            'rotate-2'    => esc_html__( 'Flip Letters', 'motionui-addons-for-elementor' ),
            'rotate-3'    => esc_html__( 'Spin Letters', 'motionui-addons-for-elementor' ),
            'scale'       => esc_html__( 'Scale Letters', 'motionui-addons-for-elementor' ),
            'loading-bar' => esc_html__( 'Loading Bar', 'motionui-addons-for-elementor' ),
            'slide'       => esc_html__( 'Slide', 'motionui-addons-for-elementor' ),
            'clip'        => esc_html__( 'Clip', 'motionui-addons-for-elementor' ),
            'zoom'        => esc_html__( 'Zoom', 'motionui-addons-for-elementor' ),
            'push'        => esc_html__( 'Push', 'motionui-addons-for-elementor' ),
        ];
    }

    /**
     * The animations that split a word into letters.
     *
     * These are the ones `lettersDelay` applies to; `type` splits letters too
     * but paces itself with its own two controls.
     *
     * @since  1.0.0
     * @return string[]
     */
    protected function get_muia_letter_types() {
        return [ 'rotate-2', 'rotate-3', 'scale' ];
    }

    /**
     * Tags a heading may render as.
     *
     * @since  1.0.0
     * @return array<string,string>
     */
    protected function get_muia_headline_tags() {
        return [
            'h1'   => 'H1',
            'h2'   => 'H2',
            'h3'   => 'H3',
            'h4'   => 'H4',
            'h5'   => 'H5',
            'h6'   => 'H6',
            'div'  => 'div',
            'p'    => 'p',
            'span' => 'span',
        ];
    }

    /**
     * Register widget controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function register_controls() {
        $this->_register_muia_headline_content_controls();
        $this->_register_muia_headline_animation_controls();
        $this->_register_muia_headline_style_controls();
    }

    /**
     * Content controls.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_headline_content_controls() {

        /* -------------------------------------------------- Heading */
        $this->start_controls_section(
            'themeic_section_headline_content',
            [
                'label' => esc_html__( 'Heading', 'motionui-addons-for-elementor' ),
            ]
        );

        $this->add_control(
            'themeic_headline_before',
            [
                'label'       => esc_html__( 'Text Before', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Hello i am expert in', 'motionui-addons-for-elementor' ),
                'placeholder' => esc_html__( 'Hello i am expert in', 'motionui-addons-for-elementor' ),
                'label_block' => true,
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $word = new Repeater();

        $word->add_control(
            'themeic_word_text',
            [
                'label'       => esc_html__( 'Word', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'JavaScript', 'motionui-addons-for-elementor' ),
                'label_block' => true,
                'dynamic'     => [ 'active' => true ],
                // Letter animations rebuild the word from its characters, so a
                // word is plain text: markup inside one would be dropped.
                'description' => esc_html__( 'Plain text only — the letter animations rebuild each word character by character.', 'motionui-addons-for-elementor' ),
            ]
        );

        $word->add_control(
            'themeic_word_color',
            [
                'label'     => esc_html__( 'Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'themeic_headline_words',
            [
                'label'       => esc_html__( 'Rotating Words', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $word->get_controls(),
                'default'     => [
                    [ 'themeic_word_text' => esc_html__( 'JavaScript', 'motionui-addons-for-elementor' ) ],
                    [ 'themeic_word_text' => esc_html__( 'Python', 'motionui-addons-for-elementor' ) ],
                    [ 'themeic_word_text' => esc_html__( 'Swift', 'motionui-addons-for-elementor' ) ],
                ],
                'title_field' => '{{{ themeic_word_text }}}',
            ]
        );

        $this->add_control(
            'themeic_headline_after',
            [
                'label'       => esc_html__( 'Text After', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'development', 'motionui-addons-for-elementor' ),
                'label_block' => true,
                'dynamic'     => [ 'active' => true ],
            ]
        );

        $this->add_control(
            'themeic_headline_tag',
            [
                'label'     => esc_html__( 'HTML Tag', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'h2',
                'options'   => $this->get_muia_headline_tags(),
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'themeic_headline_align',
            [
                'label'     => esc_html__( 'Alignment', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__( 'Left', 'motionui-addons-for-elementor' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'motionui-addons-for-elementor' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__( 'Right', 'motionui-addons-for-elementor' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .muia-animated-headline' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Animation controls.
     *
     * Each one is an option the plugin already takes, so the names here follow
     * what it calls them rather than inventing a second vocabulary. Everything
     * is in milliseconds.
     *
     * @since  1.0.0
     * @return void
     */
    protected function _register_muia_headline_animation_controls() {

        /* -------------------------------------------------- Animation */
        $this->start_controls_section(
            'themeic_section_headline_animation',
            [
                'label' => esc_html__( 'Typing Animation', 'motionui-addons-for-elementor' ),
            ]
        );

        $this->add_control(
            'themeic_headline_type',
            [
                'label'   => esc_html__( 'Animation', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'type',
                'options' => $this->get_muia_headline_types(),
            ]
        );

        // The bar carries its own pace, so the shared delay would do nothing.
        $this->add_control(
            'themeic_headline_delay',
            [
                'label'       => esc_html__( 'Word Duration', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 400,
                'max'         => 20000,
                'step'        => 100,
                'default'     => 2500,
                'description' => esc_html__( 'How long a word stays, in milliseconds.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_headline_type!' => 'loading-bar' ],
            ]
        );

        $this->add_control(
            'themeic_headline_letters_delay',
            [
                'label'       => esc_html__( 'Letter Delay', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 0,
                'max'         => 1000,
                'step'        => 10,
                'default'     => 50,
                'description' => esc_html__( 'Between one letter and the next.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_headline_type' => $this->get_muia_letter_types() ],
            ]
        );

        /* ---- typing */

        $this->add_control(
            'themeic_headline_type_letters_delay',
            [
                'label'     => esc_html__( 'Typing Speed', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 0,
                'max'       => 1000,
                'step'      => 10,
                'default'   => 150,
                'condition' => [ 'themeic_headline_type' => 'type' ],
            ]
        );

        $this->add_control(
            'themeic_headline_selection_duration',
            [
                'label'       => esc_html__( 'Selection Hold', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 0,
                'max'         => 5000,
                'step'        => 50,
                'default'     => 500,
                'description' => esc_html__( 'How long the word stays highlighted before it is deleted.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_headline_type' => 'type' ],
            ]
        );

        $this->add_control(
            'themeic_headline_type_delay',
            [
                'label'       => esc_html__( 'Pause Before Typing', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 0,
                'max'         => 10000,
                'step'        => 100,
                'default'     => 1300,
                'condition'   => [ 'themeic_headline_type' => 'type' ],
            ]
        );

        /* ---- loading bar */

        $this->add_control(
            'themeic_headline_bar_delay',
            [
                'label'       => esc_html__( 'Word Duration', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 400,
                'max'         => 20000,
                'step'        => 100,
                'default'     => 3800,
                'description' => esc_html__( 'How long a word stays while the bar fills.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_headline_type' => 'loading-bar' ],
            ]
        );

        $this->add_control(
            'themeic_headline_bar_waiting',
            [
                'label'       => esc_html__( 'Bar Start Delay', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 0,
                'max'         => 10000,
                'step'        => 50,
                'default'     => 800,
                'description' => esc_html__( 'How long the bar waits before it starts filling again.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_headline_type' => 'loading-bar' ],
            ]
        );

        /* ---- clip */

        $this->add_control(
            'themeic_headline_reveal_duration',
            [
                'label'     => esc_html__( 'Reveal Duration', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::NUMBER,
                'min'       => 0,
                'max'       => 5000,
                'step'      => 50,
                'default'   => 600,
                'condition' => [ 'themeic_headline_type' => 'clip' ],
            ]
        );

        $this->add_control(
            'themeic_headline_reveal_delay',
            [
                'label'       => esc_html__( 'Reveal Hold', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::NUMBER,
                'min'         => 0,
                'max'         => 10000,
                'step'        => 100,
                'default'     => 1500,
                'description' => esc_html__( 'How long a word stays open before the clip closes again.', 'motionui-addons-for-elementor' ),
                'condition'   => [ 'themeic_headline_type' => 'clip' ],
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
    protected function _register_muia_headline_style_controls() {

        /* -------------------------------------------------- Heading */
        $this->start_controls_section(
            'themeic_section_headline_style',
            [
                'label' => esc_html__( 'Heading', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'themeic_headline_typography',
                'selector' => '{{WRAPPER}} .ah-headline',
            ]
        );

        $this->add_control(
            'themeic_headline_color',
            [
                'label'     => esc_html__( 'Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-headline' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'themeic_headline_gap',
            [
                'label'      => esc_html__( 'Word Spacing', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px'  => [ 'min' => 0, 'max' => 60 ],
                    'em'  => [ 'min' => 0, 'max' => 3, 'step' => 0.05 ],
                    'rem' => [ 'min' => 0, 'max' => 3, 'step' => 0.05 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ah-words-wrapper' => 'margin-inline: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------- Words */
        $this->start_controls_section(
            'themeic_section_headline_words_style',
            [
                'label' => esc_html__( 'Rotating Words', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Typography only, plus a colour. The plugin sizes the slot from the
        // widest word it measures, so anything that changes a word's box —
        // padding, a border — is left off on purpose: it would be measured
        // before it applied and the slot would come out too narrow.
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'themeic_headline_words_typography',
                'selector' => '{{WRAPPER}} .ah-words-wrapper b',
            ]
        );

        $this->add_control(
            'themeic_headline_words_color',
            [
                'label'     => esc_html__( 'Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-words-wrapper b' => 'color: {{VALUE}};',
                ],
                'description' => esc_html__( 'A word with its own colour in the list above keeps that one.', 'motionui-addons-for-elementor' ),
            ]
        );

        $this->add_control(
            'themeic_headline_slot_bg',
            [
                'label'     => esc_html__( 'Slot Background', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-words-wrapper' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        /* ---- the parts only some animations draw */

        $this->add_control(
            'themeic_headline_cursor_color',
            [
                'label'     => esc_html__( 'Cursor Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-headline.type .ah-words-wrapper::after, {{WRAPPER}} .ah-headline.clip .ah-words-wrapper::after' => 'background-color: {{VALUE}};',
                ],
                'condition' => [ 'themeic_headline_type' => [ 'type', 'clip' ] ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'themeic_headline_selection_bg',
            [
                'label'     => esc_html__( 'Selection Background', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-headline.type .ah-words-wrapper.selected' => 'background-color: {{VALUE}};',
                ],
                'condition' => [ 'themeic_headline_type' => 'type' ],
            ]
        );

        $this->add_control(
            'themeic_headline_selection_color',
            [
                'label'     => esc_html__( 'Selection Text', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-headline.type .ah-words-wrapper.selected b' => 'color: {{VALUE}};',
                ],
                'condition' => [ 'themeic_headline_type' => 'type' ],
            ]
        );

        $this->add_control(
            'themeic_headline_bar_color',
            [
                'label'     => esc_html__( 'Bar Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ah-headline.loading-bar .ah-words-wrapper::after' => 'background-color: {{VALUE}};',
                ],
                'condition' => [ 'themeic_headline_type' => 'loading-bar' ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'themeic_headline_bar_height',
            [
                'label'      => esc_html__( 'Bar Height', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 1, 'max' => 20 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ah-headline.loading-bar .ah-words-wrapper::after' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [ 'themeic_headline_type' => 'loading-bar' ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * The options handed to the headline plugin.
     *
     * Only the keys the chosen animation actually reads are sent, so the
     * attribute stays readable and a stray control cannot change an animation
     * it has nothing to do with.
     *
     * @since  1.0.0
     * @param  array $settings Widget settings.
     * @return array
     */
    protected function get_muia_headline_options( $settings ) {

        $types = $this->get_muia_headline_types();
        $type  = isset( $settings['themeic_headline_type'] ) ? $settings['themeic_headline_type'] : 'type';
        $type  = isset( $types[ $type ] ) ? $type : 'type';

        $number = function ( $key, $fallback ) use ( $settings ) {
            return ( isset( $settings[ $key ] ) && '' !== $settings[ $key ] )
                ? absint( $settings[ $key ] )
                : $fallback;
        };

        $options = [ 'animationType' => $type ];

        if ( 'loading-bar' === $type ) {
            $options['barAnimationDelay'] = $number( 'themeic_headline_bar_delay', 3800 );
            $options['barWaiting']        = $number( 'themeic_headline_bar_waiting', 800 );

            return $options;
        }

        $options['animationDelay'] = $number( 'themeic_headline_delay', 2500 );

        if ( 'type' === $type ) {
            $options['typeLettersDelay']   = $number( 'themeic_headline_type_letters_delay', 150 );
            $options['selectionDuration']  = $number( 'themeic_headline_selection_duration', 500 );
            $options['typeAnimationDelay'] = $number( 'themeic_headline_type_delay', 1300 );
        }

        if ( in_array( $type, $this->get_muia_letter_types(), true ) ) {
            $options['lettersDelay'] = $number( 'themeic_headline_letters_delay', 50 );
        }

        if ( 'clip' === $type ) {
            $options['revealDuration']       = $number( 'themeic_headline_reveal_duration', 600 );
            $options['revealAnimationDelay'] = $number( 'themeic_headline_reveal_delay', 1500 );
        }

        return $options;
    }

    /**
     * Render the widget output on the frontend.
     *
     * @since  1.0.0
     * @return void
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        $words    = ! empty( $settings['themeic_headline_words'] ) ? $settings['themeic_headline_words'] : [];

        $tags = $this->get_muia_headline_tags();
        $tag  = isset( $settings['themeic_headline_tag'] ) ? $settings['themeic_headline_tag'] : 'h2';
        $tag  = isset( $tags[ $tag ] ) ? $tag : 'h2';

        $before = isset( $settings['themeic_headline_before'] ) ? $settings['themeic_headline_before'] : '';
        $after  = isset( $settings['themeic_headline_after'] ) ? $settings['themeic_headline_after'] : '';

        $this->add_render_attribute( 'headline', [
            'class' => 'muia-animated-headline',
            // Read by typingHeading() in muia-addons.js, which hands the whole
            // object straight to the plugin.
            'data-muia-headline' => wp_json_encode( $this->get_muia_headline_options( $settings ) ),
        ] );
        ?>
        <div <?php $this->print_render_attribute_string( 'headline' ); ?>>
            <<?php echo esc_attr( $tag ); ?> class="ah-headline">

                <?php if ( '' !== $before ) : ?>
                    <span class="muia-headline-before"><?php echo esc_html( $before ); ?></span>
                <?php endif; ?>

                <?php if ( $words ) : ?>
                    <span class="ah-words-wrapper">
                        <?php $is_first = true; ?>
                        <?php foreach ( $words as $index => $word ) : ?>
                            <?php
                            $text = isset( $word['themeic_word_text'] ) ? $word['themeic_word_text'] : '';

                            if ( '' === $text ) {
                                continue;
                            }

                            $key = 'word_' . $index;

                            if ( ! empty( $word['_id'] ) ) {
                                $this->add_render_attribute( $key, 'class', 'elementor-repeater-item-' . $word['_id'] );
                            }

                            // The plugin starts from whichever word is already
                            // visible, so exactly one has to be — the first one
                            // that actually renders, not the first in the list.
                            if ( $is_first ) {
                                $this->add_render_attribute( $key, 'class', 'is-visible' );
                                $is_first = false;
                            }
                            ?>
                            <b <?php $this->print_render_attribute_string( $key ); ?>><?php echo esc_html( $text ); ?></b>
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>

                <?php if ( '' !== $after ) : ?>
                    <span class="muia-headline-after"><?php echo esc_html( $after ); ?></span>
                <?php endif; ?>

            </<?php echo esc_attr( $tag ); ?>>
        </div>
        <?php
    }
}
