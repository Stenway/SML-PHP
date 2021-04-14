<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvDocument as WsvDocument;

abstract class SmlNode {
	private ?array $whitespaces = null;
	private ?string $comment = null;
	
	function _setWhitespacesAndComment($whitespaces, $comment) {
		$this->whitespaces = $whitespaces;
		$this->comment = $comment;
	}
	
	function getWhitespaces() : ?array {
		return $this->whitespaces;
	}
	
	function getComment() : ?string {
		return $this->comment;
	}
	
	public abstract function toWsvLines(WsvDocument $document, int $level, ?string $defaultIndentation, ?string $endKeyword);
}

?>