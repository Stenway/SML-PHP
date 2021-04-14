<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvDocument as WsvDocument;

class SmlElement extends SmlNamedNode {
	public array $nodes = [];
	
	private ?array $endWhitespaces = null;
	private ?string $endComment = null;
	
	function __construct(string $name) {
		parent::__construct($name);
	}
	
	function add(SmlNode $node) {
		array_push($this->nodes, $node);
	}
	
	function addElement(string $name) : SmlElement {
		$element = new SmlElement($name);
		self::add($element);
		return $element;
	}
	
	function addAttribute(string $name, array $values) : SmlAttribute {
		$attribute = new SmlAttribute($name, $values);
		self::add($attribute);
		return $attribute;
	}
	
	function addString(string $name, ?string $value) : SmlAttribute {
		return self::addAttribute($name, [$value]);
	}
	
	function getString(string $name) : ?string {
		return self::attribute($name)->getValues()[0];
	}
	
	function _setEndWhitespacesAndComment($whitespaces, $comment) {
		$this->endWhitespaces = $whitespaces;
		$this->endComment = $comment;
	}
	
	function getEndWhitespaces() : ?array {
		return $this->endWhitespaces;
	}
	
	function getEndComment() : ?string {
		return $this->endComment;
	}
	
	function elements(?string $name = null) : array {
		if ($name !== null) {
			return array_values(array_filter($this->nodes, function($node) use ($name) {
				return $node instanceof SmlElement && $node->hasName($name);
			}));
		} else {
			return array_values(array_filter($this->nodes, function($node) {
				return $node instanceof SmlElement;
			}));
		}
	}
	
	function hasElements() : bool {
		return count($this->elements()) > 0;
	}
	
	function element(string $name) : SmlElement {
		return $this->elements($name)[0];
	}
	
	function hasElement(string $name) : bool {
		return count($this->elements($name)) > 0;
	}
	
	function attributes(?string $name = null) : array {
		if ($name !== null) {
			return array_values(array_filter($this->nodes, function($node) use ($name) {
				return $node instanceof SmlAttribute && $node->hasName($name);
			}));
		} else {
			return array_values(array_filter($this->nodes, function($node) {
				return $node instanceof SmlAttribute;
			}));
		}
	}
	
	function hasAttributes() : bool {
		return count($this->attributes()) > 0;
	}
	
	function attribute(string $name) : SmlAttribute {
		return $this->attributes($name)[0];
	}
	
	function hasAttribute(string $name) : bool {
		return count($this->attributes($name)) > 0;
	}
	
	function __toString() : string {
		return $this->toString();
	}
	
	function toString() : string {
		return SmlSerializer::serializeElement2($this);
	}
	
	function toStringMinified() : string {
		return SmlSerializer::serializeElementMinified($this);
	}
	
	function echoMinified() {
		echo $this->toStringMinified();
	}
	
	function toWsvLines(WsvDocument $document, int $level, ?string $defaultIndentation, ?string $endKeyword) {
		SmlSerializer::serializeElement($this, $document, $level, $defaultIndentation, $endKeyword);
	}
}

?>