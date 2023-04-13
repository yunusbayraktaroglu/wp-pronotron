<?php

class PronotronFunctions {

	function __construct(){

		add_theme_support( 'menus' );
		add_theme_support( 'post-thumbnails' );

		add_action( 'init', [ $this, 'register_applications_post_type' ] );
		add_filter( 'wp_pronotron_panel_extension', [ $this, 'register_custom_metas' ] );
	}

	/**
	 * When to create new post type
	 * ! NEEDS PERMALINKS FLUSH
	 */
	public function register_applications_post_type(){

		register_post_type( 'applications', array(
			'labels' => array(
				'name' 			=> __( 'Applications', 'pronotron' ),
				'singular_name' => __( 'Application', 'pronotron' )
			),
			'menu_icon' 			=> 'dashicons-heart',
			'menu_position' 		=> 5,
			'has_archive' 			=> true,
			'public' 				=> true,
			'taxonomies' 			=> array( 'category' ),
			'rewrite' 				=> array( 'slug' => 'apps' ),
			'supports' 				=> array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),

			// GraphQL
			'publicly_queryable' 	=> true,
			'show_in_rest' 			=> true,
			'show_in_graphql' 		=> true,
			'graphql_single_name' 	=> 'application',
			'graphql_plural_name' 	=> 'applications',
		));

		register_post_type( 'policies', array(
			'labels' => array(
				'name' 			=> __( 'Policies', 'pronotron' ),
				'singular_name' => __( 'Policy', 'pronotron' )
			),
			'menu_icon' 			=> 'dashicons-pressthis',
			'menu_position' 		=> 6,
			'has_archive' 			=> true,
			'public' 				=> true,
			'rewrite' 				=> array( 'slug' => 'policies' ),
			'supports' 				=> array( 'title', 'editor', 'custom-fields' ),

			// GraphQL
			'publicly_queryable' 	=> true,
			'show_in_rest' 			=> true,
			'show_in_graphql' 		=> true,
			'graphql_single_name' 	=> 'policy',
			'graphql_plural_name' 	=> 'policies',
		));

	}

	public function register_custom_metas(){

		/**
		 * Without 'post_type' it registers for all post types
		 */
		// [
		// 	'meta_id'	=> '_additional',
		// 	'title'		=> 'Additional data',
		// 	'extend'	=> [
		// 		[
		// 			'id'	=> 'is_featured',
		// 			'type'	=> 'switch'
		// 		]
		// 	]
		// ]

		$panel_extensions = [
			[
				'post_type'	=> 'applications',
				'meta_id'	=> '_applications_custom_meta',
				'title'		=> 'Application metas',
				'extend'	=> [
					[
						'id'	=> 'ios_store_link',
						'type'	=> 'string'
					],
					[
						'id'	=> 'android_store_link',
						'type'	=> 'string'
					],
					[
						'id'	=> 'release_date',
						'type'	=> 'string'
					],
					[
						'id'	=> 'lastest_version',
						'type'	=> 'string'
					],
					[
						'id'	=> 'test_link',
						'type'	=> 'string'
					],
					[
						'id'	=> 'icon',
						'type'	=> 'image'
					],
				]
			]
		];

		return $panel_extensions;
	}
}

$pronotron = new PronotronFunctions();