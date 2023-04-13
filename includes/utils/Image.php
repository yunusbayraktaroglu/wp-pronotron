<?php

namespace WPPronotron\Utils;

use WPPronotron\Utils\DevelopmentUtils;

/**
 * Class ImageUtils
 * 
 * Helper functions for images
 * @package WPPronotron\Utils
 * 
 */
class ImageUtils {

    /**
     * Create tagged fluid images with image attachment id
	 * 
	 * <picture>
	 * 		<source>...</source>
	 * 		<source>...</source>
	 * 		<img>...</img>
	 * </picture> 
     * 
     * @param int $image_id - Attachment id
	 * @param bool $headless - Is request comes from rest api
     * @return string - tagged image
	 * 
     */
    public static function get_image_picture_tag( int $image_id, bool $headless = false, bool $create_webp = true ){
        
		$registered_images = get_option( 'wp_pronotron_registered_images' );

		$webp_sources = '';
		$fallback_sources = '';

		/**
		 * Create webp & fallback sources (png, jpg, etc )
		 */
		foreach( $registered_images as $image ){
			
			/** Fetch only full size, subsizes are comes with full size */
			$ratio_name_full 	= $image[ 'registered' ][ 0 ];
			$data 				= ImageUtils::get_srcset_and_sizes( $image_id, $ratio_name_full );

			$srcset = $data[ 'srcset' ];
			$sizes 	= $data[ 'sizes' ];
			$media 	= $image[ 'media' ];

			if ( $create_webp ){
				 $webp_source = sprintf(
					'<source srcset="%1$s" sizes="%2$s" media="%3$s" %4$s>',
					esc_attr( $data[ 'srcset' ] ),
					esc_attr( $data[ 'sizes' ] ),
					esc_attr( $media ),
					'type="image/webp"'
				);

				$webp_sources .= preg_replace( '/.(jpg|jpeg|png|gif)/i', '${0}.webp', $webp_source );
			}

			$fallback_sources .= sprintf(
				'<source srcset="%1$s" sizes="%2$s" media="%3$s">',
				esc_attr( $data[ 'srcset' ] ),
				esc_attr( $data[ 'sizes' ] ),
				esc_attr( $media )
			);
		}

		/**
		 * Default image
		 */
        $img_src    	= wp_get_attachment_image_url( $image_id, 'full' );
        $image_alt  	= get_post_meta( $image_id, '_wp_attachment_image_alt', true );

		$default_image 	= sprintf(
			'<img loading="lazy" decoding="async" src="%1$s" alt="%2$s">',
			esc_attr( $img_src ),
			esc_attr( $image_alt ),
		);

		$picture = sprintf(
			'<picture class="art-direction-image">%1$s</picture>',
			$webp_sources . $fallback_sources . $default_image,
		);

		/** 
		 * If image not in post content, no need to wrap <figure> 
		 * Wrapping is mandatory for gallery images etc. 
		 */
		if ( $headless ){
			return $picture;
		} else {
			return '<figure class="wp-block-image">' . $picture . '</figure>';
		}

    }

    /**
     * Get image srcset and sizes
     * 
     * @param int $image_id - Attachment id
     * @param string $size - Defined image size (thumbnail, full_screen, etc..)
     * @return array - ['srcset' => 'string', 'sizes' => 'string']
     */
    public static function get_srcset_and_sizes( int $image_id, string $image_size ){

        $srcset = wp_get_attachment_image_srcset( $image_id, $image_size );
        $sizes = wp_get_attachment_image_sizes( $image_id, $image_size);

        return array( 'srcset' => $srcset, 'sizes' => $sizes );

    }

    /**
     * Get aspect ratio like "19:6", with width and height
     * 
     * @param int $width
     * @param int $height 
     * @return string
     */
    public static function get_aspect_ratio( int $width, int $height ){
        
        $greatestCommonDivisor = static function( $width, $height ) use ( &$greatestCommonDivisor ) {
            return ( $width % $height ) ? $greatestCommonDivisor( $height, $width % $height ) : $height;
        };
    
        $divisor = $greatestCommonDivisor( $width, $height );
    
        return $width / $divisor . ':' . $height / $divisor;

    }
    
}