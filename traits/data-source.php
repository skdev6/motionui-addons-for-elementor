<?php
/**
 * Data Source Trait
 *
 * @package     CsfCore
 * @subpackage  Traits
 * @since       1.0.0
 * @license     GPL-2.0-or-later
 */

namespace Themeic\MotionUI_Addons\Traits;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trait Data_Source
 *
 * Gives a widget three interchangeable ways to fill a list of items: a manual
 * Elementor repeater, an ACF repeater field, or a post type query. Every source
 * is normalised into the same array shape, so a widget's render code has one
 * path no matter where the content came from.
 *
 * A widget declares the fields it needs; the trait builds the matching controls
 * and readers. Each entry in `fields` is:
 *
 *     'card_title' => array(          // key the item is returned under
 *         'id'    => 'title',         // used in control IDs, keep stable
 *         'label' => 'Title',
 *         'type'  => 'text',          // text | image | link
 *         'acf'   => 'title',         // default ACF sub field name
 *         'post'  => 'title',         // title | excerpt | thumbnail | permalink | meta | none
 *     ),
 *
 * `post => 'meta'` adds a meta-key control so a field with no post equivalent
 * (a role, a phone number) can still be filled from a query.
 *
 * `id` need not match the array key, and often should not: it is spliced into
 * the control IDs `acf_sub_<id>` and `post_meta_<id>`, so it has to be unique
 * across the widget's fields and must not collide with a control this trait
 * already registers. Elementor refuses a duplicate control and logs a notice.
 *
 * Usage:
 *
 *     use Data_Source;
 *
 *     $this->muia_source_controls( array( 'fields' => $this->get_source_fields() ) );
 *     // …
 *     $items = $this->muia_get_source_items( $settings, $this->get_source_fields() );
 *
 * The repeater itself stays with the widget, since only the widget knows what
 * its rows look like; name it through `repeater_control`.
 *
 * @since 1.0.0
 */
trait Data_Source {

	/**
	 * Source picker plus the controls for the ACF and post type sources.
	 *
	 * Call this inside an open controls section, after the widget has added its
	 * own repeater control.
	 *
	 * @param array $args {
	 *     @type array  $fields            Field map, see the trait docblock.
	 *     @type string $repeater_control  Repeater control ID. Default 'slides'.
	 *     @type bool   $excerpt_words     Offer an excerpt length control.
	 *     @type string $post_type_default Default post type.
	 * }
	 * @return void
	 */
	public function muia_source_controls( $args = array() ) {

		$args = wp_parse_args(
			$args,
			array(
				'fields'            => array(),
				'repeater_control'  => 'slides',
				'excerpt_words'     => true,
				'post_type_default' => 'post',
			)
		);

		$fields = $args['fields'];

		/* -----------------------------------------------------------------
		 * ACF repeater
		 * -------------------------------------------------------------- */

		if ( ! function_exists( 'get_field' ) ) {
			$this->add_control(
				'acf_missing_notice',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'Advanced Custom Fields is not active, so this source will render nothing.', 'motionui-addons-for-elementor' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition'       => array( 'source' => 'acf' ),
				)
			);
		}

