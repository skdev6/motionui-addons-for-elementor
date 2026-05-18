<?php
/**
 * Card Helper Trait
 *
 * @package     MotionUI_Addons
 * @subpackage  Traits
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Cards {
	use Custom_Control;
	/**
	 * Render card markup.
	 *
	 * @param array $args Card arguments.
	 * @return void
	 */
	public function muia_post_card( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'card_type' => 'grid', // grid, list, list-2.
				'subtitle'  => '',
				'title'     => '',
				'terms'     => array(),
				'url'       => '',
				'excerpt'   => '',
				'thumbnail_url' => '',
				'thumbnail_alt' => '',
				'show_arrow'=> false,
			)
		);

		$card_classes = array(
			'muia-card',
			sanitize_html_class( $args['card_type'] ),
		);

		$thumbnail_alt = ! empty( $args['thumbnail_alt'] ) ? $args['thumbnail_alt'] : $args['title'];
		$show_import_demo_btn = $args['card_type'] === 'card-style-grid-template';
		?>

		<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>"> 

			<?php if ( ! empty( $args['thumbnail_url'] ) ) : ?>
				<div class="muia-card-thumbnail">
					<?php if ( ! empty( $args['url'] ) ) : ?>
						<a href="<?php echo esc_url( $args['url'] ); ?>" aria-label="<?php echo esc_attr( $thumbnail_alt ); ?>">
							<img src="<?php echo esc_url( $args['thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $thumbnail_alt ); ?>">
						</a>
					<?php else : ?>
						<img src="<?php echo esc_url( $args['thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $thumbnail_alt ); ?>">
					<?php endif; ?>
				</div>
			<?php endif; ?>
                
			<?php if ( ! empty( $args['subtitle'] ) ) : ?>
				<div class="muia-card-subtitle">
					<?php echo esc_html( $args['subtitle'] ); ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $args['title'] ) ) : ?>

				<h3 class="muia-card-title">

					<?php if ( ! empty( $args['url'] ) ) : ?>
						<a href="<?php echo esc_url( $args['url'] ); ?>">
							<?php echo esc_html( $args['title'] ); ?>
						</a>
					<?php else : ?>
						<span><?php echo esc_html( $args['title'] ); ?></span>
					<?php endif; 
						if($show_import_demo_btn){ 
							$this->muia_get_import_demo_btn();  
						 } ?>
				</h3>

			<?php endif; ?>

			<?php if ( ! empty( $args['excerpt'] ) ) : ?>
				<div class="muia-card-excerpt">
					<?php echo wp_kses_post( $args['excerpt'] ); ?>
				</div>
			<?php endif; ?>  

            <?php if ( ! empty( $args['terms'] ) && is_array( $args['terms'] ) ) : ?>

                <div class="muia-card-terms">

                    <?php foreach ( $args['terms'] as $term ) : ?>

                        <?php
                        $term_name = '';
                        $term_slug = '';
                        $term_link = '';

                        // WP_Term object.
                        if ( is_object( $term ) && isset( $term->term_id ) ) {

                            $term_name = $term->name;
                            $term_slug = $term->slug;
                            $term_link = get_term_link( $term );

                        // Term ID.
                        } elseif ( is_numeric( $term ) ) {

                            $term_obj = get_term( (int) $term );

                            if ( ! is_wp_error( $term_obj ) && $term_obj ) {
                                $term_name = $term_obj->name;
                                $term_slug = $term_obj->slug;
                                $term_link = get_term_link( $term_obj );
                            } else {
                                continue;
                            }

                        // String fallback.
                        } elseif ( is_string( $term ) ) {

                            $term_name = $term;
                            $term_slug = sanitize_key( $term );
                            $term_link = '';
                        } else {
                            continue;
                        }

                        // Safety check for term link.
                        if ( is_wp_error( $term_link ) ) {
                            $term_link = '';
                        }
                        ?>

                        <span class="muia-card-term muia-cat-<?php echo esc_attr( $term_slug ); ?>">

                            <?php if ( ! empty( $term_link ) ) : ?>
                                <a href="<?php echo esc_url( $term_link ); ?>">
                                    <?php echo esc_html( $term_name ); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html( $term_name ); ?>
                            <?php endif; ?>

                        </span>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

			<?php if ( ! empty( $args['show_arrow'] ) ) : ?>

				<div class="muia-card-arrow" aria-hidden="true">
					&rarr;
				</div>

			<?php endif; ?>

		</div>

		<?php
	}
	public function muia_get_import_demo_btn($args = array()){
		$args = wp_parse_args(
			$args,
			array(
				'import_title'     => esc_html__('Import', 'motionui-addons-for-elementor'),
				'import_url'     => muia_get_acf_url('import_url'),
				'demo_title'     => esc_html__('Live demo', 'motionui-addons-for-elementor'),
				'demo_url'     => get_the_permalink(),
			)
		);
		echo '<div class="btns_list_wrap">';
		
		$this->_render_muia_btn('btns_list', array(
			'title' => $args['import_title'],
			'url' => $args['import_url'],
		));

		$this->_render_muia_btn('btns_list', array(
			'title' => $args['demo_title'],
			'url' => $args['demo_url'],
		));

		echo '</div>';
	}
}
