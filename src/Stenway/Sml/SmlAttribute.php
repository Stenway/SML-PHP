<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvDocument as WsvDocument;

class SmlAttribute extends SmlNamedNode {
	private array $values;
	
	function __construct(string $name, array $values) {
		parent::__construct($name);
		self::setValues($values);
	}
	
	function setValues(array $values) {
		if ($values === null || count($values) == 0) {
			throw new Exception("Values must contain at least one value");
		}
		$this->values = $values;
	}
	
	function setValue(string $value) {
		$this->setValues(array($value));
	}
	
	function getValues() : array {
		return $this->values;
	}
	
	function getString($index = 0) : string {
		return $this->values[$index];
	}
	
	function __toString() : string {
		return $this->toString();
	}
	
	function toString() : string {
		return SmlSerializer::serializeAttribute2($this);
	}

	function toWsvLines(WsvDocument $document, int $level, ?string $defaultIndentation, ?string $endKeyword) {
		SmlSerializer::serializeAttribute($this, $document, $level, $defaultIndentation);
	}
}

?>