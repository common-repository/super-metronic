<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* 
 * Helpers
 */
function sm_image_uploader_field( $name, $value = '', $w = 115, $h = 90) {
	$default = super_metronic()->plugin_url() . '/assets/img/default.jpg';
	if( $value ) {
		$image_attributes = wp_get_attachment_image_src( $value, array($w, $h) );
		$src = $image_attributes[0];
	} else {
		$src = $default;
	}
	return '<div>
		<img data-src="' . $default . '" src="' . $src . '" width="' . $w . 'px" height="' . $h . 'px" />
		<div>
			<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
			<button type="submit" class="upload_image_button button">'.__('Upload', Super_Metronic::TEXT_DOMAIN) .'</button>
			<button type="submit" class="remove_image_button button">&times;</button>
		</div>
	</div>';
}