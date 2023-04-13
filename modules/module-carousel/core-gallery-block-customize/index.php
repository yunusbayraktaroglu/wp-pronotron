<?php

namespace WPPronotron\Module\CarouselModule;

use WPPronotron\Utils\DevelopmentUtils;
use WPPronotron\Utils\DependencyUtils;
use WPPronotron\Utils\ImageUtils;

/**
 * Submodule CustomizeCoreGalleryBlock
 * - Adds data-carousel attribute to html output
 */
class CustomizeCoreGalleryBlock {

	public string $submodule_id;
	public string $submodule_dir;
	public string $submodule_url;

	public function init(): void {

		$submodule_id 	= $this->submodule_id;
		$submodule_dir 	= $this->submodule_dir;
		$submodule_url 	= $this->submodule_url;

		add_action( 'enqueue_block_editor_assets', function() use( $submodule_id, $submodule_dir, $submodule_url ){
			DependencyUtils::load_dependency( $submodule_id, $submodule_dir, $submodule_url );
		});

	}

}

return new CustomizeCoreGalleryBlock();