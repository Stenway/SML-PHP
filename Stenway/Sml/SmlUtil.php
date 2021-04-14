<?php

namespace Stenway\Sml;

abstract class SmlUtil {
	static function isEqual(string $name1, string $name2) {
		return strcasecmp($name1, $name2) == 0;
	}
}

?>