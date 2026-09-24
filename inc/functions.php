<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function muia_sanitize_array_recursively($array) {

	foreach ($array as $key => &$value) {
		if (is_array($value)) {
			$value = muia_sanitize_array_recursively($value);
		} else {
			$value = sanitize_text_field($value);
		}
	}

	return $array;
}
if ( ! function_exists( 'muia_has_pro' ) ) {
	/**
	 * Are the Pro features unlocked?
	 *
	 * Pro installed but unlicensed counts as no Pro, so the controls stay
	 * locked. See Motionui::is_active_pro().
	 */
	function muia_has_pro(){
		return \Themeic\MotionUI_Addons\Inc\Classes\Motionui::is_active_pro();
	}
}
if ( ! function_exists( 'muia_get_pronotice_html' ) ) {
	function muia_get_pronotice_html( $is_thumb = true ) {
		$img_src     = esc_url( THEMEIC_MUIA_ASSETS . 'img/get-pro.svg' );
		$upgrade_url = esc_url( 'https://motionuiaddons.com/' );

		$img_html = $is_thumb ? sprintf(
			'<img src="%s" alt="%s" />',
			$img_src,
			esc_attr( __( 'Upgrade Notice', 'motionui-addons-for-elementor' ) )
		) : '';

		return sprintf(
			'<div class="muia-pro-notice %s">
				%s
				<div class="muia-pro-notice-content">
					<h4>%s</h4>
					<p>%s</p>
					<a target="__blank" rel="nofollow" class="elementor-button elementor-button-default" href="%s">%s</a>
				</div>
			</div>',
			$is_thumb ? 'has-thumb' : 'no-thumb',
			$img_html,
			__( 'Upgrade to premium and unlock every feature!', 'motionui-addons-for-elementor' ),
			__( 'Upgrade and get access to every feature.', 'motionui-addons-for-elementor' ),
			$upgrade_url,
			__( 'Upgrade MotionUI Addons', 'motionui-addons-for-elementor' )
		);
	}
}
if ( ! function_exists( 'muia_get_acf_url' ) ) {
    function muia_get_acf_url( string $field_name, ?int $post_id = null ): string {
        if ( ! function_exists( 'get_field' ) ) {
            return '';
        }

        // Resolve post ID at runtime — default parameter values cannot be
        // function calls in PHP, so we handle the fallback here instead.
        $post_id     = $post_id ?? get_the_ID();
        $field_value = get_field( $field_name, $post_id );

        if ( empty( $field_value ) ) {
            return '';
        }

        // ACF link field returns an array with a 'url' key.
        if ( is_array( $field_value ) && ! empty( $field_value['url'] ) ) {
            return esc_url( $field_value['url'] );
        }

        // Plain text / URL field returns a string.
        if ( is_string( $field_value ) ) {
            return esc_url( $field_value );
        }

        return '';
    }
}
if ( ! function_exists( 'muia_get_acf_data' ) ) {
    function muia_get_acf_data( string $field_name, ?int $post_id = null ): mixed {
        if ( ! function_exists( 'get_field' ) ) {
            return null;
        }

        $post_id = $post_id ?? get_the_ID();
        return get_field( $field_name, $post_id );
    }
}
if ( ! function_exists( 'muia_get_dynamic_meta' ) ) {   
	function muia_get_dynamic_meta( string $field_name, ?int $post_id = null ): string {

		$post_id = $post_id ?? get_the_ID();

		if ( empty( $field_name ) || empty( $post_id ) ) {
			return '';
		}
		$field_value = '';

		if ( function_exists( 'get_field' ) ) {
			$field_value = get_field( $field_name, $post_id );
		}
		if ( empty( $field_value ) ) {
			$field_value = get_post_meta( $post_id, $field_name, true );
		}
		if ( empty( $field_value ) ) {
			return '';
		}
		if ( is_array( $field_value ) ) {

			if ( ! empty( $field_value['url'] ) ) {
				return esc_url( $field_value['url'] );
			}

			// Optional fallback keys.
			if ( ! empty( $field_value['link'] ) ) {
				return esc_url( $field_value['link'] );
			}

			if ( ! empty( $field_value['value'] ) ) {
				return esc_url( $field_value['value'] );
			}

			return $field_value;
		}
		if ( is_string( $field_value ) ) {
			return wp_kses_post( $field_value );
		}

		return '';
	}
}
/* -------------------------------------------------------------------------
 * Marketing links
 *
 * The widget and extension catalogs point at the product site for demos and
 * tutorials. Composing those URLs here keeps the domain in one place, and lets
 * a row say only what is specific to it — the path.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'muia_brand_url' ) ) {
	/**
	 * A URL on the product site.
	 *
	 * @param string $path Path with a leading slash. Normalised either way.
	 * @return string
	 */
	function muia_brand_url( $path = '' ) {
		$path = (string) $path;

		return 'https://motionuiaddons.com' . ( '' === $path ? '' : '/' . ltrim( $path, '/' ) );
	}
}

