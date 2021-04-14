<?php

namespace Stenway\Sml;

abstract class SmlResponse {
	static function writeError(string $errorText, ?string $errorDetails = null) {
		$errorDocument = new SmlDocument("SmlRequestError");
		$errorDocument->getRoot()->addString("Error", $errorText);
		if ($errorDetails !== null) {
			$errorDocument->getRoot()->addString("Details", $errorDetails);
		}
		$errorDocument->echoMinified();
	}
}

?>