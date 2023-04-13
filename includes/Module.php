<?php

namespace WPPronotron;

use WPPronotron\Utils\DevelopmentUtils;

abstract class Module {

	public $module_id;
	public $submodules = array();
	public $needs_admin_section = false;

	/**
	 * Every module must have it's module data
	 * and init_module function
	 */
	abstract function module(): array;
	abstract function init_module(): void;

	/** Getters */
	final public function id(): string {
		return $this->module_id;
	}

	final public function name(): string {
		return $this->module()[ 'name' ];
	}

	final public function description(): string {
		return $this->module()[ 'description' ];
	}

	final public function print_description( $args ): void {
		$title = sprintf( '<p id="%1$s">%2$s</p>', $args[ 'id' ], $this->description() );
		echo $title;
	}

	/**
	 * Construct module
	 * 
	 * @param string $module_id
	 * @return void
	 */
	final public function init( string $module_id ): void {

		$this->module_id = $module_id;
		$this->init_module();
		
	}

	/**
	 * Add submodule to the module
	 * 
	 * @param string $submodule_id
	 * @return void
	 */
	final public function add_submodule( string $submodule_id ): void {

		$submodule_dir = WPPRONOTRON_BUILD_DIR . "/{$this->module_id}/{$submodule_id}";
		$submodule_url = WPPRONOTRON_BUILD_URL . "/{$this->module_id}/{$submodule_id}";

		/** Submodules returns created objects when required */
		$submodule = require_once "{$submodule_dir}/index.php";

		$submodule->submodule_id = $submodule_id;
		$submodule->submodule_dir = $submodule_dir;
		$submodule->submodule_url = $submodule_url;

		$submodule->init();

		if ( method_exists( $submodule, 'submodule_options' ) ){
			$this->needs_admin_section = true;
		}

		array_push( $this->submodules, $submodule );
		
	}
	
}