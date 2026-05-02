<?php
/**
 * Animated Gallery Widget
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
    exit;
}

class Animated_Gallery extends Muia_Base {

    public function get_keywords() {
        return [ 'gallery', 'slide', 'banner', 'image', 'motionui' ];
    }
	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
    protected function register_controls() {

        // ==================== Content Section ====================
        $this->start_controls_section(
            'gallery_content_section',
            [
                'label' => esc_html__( 'Content', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $gallery_repeater = new \Elementor\Repeater();

        $gallery_repeater->add_control(
            'gallery_title',
            [
                'label'       => esc_html__( 'Title', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Slide Title', 'motionui-addons-for-elementor' ),
                'label_block' => true,
            ]
        );

        $gallery_repeater->add_control(
            'gallery_link',
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

        $gallery_repeater->add_control(
            'gallery_image',
            [
                'label'   => esc_html__( 'Gallery Image', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'gallery_list',
            [
                'label'       => esc_html__( 'Gallery', 'motionui-addons-for-elementor' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $gallery_repeater->get_controls(),
                'default'     => [
                    [ 'gallery_title' => esc_html__( 'Hello Title', 'motionui-addons-for-elementor' ) ],
                    [ 'gallery_title' => esc_html__( 'UI Design',   'motionui-addons-for-elementor' ) ],
                    [ 'gallery_title' => esc_html__( 'UX Design',   'motionui-addons-for-elementor' ) ],
                ],
                'title_field' => '{{{ gallery_title }}}',
            ]
        );

        $this->add_control(
            'gallery_layout',
            [
                'label'   => esc_html__( 'Layout', 'motionui-addons-for-elementor' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'grid' => esc_html__( 'Grid',      'motionui-addons-for-elementor' ),
                    'justified' => esc_html__( 'Justified', 'motionui-addons-for-elementor' ),
                    // 'masonry' => esc_html__( 'Masonry',   'motionui-addons-for-elementor' ),
                    // 'inline' => esc_html__( 'Inline',    'motionui-addons-for-elementor' ),
                ],
                'default' => 'grid',
            ]
        );

        $this->add_responsive_control(
            'gallery_columns',
            [
                'label'     => esc_html__( 'Columns', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '3',
                'options'   => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .muia-gallery-wrap' => '--grid-col: {{VALUE}};',
                ],
                'condition' => [ 'gallery_layout!' => ['inline', 'justified'] ],
            ]
        );

        $this->end_controls_section();

        // ==================== Style Section ====================
        $this->start_controls_section(
            'gallery_style',
            [
                'label' => esc_html__( 'Gallery Style', 'motionui-addons-for-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'images_height',
            [
                'label'      => esc_html__( 'Image Height', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'vh', 'custom' ],
                'range'      => [
                    'px' => [ 'min' => 50, 'max' => 800 ],
                    'vh' => [ 'min' => 10, 'max' => 100 ],
                ],
                'default'    => [   
                    'unit' => 'px',
                    'size' => 300, // fixed: 20px was too small to see anything
                ],
                // fixed: missing selectors — without this the control does nothing
                'selectors'  => [
                    '{{WRAPPER}} .muia-gallery-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'gallery_gap',
            [
                'label'      => esc_html__( 'Gap', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 10 ],
                'selectors'  => [
                    '{{WRAPPER}} .muia-gallery-wrap' => 'gap: {{SIZE}}{{UNIT}}; --gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'motionui-addons-for-elementor' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'default'    => [ 'unit' => 'px', 'size' => 0 ],
                'selectors'  => [
                    '{{WRAPPER}} .muia-gallery-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'ttcontent_typography',
                'selector' => '{{WRAPPER}} .muia-gallery-title',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'     => esc_html__( 'Text Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .muia-gallery-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label'     => esc_html__( 'Overlay Color', 'motionui-addons-for-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => 'rgba(0,0,0,0.4)',
                'selectors' => [
                    '{{WRAPPER}} .muia-gallery-overlay' => 'background-color: {{VALUE}};',
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
        $items    = $settings['gallery_list'] ?? [];
        $layout   = $settings['gallery_layout'] ?? 'grid';

        if ( empty( $items ) ) {
            return;
        }

        $wrap_classes = implode( ' ', [
            'muia-gallery-wrap',
            'muia-layout-' . esc_attr( $layout ),
        ] );
        ?>

        <div class="<?php echo esc_attr( $wrap_classes ); ?>">

            <?php foreach ( $items as $item ) :
                $image_url = $item['gallery_image']['url'] ?? \Elementor\Utils::get_placeholder_image_src();
                $title     = $item['gallery_title'] ?? '';
                $link      = $item['gallery_link']  ?? [];
                $item_key  = 'gallery-item-' . $item['_id'];

                // Base attributes always present on the <a>
                $this->add_render_attribute( $item_key, [
                    'class' => [
                        'muia-gallery-item',
                        'elementor-repeater-item-' . $item['_id'],
                    ],
                    'href'  => ! empty( $link['url'] ) ? esc_url( $link['url'] ) : esc_url( $image_url ),
                ] );

                // Let Elementor handle target, rel, nofollow, noopener automatically
                if ( ! empty( $link['url'] ) ) {
                    $this->add_link_attributes( $item_key, $link );
                }
                ?>

                <a <?php $this->print_render_attribute_string( $item_key ); ?>>

                    <img
                        class="muia-gallery-image"
                        src="<?php echo esc_url( $image_url ); ?>"
                        alt="<?php echo esc_attr( $title ); ?>"
                        loading="lazy"
                    />

                    <div class="muia-gallery-overlay">
                        <?php if ( ! empty( $title ) ) : ?>
                            <span class="muia-gallery-title">
                                <?php echo esc_html( $title ); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                </a>

            <?php endforeach; ?>

        </div>

        <?php
    }
}