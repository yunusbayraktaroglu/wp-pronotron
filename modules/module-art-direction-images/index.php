<?php

namespace WPPronotron\Module;

use WPPronotron\Module;
use WPPronotron\Utils\ImageUtils;
use WPPronotron\Utils\DevelopmentUtils;

/**
 * Module Art Direction Images
 * 
 * - Define ratio based image sizes via admin UI
 * - Customize default image editor to crop images in defined ratios
 * - Customize "core/image" gutenberg block to display <picture><source> tagged images
 * 
 * @package WPPronotron\Module
 */

class ArtDirectionImagesModule extends Module {

    public function module(): array {
		return [
			'name' 			=> __( 'Art Direction Images', 'pronotron' ),
			'description' 	=> __( 'Define custom images sizes with orientations.', 'pronotron' ),
		];
    }

	public function init_module(): void {

		/**
		 * Instruct WP to delete existing images when a crop is made.
		 */
		define( 'IMAGE_EDIT_OVERWRITE', true );

		$this->add_submodule( 'control-image-sizes' );
		$this->add_submodule( 'core-image-block-customize' );
		$this->add_submodule( 'core-image-editor-customize' );

        $this->extend_rest_api();

		//$this->debug();
	}

    /**
     * Rest api extensions of module
     * @return void
     */
    protected function extend_rest_api(): void {

        add_action( 'graphql_register_types', function(){

            register_graphql_field( 'MediaItem', 'artDirectioned', [
                'type' => 'String',
                'description' => __( 'Art directioned media in <source> tag', 'pronotron' ),
                'resolve' => function( $post ){
                    return ImageUtils::get_image_picture_tag( $post->ID, true, true );
                }
            ] );
                
        });
    }

	protected function debug(){
		
		$test_image = 197;
		
		/** Control picture tag */
		//$art_direction_image = ImageUtils::get_image_picture_tag( $test_image );

		/** Returns image all metadata */
		$meta = wp_get_attachment_metadata( $test_image );

		/** When any image edit done, wp creates backup-sizes to keep unedited images */
		// $backup_sizes = get_post_meta( $test_image, '_wp_attachment_backup_sizes', true );

        $srcset = wp_get_attachment_image_srcset( $test_image, 'landscape_5_3_full' );

		//DevelopmentUtils::console_log( $srcset );

		//DevelopmentUtils::console_log( $meta );
		//DevelopmentUtils::console_log( $art_direction_image );
		//DevelopmentUtils::console_log( $backup_sizes );

		/** Control our registered images */
		//DevelopmentUtils::console_log( get_option( "wp_pronotron_options" ) );
		//DevelopmentUtils::console_log( get_option( "wp_pronotron_registered_images" ) );

		/** Control if our images registered successfuly  */
		//DevelopmentUtils::console_log( get_intermediate_image_sizes() );
		//DevelopmentUtils::console_log( wp_get_registered_image_subsizes() );
		//DevelopmentUtils::console_log( wp_get_additional_image_sizes() );
	}

}

return new ArtDirectionImagesModule();