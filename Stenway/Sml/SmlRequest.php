<?php

namespace Stenway\Sml;

class SmlRequest {
	static function getDocument() : ?SmlDocument {
		if (!isset($_POST['smlRequest'])) {
			SmlResponse::writeError( "Request contains no SML document");
			return null;
		}
		$smlRequest = $_POST['smlRequest'];
		$requestDocument = null;
		try {
			$requestDocument = SmlDocument::parse($smlRequest);
		} catch (Exception $e) {
			SmlResponse::writeError("Request SML document is invalid", $e->getMessage());
			return null;
		}
		return $requestDocument;
	}
}

?>