if ( ! function_exists( 'muia_is_absolute_url' ) ) {
	/**
	 * Has this already got a host on the front?
	 *
	 * Covers `https://…`, any other scheme, and protocol-relative `//…`.
	 *
	 * @param string $url URL or path.
	 * @return bool
	 */
	function muia_is_absolute_url( $url ) {
		return (bool) preg_match( '#^([a-z][a-z0-9+.\-]*:)?//#i', (string) $url );
	}
}

if ( ! function_exists( 'get_muia_demo_url' ) ) {
	/**
	 * Demo URL for a catalog entry.
	 *
	 * Demos live on the product site behind a query argument, so the normal
	 * thing to pass is the path. A full URL is returned untouched, for the odd
	 * demo hosted somewhere else.
	 *
	 * @param string $path Demo path, e.g. '/element/button/'.
	 * @return string Absolute URL, or '' when there is no demo. The dashboard
	 *                hides the button on an empty string, so that is the way to
	 *                say "no demo" rather than linking to a dead page.
	 */
	function get_muia_demo_url( $path = '' ) {
		$path = trim( (string) $path );

		if ( '' === $path ) {
			return '';
		}

		if ( muia_is_absolute_url( $path ) ) {
			return $path;
		}

		// The site takes the demo path as a query argument, not as a real path.
		return muia_brand_url( '/' ) . '?demoUrl=/' . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'get_muia_tuto_url' ) ) {
	/**
	 * Tutorial URL for a catalog entry.
	 *
	 * Tutorials live wherever they live — a YouTube video, a docs site — so a
	 * full URL is the normal thing to pass and it comes back untouched. A bare
	 * path is still resolved against the product site, so both forms work.
	 *
	 * An entry with no tutorial of its own falls back to whatever
	 * `muia_tutorial_default_url` supplies. When that is empty too the result
	 * is '', and the dashboard hides the link rather than offering a dead one.
	 *
	 * @param string $url Full URL, e.g. 'https://www.youtube.com/watch?v=…',
	 *                    or a path on the product site. '' to use the default.
	 * @return string
	 */
	function get_muia_tuto_url( $url = '' ) {

		$url = trim( (string) $url );

		if ( '' === $url ) {
			/**
			 * Where an entry with no tutorial of its own points.
			 *
			 * Empty is the shipped default and means "no link". Set it to a
			 * URL — a channel or a tutorials index — to give every such entry
			 * one destination, from here or from the Pro plugin.
			 *
			 * @param string $default_url Full URL or path, '' for no link.
			 */
			$url = trim( (string) apply_filters( 'muia_tutorial_default_url', '' ) );
		}

		if ( '' === $url ) {
			return '';
		}

		return muia_is_absolute_url( $url ) ? $url : muia_brand_url( $url );
	}
}
