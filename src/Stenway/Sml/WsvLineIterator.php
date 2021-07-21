<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvLine as WsvLine;

abstract class WsvLineIterator {	
	abstract public function hasLine() : bool;
	abstract public function isEmptyLine() : bool;
	abstract public function getLine() : WsvLine;
	abstract public function getLineAsArray() : array;
	abstract public function getEndKeyword() : ?string;
	abstract public function getLineIndex() : int;
}

?>