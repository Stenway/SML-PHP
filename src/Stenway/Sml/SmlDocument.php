<?php

namespace Stenway\Sml;

use \Stenway\ReliableTxt\ReliableTxtDocument as ReliableTxtDocument;
use \Stenway\ReliableTxt\ReliableTxtEncoding as ReliableTxtEncoding;

class SmlDocument {
	private SmlElement $root;
	private int $encoding = ReliableTxtEncoding::UTF_8;
	private ?string $endKeyword = "End";
	private ?string $defaultIndentation = null;
	
	public array $emptyNodesBefore = [];
	public array $emptyNodesAfter = [];
	
	function __construct(string $rootName = "Root") {
		$this->root = new SmlElement($rootName);
	}
	
	function setDefaultIndentation(?string $defaultIndentation) {
		if ($defaultIndentation !== null && strlen($defaultIndentation) > 0 &&
				!WsvString::isWhitespace($defaultIndentation)) {
			throw new IllegalArgumentException(
					"Indentation value contains non whitespace character");
		}
		$this->defaultIndentation = $defaultIndentation;
	}
	
	function getDefaultIndentation() : ?string {
		return $this->defaultIndentation;
	}
	
	function setEndKeyword(?string $endKeyword) {
		$this->endKeyword = $endKeyword;
	}
	
	function getEndKeyword() : ?string {
		return $this->endKeyword;
	}
	
	function setEncoding(int $encoding) {
		$this->encoding = $encoding;
	}
	
	function getEncoding() : int {
		return $this->encoding;
	}
	
	function setRoot(SmlElement $root) {
		$this->root = $root;
	}
	
	function getRoot() : SmlElement {
		return $this->root;
	}
	
	function __toString() : string {
		return $this->toString();
	}
	
	function toString() : string {
		return SmlSerializer::serializeDocument($this);
	}
	
	function toStringMinified() : string {
		return SmlSerializer::serializeDocumentNonPreserving($this, true);
	}
	
	function echoMinified() {
		echo $this->toStringMinified();
	}
	
	function save(string $filePath) {
		$content = self::toString();
		$file = new ReliableTxtDocument($content, $this->encoding);
		$file->save($filePath);
	}
	
	static function parse(string $content) : SmlDocument {
		return SmlParser::parseDocument($content);
	}

	static function load(string $filePath) : SmlDocument {
		$file = ReliableTxtDocument::load($filePath);
		$content = $file->getText();
		$document = self::parse($content);
		$document->setEncoding($file->getEncoding());
		return $document;
	}
}

?>