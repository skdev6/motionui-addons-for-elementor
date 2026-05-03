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