<?php

use WPPronotron\Utils\DevelopmentUtils;

/**
 * Overwrite default WordPress image-edit.php
 * 

 * 1# Print radio buttons
 * 
 * <?php wp_pronotron_print_ratio_buttons( $post_id, $nonce ); ?> (288)
 * 
 * 
 * 2# Correct wp_save_image() function, we pass $sizes as json array (1027)
 * 
 * $sizes = json_decode( wp_unslash( $_REQUEST['target'] ) );
 * $nocrop  = true;
 * 
 * 
 * 3# Delete edited files if only in $sizes array (1044)
 * 
 *	if ( in_array( $key, $sizes ) ){ <- THIS PART
 *		if ( ! empty( $size['file'] ) && preg_match( '/-e[0-9]{13}-/', $size['file'] ) ) {
 *			$delete_file = path_join( $dirname, $size['file'] );
 *			wp_delete_file( $delete_file );
 *		}
 *	}
 * 
 */

function wp_pronotron_print_ratio_buttons( $post_id, $nonce ){

	$registered_images = get_option( 'wp_pronotron_registered_images' );

	if ( ! $registered_images ) return null;

	foreach ( $registered_images as $image => $image_data ){

		$label = $image_data[ 'label' ];
		$ratio = $image_data[ 'ratio' ];
		$value = esc_html( json_encode( $image_data[ 'registered' ] ) );

		?>
		<span class="imgedit-label" onclick="imageEdit.ySetRatioSelection(<?= "$post_id, '$nonce', $ratio[0], $ratio[1]"; ?>);">
			<input type="radio" value="<?= $value; ?>" id="imgedit-target-<?= $label ?>" name="imgedit-target-<?= $post_id; ?>" />
			<label for="imgedit-target-<?= $label ?>"><?php _e( $label ); ?></label>
		</span>
		<?php
	}
}
