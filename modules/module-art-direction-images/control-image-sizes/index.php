<?php

namespace WPPronotron\Module\ArtDirectionImagesModule;

use WPPronotron\Utils\DevelopmentUtils;
use WPPronotron\Utils\ImageUtils;

/**
 * Submodule ControlImageSizes
 * 
 * @package WPPronotron\Module\ArtDirectionImagesModule
 */
class ControlImageSizes {

	public string $submodule_id;
	public string $submodule_dir;
	public string $submodule_url;
	public array $user_options = array();

	/**
	 * Admin page default options
	 * @return array
	 */
	public function submodule_options(){
		return [
			'id'    	=> 'control_image_sizes',
			'name'		=> __( 'Control image sizes', 'pronotron' ),
			'desc'    	=> __( 'Control image sizes to create clean image pipelines.', 'pronotron' ),
			'class'		=> 'control-image-sizes',
			'default' => [
				'upload_sizes' => [
					'width' 	=> 2000,
					'height' 	=> 1200
				],
				'landscape_ratios' => [
					[ 'x' => 5, 'y' => 3 ],
					[ 'x' => 5, 'y' => 4 ],
				],
				'portrait_ratios' => [
					[ 'x' => 3, 'y' => 5 ],
				]
			],
		];
	}


	/**
	 * Init submodule
	 * 
	 * @return void
	 */
	public function init(): void {

		/** Check if options are setted */
		$options = get_option( "wp_pronotron_options" );

		if ( ! $options ){

			delete_option( 'wp_pronotron_registered_images' );
			//DevelopmentUtils::console_log( 'Submodule "control_image_sizes" stopped. (No options)' );
			return;
		}

		/** Retrieve submodule options */
		$submodule_id = $this->submodule_options()[ 'id' ];

		if ( $options[ $submodule_id ] && ! empty( $options[ $submodule_id ] ) ){

			$this->user_options = $options[ $submodule_id ];
		}

		$this->clean_default_image_sizes();
		$this->create_image_ratios();
		$this->edit_selectable_image_sizes();

		add_filter( 'wp_handle_upload_prefilter', [ $this, 'force_image_sizes' ] ); 
	}

	
	/**
	 * Init image ratios if user has saved some
	 * Sort them in a logic, and create @media queries
	 * 
	 * @return void
	 */
	public function create_image_ratios(){

		$upload_sizes = $this->user_options[ 'upload_sizes' ];
		$registered = array();


		/** 
		 * Create landscape image sizes
		 */

		/** Sort medias by ratio value and create media attributes */
		$medias_landscape = array();

		foreach ( $this->user_options[ 'landscape_ratios' ] as $index => $ratio ){

			$x = $ratio[ 'x' ];
			$y = $ratio[ 'y' ];
			$ratio_name = "landscape_{$x}_{$y}";

			$landscape_size_full = array( 
				$upload_sizes[ 'width' ],
				$upload_sizes[ 'width' ] / $x * $y
			);
			add_image_size( "{$ratio_name}_full", $landscape_size_full[0], $landscape_size_full[1], true );

			$landscape_size_medium = array(
				$landscape_size_full[0] * 0.75,
				$landscape_size_full[1] * 0.75,
			);
			add_image_size( "{$ratio_name}_medium", $landscape_size_medium[0], $landscape_size_medium[1], true );

			$option = array(
				"id"			=> "{$x}_{$y}",
				"type"			=> "landscape",
				"label" 		=> "Landscape {$x}:{$y}",
				"registered" 	=> [ "{$ratio_name}_full", "{$ratio_name}_medium" ],
				"ratio"			=> [ $x, $y ],
				"px_sizes" 		=> [ $landscape_size_full, $landscape_size_medium ]
			);

			array_push( $medias_landscape, array( 
				'id' 	=> $option[ 'id' ], 
				'media' => "{$x}/{$y}", 
				'mult' 	=> $x / $y 
			));
			array_push( $registered, $option );
		}


		// Sort medias by x ratio / y ratio
		usort( $medias_landscape, function( $first, $second ){
			return $first[ 'mult' ] < $second[ 'mult' ] ? -1 : 1;
		});

		/** Create media strings */
		for ( $i = 0; $i < count( $medias_landscape ); $i++ ){

			$id = $medias_landscape[ $i ][ 'id' ];

			$media = "(orientation: landscape)";

			if ( isset( $medias_landscape[ $i - 1 ] ) ){
				$media .= " and (min-aspect-ratio: {$medias_landscape[$i-1]['media']})";
			}

			if ( isset( $medias_landscape[ $i + 1 ] ) ) {
				$media .= " and (max-aspect-ratio: {$medias_landscape[$i]['media']})";
			}

			$key = array_search( $id, array_column( $registered, 'id' ) );
			$registered[ $key ][ 'media' ] = $media; 
		}




		/** 
		 * Create portrait image sizes
		 */

		/** Sort medias by ratio value and create media attributes */
		$medias_portrait = array();

		foreach ( $this->user_options[ 'portrait_ratios' ] as $index => $ratio ){

			$x = $ratio[ 'x' ];
			$y = $ratio[ 'y' ];
			$ratio_name = "portrait_{$x}_{$y}";

			$portrait_size_full = array( 
				$upload_sizes[ 'height' ] / $y * $x,
				$upload_sizes[ 'height' ]
			);
			add_image_size( "{$ratio_name}_full", $portrait_size_full[0], $portrait_size_full[1], true );

			$portrait_size_medium = array(
				$portrait_size_full[0] * 0.75,
				$portrait_size_full[1] * 0.75,
			);
			add_image_size( "{$ratio_name}_medium", $portrait_size_medium[0], $portrait_size_medium[1], true );

			$option = array(
				"id"			=> "{$x}_{$y}",
				"type"			=> "portrait",
				"label" 		=> "Portrait {$x}:{$y}",
				"registered" 	=> [ "{$ratio_name}_full", "{$ratio_name}_medium" ],
				"ratio"			=> [ $x, $y ],
				"px_sizes" 		=> [ $portrait_size_full, $portrait_size_medium ]
			);

			array_push( $medias_portrait, array( 
				'id' 	=> $option[ 'id' ], 
				'media' => "{$x}/{$y}", 
				'mult' 	=> $x / $y
			));
			array_push( $registered, $option );
		}


		// Sort medias by x ratio / y ratio
		usort( $medias_portrait, function( $first, $second ){
			return $first[ 'mult' ] > $second[ 'mult' ] ? -1 : 1;
		});

		/** Create media strings */
		for ( $i = 0; $i < count( $medias_portrait ); $i++ ){

			$id = $medias_portrait[ $i ][ 'id' ];

			$media = "(orientation: portrait)";

			if ( isset( $medias_portrait[ $i - 1 ] ) ){
				$media .= " and (max-aspect-ratio: {$medias_portrait[$i-1]['media']})";
			}

			if ( isset( $medias_portrait[ $i + 1 ] ) ) {
				$media .= " and (min-aspect-ratio: {$medias_portrait[$i]['media']})";
			}

			$key = array_search( $id, array_column( $registered, 'id' ) );
			$registered[ $key ][ 'media' ] = $media; 
		}


		update_option( 'wp_pronotron_registered_images', $registered );

		//DevelopmentUtils::console_log( $registered );
		//DevelopmentUtils::console_log( get_option( 'wp_pronotron_registered_images' ) );
		//DevelopmentUtils::console_log( wp_get_registered_image_subsizes() );
	}


