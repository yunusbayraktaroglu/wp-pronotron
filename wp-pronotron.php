<?php

/**
 * Plugin Name:       WP Pronotron
 * Description:       A modular WordPress plugin that collects web development practices that I often use.
 * Version:           1.0.0-alpha.0
 * GitHub Plugin URI: https://github.com/yunusbayraktaroglu/wp-pronotron
 * Author:            Yunus Bayraktaroglu
 * Author URI:        https://pronotron.com/
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * License:           GPL-3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       pronotron
 *
 * @package  WPPronotron
 * @dependency - Php Imagick
 */

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

define( 'WPPRONOTRON_VERSION', '1.0.0-alpha.0' );
define( 'WPPRONOTRON_BUILD_DIR', plugin_dir_path( __FILE__ ) . 'build' );
define( 'WPPRONOTRON_BUILD_URL', plugins_url( '/build', __FILE__ ) );
define( 'WPPRONOTRON_SLUG', plugin_basename( __DIR__ ) );

/** Bootstrap Plugin */
if ( ! class_exists( 'WPPronotron' ) ){
	require_once __DIR__ . '/includes/WPPronotron.php';
}

/**
 * Instantiates main class
 * @return object
 */
if ( ! function_exists( 'wp_pronotron_init' ) ){

	function wp_pronotron_init(){
		return \WPPronotron::instance();
	}

}
wp_pronotron_init();