<?php

namespace WPPronotron\Module;

use WPPronotron\Module;
use WPPronotron\Utils\ImageUtils;
use WPPronotron\Utils\DevelopmentUtils;

/**
 * Class MetafieldModule
 * Auto create metafields and gutenberg sidebar boxes for releated metafields
 * 
 * @package WPPronotron\Module
 */

class AutoMetafieldsModule extends Module {

    public function module(): array {
		return [
			'name' 			=> __( 'Auto metafield module', 'pronotron' ),
			'description' 	=> __( 'Auto create metafields and related gutenberg block editor panels.', 'pronotron' ),
		];
    }

    public function init_module(): void {

		/** Auto create metafields and related panels on block editor to edit these metas */
		$this->add_submodule( 'metafield-creator' );
		//$this->debug();

    }

	public function debug(): void {

		/** Get meta fields for post */
		//DevelopmentUtils::console_log( get_post_meta( 194 ) );

	}
	
}

return new AutoMetafieldsModule();