		$this->add_control(
			'acf_field',
			array(
				'label'       => esc_html__( 'Repeater Field Name', 'motionui-addons-for-elementor' ),
				'description' => esc_html__( 'The ACF field name, not its label.', 'motionui-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'slides',
				'label_block' => true,
				'condition'   => array( 'source' => 'acf' ),
			)
		);

		$this->add_control(
			'acf_from',
			array(
				'label'     => esc_html__( 'Read From', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'current',
				'options'   => array(
					'current' => esc_html__( 'Current Post', 'motionui-addons-for-elementor' ),
					'option'  => esc_html__( 'Options Page', 'motionui-addons-for-elementor' ),
					'custom'  => esc_html__( 'Specific Post ID', 'motionui-addons-for-elementor' ),
				),
				'condition' => array( 'source' => 'acf' ),
			)
		);

		$this->add_control(
			'acf_post_id',
			array(
				'label'     => esc_html__( 'Post ID', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'condition' => array(
					'source'   => 'acf',
					'acf_from' => 'custom',
				),
			)
		);

		// Named outside the `acf_sub_*` namespace below: a widget with a field
		// whose `id` is `heading` would otherwise collide with this control.
		$this->add_control(
			'acf_sub_fields_title',
			array(
				'label'     => esc_html__( 'Sub Field Names', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'source' => 'acf' ),
			)
		);

		foreach ( $fields as $field ) {
			$this->add_control(
				'acf_sub_' . $field['id'],
				array(
					'label'       => $field['label'],
					'type'        => Controls_Manager::TEXT,
					'default'     => isset( $field['acf'] ) ? $field['acf'] : $field['id'],
					'placeholder' => isset( $field['acf'] ) ? $field['acf'] : $field['id'],
					'description' => esc_html__( 'Leave empty to skip.', 'motionui-addons-for-elementor' ),
					'label_block' => true,
					'condition'   => array( 'source' => 'acf' ),
				)
			);
		}

		/* -----------------------------------------------------------------
		 * Post type query
		 * -------------------------------------------------------------- */

		$this->add_control(
			'post_type',
			array(
				'label'     => esc_html__( 'Post Type', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => $args['post_type_default'],
				'options'   => $this->muia_get_post_type_options(),
				'condition' => array( 'source' => 'post' ),
			)
		);

		$this->add_control(
			'post_limit',
			array(
				'label'     => esc_html__( 'Number of Posts', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 50,
				'default'   => 6,
				'condition' => array( 'source' => 'post' ),
			)
		);

		$this->add_control(
			'post_taxonomy',
			array(
				'label'     => esc_html__( 'Filter by Taxonomy', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array_merge(
					array( '' => esc_html__( 'None', 'motionui-addons-for-elementor' ) ),
					$this->muia_get_taxonomy_options()
				),
				'condition' => array( 'source' => 'post' ),
			)
		);

		$this->add_control(
			'post_terms',
			array(
				'label'       => esc_html__( 'Term Slugs', 'motionui-addons-for-elementor' ),
				'description' => esc_html__( 'Comma separated. Leave empty for all terms.', 'motionui-addons-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'source'         => 'post',
					'post_taxonomy!' => '',
				),
			)
		);

		$this->add_control(
			'post_orderby',
			array(
				'label'     => esc_html__( 'Order By', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'Date', 'motionui-addons-for-elementor' ),
					'title'      => esc_html__( 'Title', 'motionui-addons-for-elementor' ),
					'menu_order' => esc_html__( 'Menu Order', 'motionui-addons-for-elementor' ),
					'rand'       => esc_html__( 'Random', 'motionui-addons-for-elementor' ),
				),
				'condition' => array( 'source' => 'post' ),
			)
		);

		$this->add_control(
			'post_order',
			array(
				'label'     => esc_html__( 'Order', 'motionui-addons-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => esc_html__( 'Descending', 'motionui-addons-for-elementor' ),
					'ASC'  => esc_html__( 'Ascending', 'motionui-addons-for-elementor' ),
				),
				'condition' => array( 'source' => 'post' ),
			)
		);

		// Meta key inputs for fields a post has no native equivalent for.
		$meta_fields = array_filter(
			$fields,
			function ( $field ) {
				return isset( $field['post'] ) && 'meta' === $field['post'];
			}
		);

		if ( $meta_fields ) {
			// Outside the `post_meta_*` namespace, for the same reason as the
			// ACF heading above.
			$this->add_control(
				'post_meta_keys_title',
				array(
					'label'     => esc_html__( 'Meta Keys', 'motionui-addons-for-elementor' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'source' => 'post' ),
				)
			);

			foreach ( $meta_fields as $field ) {
				$this->add_control(
					'post_meta_' . $field['id'],
					array(
						'label'       => $field['label'],
						'type'        => Controls_Manager::TEXT,
						'placeholder' => $field['id'],
						'description' => esc_html__( 'Custom field key. Leave empty to skip.', 'motionui-addons-for-elementor' ),
						'label_block' => true,
						'condition'   => array( 'source' => 'post' ),
					)
				);
			}
		}

		if ( $args['excerpt_words'] ) {
			$this->add_control(
				'post_excerpt_words',
				array(
					'label'       => esc_html__( 'Excerpt Length', 'motionui-addons-for-elementor' ),
					'description' => esc_html__( 'Words. 0 keeps the full excerpt.', 'motionui-addons-for-elementor' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 0,
					'max'         => 100,
					'default'     => 18,
					'separator'   => 'before',
					'condition'   => array( 'source' => 'post' ),
				)
			);
		}
	}

	/**
	 * The source picker itself.
	 *
	 * Kept separate so a widget can put it above its repeater control.
	 *
	 * @return void
	 */
	public function muia_source_picker() {

		$this->add_control(
			'source',
			array(
				'label'       => esc_html__( 'Source', 'motionui-addons-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'repeater',
				'options'     => array(
					'repeater' => esc_html__( 'Manual (Repeater)', 'motionui-addons-for-elementor' ),
					'acf'      => esc_html__( 'ACF Repeater', 'motionui-addons-for-elementor' ),
					'post'     => esc_html__( 'Post Type', 'motionui-addons-for-elementor' ),
				),
				'render_type' => 'template',
			)
		);
	}

	/* ---------------------------------------------------------------------
	 * Readers
	 * ------------------------------------------------------------------ */

	/**
	 * Items for the selected source.
	 *
	 * @param array $settings Widget settings.
	 * @param array $fields   Field map, see the trait docblock.
	 * @param array $args     Same args passed to muia_source_controls().
	 * @return array<int, array>
	 */
	public function muia_get_source_items( $settings, $fields, $args = array() ) {

		$args = wp_parse_args(
			$args,
			array( 'repeater_control' => 'slides' )
		);

		$source = ! empty( $settings['source'] ) ? $settings['source'] : 'repeater';

		switch ( $source ) {
			case 'acf':
				return $this->muia_items_from_acf( $settings, $fields );

			case 'post':
				return $this->muia_items_from_posts( $settings, $fields );

			default:
				return ! empty( $settings[ $args['repeater_control'] ] )
					? $settings[ $args['repeater_control'] ]
					: array();
		}
	}

	/**
	 * Items from an ACF repeater field.
	 *
	 * @param array $settings Widget settings.
	 * @param array $fields   Field map.
	 * @return array<int, array>
	 */
	protected function muia_items_from_acf( $settings, $fields ) {

		if ( ! function_exists( 'get_field' ) || empty( $settings['acf_field'] ) ) {
			return array();
		}

		switch ( ! empty( $settings['acf_from'] ) ? $settings['acf_from'] : 'current' ) {
			case 'option':
				$post_id = 'option';
				break;

			case 'custom':
				$post_id = ! empty( $settings['acf_post_id'] ) ? (int) $settings['acf_post_id'] : 0;
				break;

			default:
				$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return array();
		}

		$rows = get_field( $settings['acf_field'], $post_id );

		if ( ! is_array( $rows ) ) {
			return array();
		}

		$items = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$item = array( '_id' => '' );

			foreach ( $fields as $key => $field ) {
				$sub_key = ! empty( $settings[ 'acf_sub_' . $field['id'] ] )
					? $settings[ 'acf_sub_' . $field['id'] ]
					: '';

				if ( '' === $sub_key || ! isset( $row[ $sub_key ] ) ) {
					$item[ $key ] = $this->muia_empty_value( $field );
					continue;
				}

				$item[ $key ] = $this->muia_cast_value( $row[ $sub_key ], $field );
			}

			$items[] = $item;
		}

		return $items;
	}

	/**
	 * Items from a post type query.
	 *
	 * @param array $settings Widget settings.
	 * @param array $fields   Field map.
	 * @return array<int, array>
	 */
	protected function muia_items_from_posts( $settings, $fields ) {

		$query_args = array(
			'post_type'           => ! empty( $settings['post_type'] ) ? $settings['post_type'] : 'post',
			'posts_per_page'      => ! empty( $settings['post_limit'] ) ? (int) $settings['post_limit'] : 6,
			'orderby'             => ! empty( $settings['post_orderby'] ) ? $settings['post_orderby'] : 'date',
			'order'               => ! empty( $settings['post_order'] ) ? $settings['post_order'] : 'DESC',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( ! empty( $settings['post_taxonomy'] ) && ! empty( $settings['post_terms'] ) ) {
			$terms = array_filter( array_map( 'trim', explode( ',', $settings['post_terms'] ) ) );

			if ( $terms ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => $settings['post_taxonomy'],
						'field'    => 'slug',
						'terms'    => $terms,
					),
				);
			}
		}

		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return array();
		}

		$excerpt_words = isset( $settings['post_excerpt_words'] ) ? (int) $settings['post_excerpt_words'] : 0;
		$items         = array();

		foreach ( $query->posts as $post ) {

			$item = array( '_id' => '' );

			foreach ( $fields as $key => $field ) {
				$item[ $key ] = $this->muia_post_field_value( $post, $field, $settings, $excerpt_words );
			}

			$items[] = $item;
		}

		wp_reset_postdata();

		return $items;
	}

	/**
	 * Resolve one field for one post.
	 *
	 * @param \WP_Post $post          Post object.
	 * @param array    $field         Field spec.
	 * @param array    $settings      Widget settings.
	 * @param int      $excerpt_words Excerpt word limit, 0 for no limit.
	 * @return mixed
	 */
	protected function muia_post_field_value( $post, $field, $settings, $excerpt_words ) {

		$from = isset( $field['post'] ) ? $field['post'] : 'none';

		switch ( $from ) {
			case 'title':
				return get_the_title( $post );

			case 'excerpt':
				$excerpt = wp_strip_all_tags( get_the_excerpt( $post ) );

				return $excerpt_words > 0 ? wp_trim_words( $excerpt, $excerpt_words ) : $excerpt;

			case 'thumbnail':
				$thumbnail_id = get_post_thumbnail_id( $post );

				return $thumbnail_id
					? array(
						'id'  => $thumbnail_id,
						'url' => wp_get_attachment_image_url( $thumbnail_id, 'large' ),
					)
					: array();

			case 'permalink':
				return array( 'url' => get_permalink( $post ) );

			case 'meta':
				$meta_key = ! empty( $settings[ 'post_meta_' . $field['id'] ] )
					? $settings[ 'post_meta_' . $field['id'] ]
					: '';

				if ( '' === $meta_key ) {
					return $this->muia_empty_value( $field );
				}

				return $this->muia_cast_value( get_post_meta( $post->ID, $meta_key, true ), $field );

			default:
				return $this->muia_empty_value( $field );
		}
	}

	/* ---------------------------------------------------------------------
	 * Normalisers
	 * ------------------------------------------------------------------ */

	/**
	 * Coerce a raw value into the shape the field's type implies.
	 *
	 * @param mixed $value Raw value.
	 * @param array $field Field spec.
	 * @return mixed
	 */
	protected function muia_cast_value( $value, $field ) {

		switch ( isset( $field['type'] ) ? $field['type'] : 'text' ) {
			case 'image':
				return $this->muia_normalize_image( $value );

			case 'link':
				return $this->muia_normalize_link( $value );

			default:
				return is_scalar( $value ) ? wp_strip_all_tags( (string) $value ) : '';
		}
	}

	/**
	 * The empty value for a field type.
	 *
	 * @param array $field Field spec.
	 * @return mixed
	 */
	protected function muia_empty_value( $field ) {

		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		return in_array( $type, array( 'image', 'link' ), true ) ? array() : '';
	}

	/**
	 * Coerce an image value into `array( 'id' => …, 'url' => … )`.
	 *
	 * ACF returns an array, an attachment ID or a URL depending on the field's
	 * return format, so all three are handled rather than assuming one.
	 *
	 * @param mixed $value Raw image value.
	 * @return array
	 */
	protected function muia_normalize_image( $value ) {

		if ( is_array( $value ) ) {
			return array(
				'id'  => isset( $value['ID'] ) ? (int) $value['ID'] : ( isset( $value['id'] ) ? (int) $value['id'] : '' ),
				'url' => isset( $value['url'] ) ? $value['url'] : '',
			);
		}

		if ( is_numeric( $value ) ) {
			return array(
				'id'  => (int) $value,
				'url' => wp_get_attachment_image_url( (int) $value, 'large' ),
			);
		}

		if ( is_string( $value ) && '' !== $value ) {
			return array(
				'id'  => '',
				'url' => $value,
			);
		}

		return array();
	}

	/**
	 * Coerce a link value into Elementor's URL control shape.
	 *
	 * @param mixed $value Raw link value.
	 * @return array
	 */
	protected function muia_normalize_link( $value ) {

		if ( is_array( $value ) ) {
			return array(
				'url'         => isset( $value['url'] ) ? $value['url'] : '',
				'is_external' => ( isset( $value['target'] ) && '_blank' === $value['target'] ) ? 'on' : '',
				'nofollow'    => '',
			);
		}

		if ( is_string( $value ) && '' !== $value ) {
			return array(
				'url'         => $value,
				'is_external' => '',
				'nofollow'    => '',
			);
		}

		return array();
	}

	/* ---------------------------------------------------------------------
	 * Option lists
	 * ------------------------------------------------------------------ */

	/**
	 * Selectable post types, minus the builder-internal ones.
	 *
	 * @return array<string, string>
	 */
	protected function muia_get_post_type_options() {

		$excluded = array( 'attachment', 'elementor_library', 'e-floating-buttons', 'ha_library' );
		$options  = array();

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			if ( in_array( $post_type->name, $excluded, true ) ) {
				continue;
			}

			$options[ $post_type->name ] = $post_type->label;
		}

		return $options;
	}

	/**
	 * Selectable taxonomies.
	 *
	 * @return array<string, string>
	 */
	protected function muia_get_taxonomy_options() {

		$options = array();

		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
			if ( 'post_format' === $taxonomy->name ) {
				continue;
			}

			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}
}
