<?php
/**
 * Animated Slider Widget
 *
 * @package     MotionUI Addons for Elementor
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Animated_Slider
 *
 * @since 1.0.0
 */
class Animated_Slider extends Muia_Base {

    /**
     * Retrieve widget keywords.
     */
    public function get_keywords() {
        return [ 'slider', 'slide', 'animated slider', 'hero', 'banner', 'slideshow', 'motionui' ];
    }
    /**
     * Register widget controls.
     */
    protected function register_controls() {

        // ==================== Content Section ====================
        $this->start_controls_section(
            'slide_content_section',
            [
                'label' => esc_html__( 'Content', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $slide_repeater = new \Elementor\Repeater();

        $slide_repeater->add_control(
            'slide_title',
            [
                'label'       => esc_html__( 'Title', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Slide Title', 'motionui-addons-for-elementor' ),
                'label_block' => true,
            ]
        );

        $slide_repeater->add_control(
            'slide_link',
            [
                'label'         => esc_html__( 'Link', 'motionui-addons-for-elementor' ),
                'type'          => Controls_Manager::URL,
                'placeholder'   => esc_html__( 'https://your-link.com', 'motionui-addons-for-elementor' ),
                'show_external' => true,
                'default'       => [
                    'url'         => '',
                    'is_external' => false,
                    'nofollow'    => false,
                ],
                'label_block'   => true,
            ]
        );
		$slide_repeater->add_control(
			'slide_background',
			[
				'label' => esc_html__( 'Choose Image', 'motionui-addons-for-elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
                'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.sb-item,{{WRAPPER}} {{CURRENT_ITEM}} .sb-item' => 'background-image: url({{url}})',
				],
			]
		);
        $this->add_control(
            'slide_list',
            [
                'label'       => esc_html__( 'Slides', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $slide_repeater->get_controls(),
                'default'     => [
                    [
                        'slide_title' => esc_html__( 'Hello Title', 'motionui-addons-for-elementor' ),
                    ],
                    [
                        'slide_title' => esc_html__( 'UI Design', 'motionui-addons-for-elementor' ),
                    ],
                    [
                        'slide_title' => esc_html__( 'UX Design', 'motionui-addons-for-elementor' ),
                    ],
                ],
                'title_field' => '{{{ slide_title }}}',
            ]
        );

        $this->end_controls_section();

        // ==================== Pagination Section ====================
        $this->start_controls_section(
            'pagi_content_section',
            [
                'label' => esc_html__( 'Pagination', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'prev_p_icon',
            [
                'label'       => esc_html__( 'Previous Icon', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::ICONS,
                'skin'        => 'inline',
                'default'     => [
                    'value'   => 'fas fa-arrow-left',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'next_p_icon',
            [
                'label'       => esc_html__( 'Next Icon', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::ICONS,
                'skin'        => 'inline',
                'default'     => [
                    'value'   => 'fas fa-arrow-right',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->end_controls_section();

        // ==================== Style Sections ====================
        $this->start_controls_section(
            'slide_style',
            [
                'label' => esc_html__( 'Slide Style', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Overlay', 'motionui-addons-for-elementor' ),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
            'slide_style_title',
            [
                'label' => esc_html__( 'Title Style', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography_title',
                'selector' => '{{WRAPPER}} .slide-title-item',
            ]
        );

        $this->add_control(
            'slide_title_color',
            [
                'label'     => esc_html__( 'Title Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slide-title-item' => '--color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'slide_pagi_style',
            [
                'label' => esc_html__( 'Pagination Style', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'pagi_typography',
                'selector' => '{{WRAPPER}} .pagi',
            ]
        );

        $this->add_control(
            'pagi_text_color',
            [
                'label'     => esc_html__( 'Pagination Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pagi-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $slides   = $settings['slide_list'] ?? [];

        if ( empty( $slides ) ) {
            return;
        }
        ?>

        <div class="muia-slide-basic">
            
            <!-- Backgrounds -->
            <div class="slide-bg-wrap">
                <div class="overlay"></div>
                <?php 
                $slide_count = 0;
                foreach ( $slides as $slide ) : 
                    $slide_count++;
                ?>
                    <div class="slide-bg-item elementor-repeater-item-<?php printf( '%s%s', esc_attr( $slide['_id'] ), $slide_count === 0 ? ' active' : '' ); ?>" 
                         style="--index: <?php echo esc_attr( $slide_count ); ?>;">
                         <div class="sb-item"></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Titles -->
            <div class="title-wrapper">
                <?php 
                $count = 0;
                foreach ( $slides as $slide ) : 
                    $link   = $slide['slide_link'] ?? [];
                    $url    = ! empty( $link['url'] ) ? $link['url'] : '';
                    $target = ! empty( $link['is_external'] ) ? '_blank' : '_self';
                    $rel    = ! empty( $link['nofollow'] ) ? 'nofollow' : '';
                ?>
                    <?php if ( ! empty( $url ) ) : ?>
                        <a href="<?php echo esc_url( $url ); ?>" 
                        target="<?php echo esc_attr( $target ); ?>" 
                        rel="<?php echo esc_attr( $rel ); ?>" 
                        class="slide-title-item elementor-repeater-item-<?php printf( '%s%s', esc_attr( $slide['_id'] ), $count === 0 ? ' active' : '' ); ?>">
                            <?php echo esc_html( $slide['slide_title'] ); ?>
                        </a>
                    <?php else : ?>
                        <h2 class="slide-title-item elementor-repeater-item-<?php printf( '%s%s', esc_attr( $slide['_id'] ), $count === 0 ? ' active' : '' ); ?>">
                            <?php echo esc_html( $slide['slide_title'] );  ?>
                        </h2>
                    <?php endif; $count++; ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="pagi-wrapper">
                <div class="muia-dot-pagi">
                    <?php $count=0;  foreach ( $slides as $slide ) : ?>
                            <div class="dot-item<?php echo $count === 0 ? ' active' : ''; ?>" data-go="<?php echo esc_attr($count) ?>"><span></span></div>  
                        <?php $count++; endforeach; ?>
                </div>
                <div class="muia-thumb-pagi-wrapper">
                    <div class="muia-thumb-pagi">
                        <?php $count=0; foreach ( $slides as $slide ) : ?>
                            <div class="pagi-thumb sb-item elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ); echo $count === 0 ? ' active' : ''; ?>" data-go="<?php echo esc_attr($count) ?>"></div>
                        <?php $count++; endforeach; ?>
                    </div>
                    <div class="muia-slide-nav">
                        <button class="muia-prev"><i class="th-icon-angle-left"></i></button>
                        <button class="muia-next"><i class="th-icon-angle-right"></i></button>
                    </div>
                </div>
            </div>

        </div>

        <?php
    }

}