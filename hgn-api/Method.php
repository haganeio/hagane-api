<?php
namespace Hagane\Resource;

class Method {
	private $key;
	private $method;

	public function __construct() {
		$this->node = array();
	}

	public function get($path, $function) {
		$this->node[$path] = $function;
	}
}

?>