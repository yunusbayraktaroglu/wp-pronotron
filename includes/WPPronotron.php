<?php

require_once __DIR__ . '/Module.php';
require_once __DIR__ . '/PluginUpdater.php';
require_once __DIR__ . '/AdminPage.php';
require_once __DIR__ . '/ActivationDeactivation.php';
require_once __DIR__ . '/utils/index.php';

use WPPronotron\AdminPage;
use WPPronotron\PluginUpdater;
use WPPronotron\Utils;

/**
 * Class WPPronotron
 * @package WPPronotron
 */
final class WPPronotron {

	private static $instance;
	private static $admin_page;
	private static $plugin_updater;

	/**
	 * Registered modules
	 */
	public static $modules = array();

	/**
	 * The instance of the WPPronotron object
	 * @return \WPPronotron - The one true WPPronotron
	 */
	public static function instance(){

		if ( ! isset( self::$instance ) || ! ( self::$instance instanceof self ) ) {

			self::$instance = new self();
			self::$admin_page = new AdminPage();
			self::$plugin_updater = new PluginUpdater();
			self::$plugin_updater->init();
			self::$instance->actions();

		}

		return self::$instance;
	}

	/**
	 * Sets up actions to run at certain spots throughout WordPress 
	 * and the WPPronotron execution cycle
	 * @return void
	 */
	private function actions(){

		/**
		 * Init WPPronotron after themes have been setup,
		 * allowing for both plugins and themes to register
		 * things before pronotron_init
		 */
		add_action( 'after_setup_theme', function(){

			$instance = self::instance();
			$instance->register_modules();
			
			do_action( 'pronotron_init', $instance );

		});

		/** Admin page */
		add_action( 'after_setup_theme', [ $this, 'init_admin_page' ] );
	}

	/**
	 * Register modules.
	 * @return void
	 */
	private function register_modules(){

		self::$instance->add_module( 'module-auto-metafields' );
		self::$instance->add_module( 'module-art-direction-images' );
		self::$instance->add_module( 'module-carousel' );

		//Utils\DevelopmentUtils::console_log( self::$modules );
	}

	/**
	 * Register a module to WPPronotron
	 * @param string $module_name
	 * @return void
	 */
	public static function add_module( $module_id ){
		
		$module = require_once WPPRONOTRON_BUILD_DIR . "/$module_id/index.php";
		$module->init( $module_id );

		array_push( self::$modules, $module );
	}

	/**
	 * Initialize admin page
	 * @return void
	 */
	public function init_admin_page(){

		self::$admin_page->init( self::$modules );

		//Utils\DevelopmentUtils::console_log( wp_load_alloptions() );
		//Utils\DevelopmentUtils::console_log( get_option( "wp_pronotron_options" ) );
		//Utils\DevelopmentUtils::console_log( wp_get_registered_image_subsizes() );
	}

}