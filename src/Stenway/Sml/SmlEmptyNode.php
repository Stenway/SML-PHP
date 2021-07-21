<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvDocument as WsvDocument;

class SmlEmptyNode extends SmlNode {
	function __toString() : string {
		return $this->toString();
	}
	
	function toString() : string {
		return SmlSerializer::serializeEmptyNode2($this);
	}
	
	
	function toWsvLines(WsvDocument $document, int $level, ?string $defaultIndentation, ?string $endKeyword) {
		SmlSerializer::serializeEmptyNode($this, $document, $level, $defaultIndentation);
	}
}

?>