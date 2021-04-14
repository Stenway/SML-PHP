<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvDocument as WsvDocument;

abstract class SmlNamedNode extends SmlNode {
	private string $name;
	
	function __construct(string $name) {
		$this->setName($name);
	}
	
	function setName(string $name) {
		$this->name = $name;
	}
	
	function getName() : string {
		return $this->name;
	}
	
	function hasName(string $name) : bool {
		return strcasecmp($this->name, $name) == 0;
	}
	
	public abstract function toWsvLines(WsvDocument $document, int $level, ?string $defaultIndentation, ?string $endKeyword);
}

?>