<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvLine as WsvLine;
use \Stenway\Wsv\WsvDocument as WsvDocument;

class WsvDocumentLineIterator extends WsvLineIterator {
	private WsvDocument $wsvDocument;
	private ?string $endKeyword;
	
	private int $index = 0;
	
	function __construct(WsvDocument $wsvDocument, ?string $endKeyword) {
		$this->wsvDocument = $wsvDocument;
		$this->endKeyword = $endKeyword;
	}
	
	function getEndKeyword() : ?string {
		return $this->endKeyword;
	}
	
	function hasLine() : bool {
		return $this->index < count($this->wsvDocument->lines);
	}
	
	function isEmptyLine() : bool {
		return self::hasLine() && !$this->wsvDocument->lines[$this->index]->hasValues();
	}
	
	function getLine() : WsvLine {
		$line = $this->wsvDocument->lines[$this->index];
		$this->index++;
		return $line;
	}
	
	function getLineAsArray() : array {
		return self::getLine()->values;
	}
	
	function getLineIndex() : int {
		return $this->index;
	}
}

?>