	/**
	 * Control selectable image sizes in editor for "core/image" blocks
	 * 
	 * @return void
	 */
	public function edit_selectable_image_sizes(){

		$registered_images = get_option( 'wp_pronotron_registered_images' );

		/**
		 * image -> label
		 * "landscape_5_3_full" => "Landscape 5:3",
		 * DevelopmentUtils::console_log( $registered_images );
		 * 
		 * Only adds full version to use in gutenberg editor
		 */
		$custom_sizes = array();

		foreach( $registered_images as $custom_image ){
			$custom_sizes[ $custom_image[ 'registered' ][0] ] = $custom_image[ 'label' ];
		}
		
		add_filter( 'image_size_names_choose', function( $sizes ) use( $custom_sizes ){
		    return array_merge( $sizes, $custom_sizes );
		});
	}


	/**
	 * Remove default image sizes to keep image pipeline clean
	 * 
	 * @return void
	 */
	public function clean_default_image_sizes(){

		add_filter( 'intermediate_image_sizes', function( $sizes ){
			$removeSizes = array( '1536x1536', '2048x2048', 'medium_large', 'medium', 'large' );
			return array_diff( $sizes, $removeSizes );
		});

		// add_filter( 'edit_custom_thumbnail_sizes', '__return_true' );
		// add_filter( 'edit_custom_thumbnail_sizes', function(){ return array( 'vertical_large' ); } );
	}


	/**
	 * Force uploaded image sizes to defined width and height
	 * 
	 * @param image $file - Uploaded file
	 * @return image 
	 */
	public function force_image_sizes( $file ){
		
		$minimum = $this->user_options[ 'upload_sizes' ];
		$img = getimagesize( $file[ 'tmp_name' ] );

		/** Allow square images for badges, icons, thumbnails */
		if ( $img[0] == $img[1] ){
			return $file;
		}

		if ( $img[0] < $minimum[ 'width' ] ){
			$file[ 'error' ] = 'Image too small. Minimum width is ' . $minimum[ 'width' ] . 'px. Uploaded image width is ' . $img[0] . 'px';
		} elseif ( $img[1] < $minimum[ 'height' ] ){
			$file[ 'error' ] = 'Image too small. Minimum height is ' . $minimum[ 'height' ] . 'px. Uploaded image height is ' . $img[1] . 'px';
		}

		return $file;
	}
}

return new ControlImageSizes();