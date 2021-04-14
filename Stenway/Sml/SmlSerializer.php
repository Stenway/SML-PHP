<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvLine as WsvLine;
use \Stenway\Wsv\WsvDocument as WsvDocument;
use \Stenway\Wsv\WsvSerializer as WsvSerializer;

class SmlSerializer {
	public static function serializeDocument(SmlDocument $document) : string {
		$wsvDocument = new WsvDocument();
		
		self::serialzeEmptyNodes($document->emptyNodesBefore, $wsvDocument);
		$document->getRoot()->toWsvLines($wsvDocument, 0, $document->getDefaultIndentation(), $document->getEndKeyword());
		self::serialzeEmptyNodes($document->emptyNodesAfter, $wsvDocument);
		
		return $wsvDocument->toString();
	}
	
	public static function serializeElement2(SmlElement $element) : string {
		$wsvDocument = new WsvDocument();
		$element->toWsvLines($wsvDocument, 0, null, "End");
		return $wsvDocument->toString();
	}
	
	public static function serializeElementMinified(SmlElement $element) : string {
		$wsvDocument = new WsvDocument();
		$element->toWsvLines($wsvDocument, 0, "", null);
		return $wsvDocument->toString();
	}

	public static function serializeAttribute2(SmlAttribute $attribute) : string {
		$wsvDocument = new WsvDocument();
		$attribute->toWsvLines($wsvDocument, 0, null, null);
		return $wsvDocument->toString();
	}
	
	public static function serializeEmptyNode2(SmlEmptyNode $emptyNode) : string {
		$wsvDocument = new WsvDocument();
		$emptyNode->toWsvLines($wsvDocument, 0, null, null);
		return $wsvDocument->toString();
	}
	
	private static function serialzeEmptyNodes(array $emptyNodes, WsvDocument $wsvDocument) {
		foreach ($emptyNodes as $emptyNode) {
			$emptyNode->toWsvLines($wsvDocument, 0, null, null);
		}
	}

	public static function serializeElement(SmlElement $element, WsvDocument $wsvDocument,
			int $level, ?string $defaultIndentation, ?string $endKeyword) {
		$childLevel = $level + 1;
		
		$whitespaces = self::getWhitespaces($element->getWhitespaces(), $level, $defaultIndentation);
		$line = new WsvLine();
		$line->_set([$element->getName()], $whitespaces, $element->getComment());
		$wsvDocument->addLine($line);
		
		foreach ($element->nodes as $child) {
			$child->toWsvLines($wsvDocument, $childLevel, $defaultIndentation, $endKeyword);
		}
		
		$endWhitespaces = self::getWhitespaces($element->getEndWhitespaces(), $level, $defaultIndentation);
		$endLine = new WsvLine();
		$endLine->_set([$endKeyword], $endWhitespaces, $element->getEndComment());
		$wsvDocument->addLine($endLine);
	}
	
	private static function getWhitespaces(?array $whitespaces, int $level, 
			?string $defaultIndentation) : array {
		if ($whitespaces !== null && count($whitespaces) > 0) {
			return $whitespaces;
		}
		if ($defaultIndentation === null) {
			$indentStr = str_repeat("\t", $level);
			return [$indentStr];
		} else {
			$indentStr = str_repeat($defaultIndentation, $level);
			return [$indentStr];
		}
	}
		
	public static function serializeAttribute(SmlAttribute $attribute, WsvDocument $wsvDocument,
			int $level, ?string $defaultIndentation) {
		$whitespaces = self::getWhitespaces($attribute->getWhitespaces(), $level, $defaultIndentation);
		$combined = array_merge([$attribute->getName()], $attribute->getValues());
		$line = new WsvLine();
		$line->_set($combined, $whitespaces, $attribute->getComment());
		$wsvDocument->addLine($line);
	}
		
	public static function serializeEmptyNode(SmlEmptyNode $emptyNode, WsvDocument $wsvDocument, 
			int $level, ?string $defaultIndentation) {
		$whitespaces = self::getWhitespaces($emptyNode->getWhitespaces(), $level, $defaultIndentation);
		
		$line = new WsvLine();
		$line->_set(null, $whitespaces, $emptyNode->getComment());
		$wsvDocument->addLine($line);
	}
	
	public static function serializeDocumentNonPreserving(SmlDocument $document, bool $minified = false) : string {
		$defaultIndentation = $document->getDefaultIndentation();
		if ($defaultIndentation === null) {
			$defaultIndentation = "\t";
		}
		$endKeyword = $document->getEndKeyword();
		if ($minified) {
			$defaultIndentation = "";
			$endKeyword = null;
		}
		$result = self::serializeElementNonPreserving($document->getRoot(), 0, $defaultIndentation, $endKeyword);
		$result = substr($result, 0, -1);
		return $result;
	}

	private static function serializeElementNonPreserving(SmlElement $element,
			int $level, string $defaultIndentation, ?string $endKeyword) : string {
		$result = "";
		$result .= self::getIndentationString($defaultIndentation, $level);
		$result .= WsvSerializer::serializeValue($element->getName());
		$result .= "\n"; 

		$childLevel = $level + 1;
		foreach ($element->nodes as $child) {
			if ($child instanceof SmlElement) {
				$result .= self::serializeElementNonPreserving($child, $childLevel, $defaultIndentation, $endKeyword);
			} else if ($child instanceof SmlAttribute) {
				$result .= self::serializeAttributeNonPreserving($child, $childLevel, $defaultIndentation);
			}
		}
		
		$result .= self::getIndentationString($defaultIndentation, $level);
		$result .= WsvSerializer::serializeValue($endKeyword);
		$result .= "\n";
		return $result;
	}
	
	private static function serializeAttributeNonPreserving(SmlAttribute $attribute,
			int $level, string $defaultIndentation) : string {
		$result = "";
		$result .= self::getIndentationString($defaultIndentation, $level);
		$result .= WsvSerializer::serializeValue($attribute->getName());
		$result .= " "; 
		$result .= WsvSerializer::serializeLineValues($attribute->getValues());
		$result .= "\n"; 
		return $result;
	}
	
	private static function getIndentationString(string $defaultIndentation, int $level) : string {
		return str_repeat($defaultIndentation, $level);
	}
}

?>