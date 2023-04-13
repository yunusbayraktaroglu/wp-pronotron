<?php

/**
 * WP Pronotron Activation / Deactivation hooks.
 */
register_activation_hook( plugin_dir_path( __DIR__ ) . '/wp-pronotron.php', 'wp_pronotron_activation_callback' );
register_deactivation_hook( plugin_dir_path( __DIR__ ) . '/wp-pronotron.php', 'wp_pronotron_deactivation_callback' );

/** On activation */
function wp_pronotron_activation_callback(){

	do_action( 'wp_pronotron_activate' );
	update_option( 'wp_pronotron_version', WPPRONOTRON_VERSION );

}

/** On deactivation */
function wp_pronotron_deactivation_callback(){

	do_action( 'wp_pronotron_deactivate' );
	delete_option( 'wp_pronotron_version' );
	delete_option( "wp_pronotron_options" );
	delete_option( 'wp_pronotron_registered_images' );
	
}