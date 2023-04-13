<?php

namespace WPPronotron\Module\AutoMetafieldsModule;

use WPPronotron\Utils\DependencyUtils;

/**
 * Submodule MetafieldModule
 *
 * Get passed custom metas with custom scheme via WordPress Filters
 * 
 * - Create post metas, and default values as empty strings
 * - Localize them and pass to create block editor fields
 * 
 * 
 * 
 * @fix - custom metas saving as json strings - is that mandatory??
 */

class MetafieldCreator {

	public string $submodule_id;
	public string $submodule_dir;
	public string $submodule_url;

	public array $metafield_extensions;

	public function init(): void {
		
		/**
		 * Allow themes & plugins to add metafields via filters
		 * WP Pronotron custom filters runs after themes setup
		 */
		$metafield_extensions = apply_filters( 'wp_pronotron_panel_extension', false );

		if ( ! $metafield_extensions ){
			return;
		}

		$this->metafield_extensions = $metafield_extensions;

		$submodule_id 	= $this->submodule_id;
		$submodule_dir 	= $this->submodule_dir;
		$submodule_url 	= $this->submodule_url;
		$local 			= [ 'id' => 'panel_extension_options', 'data' => $metafield_extensions ];

		add_action( 'enqueue_block_editor_assets', function() use( $submodule_id, $submodule_dir, $submodule_url, $local ){
			DependencyUtils::load_dependency( $submodule_id, $submodule_dir, $submodule_url, $local );
		});

		add_action( 'init', function() use( $metafield_extensions ){
			$this->register_post_metas( $metafield_extensions );
		});
	}


	/**
	 * Register passed post metas as JSON strings
	 * 
	 * @param array $metas
	 * @return void
	 */
	public function register_post_metas( $metas ): void {

		foreach ( $metas as $meta ){

			/**
			 * Empty string means register meta for all post types
			 */
			$post_type 	= $meta[ 'post_type' ] ?? '';
			$meta_id 	= $meta[ 'meta_id' ] ?? new WP_Error( 'wp-pronotron-error', 'Meta id (meta_id) is mandatory.' );

			/**
			 * Create default json
			 */
			$jsonized_defaults = array();

			foreach ( $meta[ 'extend' ] as $component ){
				$jsonized_defaults[ $component[ 'id' ] ] = false;
			}
			
			$jsonized_defaults = json_encode( $jsonized_defaults );

			/**
			 * Register post meta
			 */
			register_post_meta( 
				$post_type, 
				$meta_id, 
				array(
					'default'       	=> $jsonized_defaults,
					'show_in_rest'  	=> true,
					'single'        	=> true,
					'type'          	=> 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback' 	=> function(){
						return current_user_can( 'edit_posts' );
					}
				)
			);
		}


		/**
		 * Ability for GraphQL collect custom 'metafields'
		 * for all post types
		 * 
		 * @todo create auto for each meta_id
		 */
		add_action( 'graphql_register_types', function() {

			register_graphql_field( 'ContentNode', 'metafields', [
			   'type' => 'String',
			   'description' => __( 'The metas of the post', 'wp-graphql' ),
			   'resolve' => function( $post ){
					$meta = get_post_meta( $post->ID );
					$meta = json_encode( $meta );
					return ! empty( $meta ) ? $meta : 'test';
				}
			]);
			
		});
		

	}

}

return new MetafieldCreator();
