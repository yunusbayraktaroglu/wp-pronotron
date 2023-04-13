<?php

namespace WPPronotron\Module;

use WPPronotron\Module;
use WPPronotron\Utils\ImageUtils;
use WPPronotron\Utils\DevelopmentUtils;

/**
 * Carousel Module
 * Add functionalty to "core/gallery" blocks, transform image carousels.
 * 
 * @package WPPronotron\Module
 */

class CarouselModule extends Module {

    public function module(): array {
		return [
			'name' 			=> __( 'Carousel module', 'pronotron' ),
			'description' 	=> __( 'Create image carousels from "core/gallery" blocks.', 'pronotron' ),
		];
    }

    public function init_module(): void {

		$this->add_submodule( 'core-gallery-block-customize' );
		// $this->debug();

    }

	public function debug(): void {
	}
	
}

return new CarouselModule();