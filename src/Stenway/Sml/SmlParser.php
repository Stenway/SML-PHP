<?php

namespace Stenway\Sml;

use \Stenway\Wsv\WsvDocument as WsvDocument;

class SmlParser {
	
	private const ONLY_ONE_ROOT_ELEMENT_ALLOWED					= "Only one root element allowed";
	private const ROOT_ELEMENT_EXPECTED							= "Root element expected";
	private const INVALID_ROOT_ELEMENT_START					= "Invalid root element start";
	private const NULL_VALUE_AS_ELEMENT_NAME_IS_NOT_ALLOWED		= "Null value as element name is not allowed";
	private const NULL_VALUE_AS_ATTRIBUTE_NAME_IS_NOT_ALLOWED	= "Null value as attribute name is not allowed";
	private const END_KEYWORD_COULD_NOT_BE_DETECTED				= "End keyword could not be detected";
	
	
	static function parseDocument(string $content) : SmlDocument {
		$wsvDocument = WsvDocument::parse($content);
		$endKeyword = self::determineEndKeyword($wsvDocument);
		$iterator = new WsvDocumentLineIterator($wsvDocument, $endKeyword);
		
		$document = new SmlDocument();
		$document->setEndKeyword($endKeyword);
		
		$document->emptyNodesBefore = self::readEmptyNodes($iterator);
		
		$rootElement = self::readRootElement($iterator);
		self::readElementContent($iterator, $rootElement);
		$document->setRoot($rootElement);
		
		$document->emptyNodesAfter = self::readEmptyNodes($iterator);
		if ($iterator->hasLine()) {
			throw self::getException($iterator, self::ONLY_ONE_ROOT_ELEMENT_ALLOWED);
		}
		return $document;
	}
	
	private static function equalIgnoreCase(?string $name1, ?string $name2) : bool {
		if ($name1 === null) {
			return $name1 === $name2;
		}
		return strcasecmp($name1, $name2) == 0;
	}
	
	public static function readRootElement(WsvLineIterator $iterator) : SmlElement {
		if (!$iterator->hasLine()) {
			throw self::getException($iterator, self::ROOT_ELEMENT_EXPECTED);
		}
		$rootStartLine = $iterator->getLine();
		if (!$rootStartLine->hasValues() || count($rootStartLine->values) != 1 
				|| self::equalIgnoreCase($iterator->getEndKeyword(), $rootStartLine->values[0])) {
			throw self::getLastLineException($iterator, self::INVALID_ROOT_ELEMENT_START);
		}
		$rootElementName = $rootStartLine->values[0];
		if ($rootElementName == null) {
			throw self::getLastLineException($iterator, self::NULL_VALUE_AS_ELEMENT_NAME_IS_NOT_ALLOWED);
		}
		$rootElement = new SmlElement($rootElementName);
		$rootElement->_setWhitespacesAndComment($rootStartLine->getWhitespaces(), $rootStartLine->getComment());
		return $rootElement;
	}
	
	public static function readNode(WsvLineIterator $iterator, SmlElement $parentElement) : ?SmlNode {
		$node = null;
		$line = $iterator->getLine();
		if ($line->hasValues()) {
			$name = $line->values[0];
			if (count($line->values) == 1) {
				if (self::equalIgnoreCase($iterator->getEndKeyword(), $name)) {
					$parentElement->_setEndWhitespacesAndComment($line->getWhitespaces(), $line->getComment());
					return null;
				}
				if ($name === null) {
					throw self::getLastLineException($iterator, self::NULL_VALUE_AS_ELEMENT_NAME_IS_NOT_ALLOWED);
				}
				$childElement = new SmlElement($name);
				$childElement->_setWhitespacesAndComment($line->getWhitespaces(), $line->getComment());

				self::readElementContent($iterator, $childElement);

				$node = $childElement;
			} else {
				if ($name === null) {
					throw self::getLastLineException($iterator, self::NULL_VALUE_AS_ATTRIBUTE_NAME_IS_NOT_ALLOWED);
				}
				$values = array_slice($line->values, 1);
				$childAttribute = new SmlAttribute($name, $values);
				$childAttribute->_setWhitespacesAndComment($line->getWhitespaces(), $line->getComment());

				$node = $childAttribute;
			}
		} else {
			$emptyNode = new SmlEmptyNode();
			$emptyNode->_setWhitespacesAndComment($line->getWhitespaces(), $line->getComment());

			$node = $emptyNode;
		}
		return $node;
	}
	
	private static function readElementContent(WsvLineIterator $iterator, SmlElement $element) {
		while (true) {
			if (!$iterator->hasLine()) {
				throw self::getLastLineException($iterator, "Element \"" . $element->getName() . "\" not closed");
			}
			$node = self::readNode($iterator, $element);
			if ($node === null) {
				break;
			}
			$element->add($node);
		}
	}
	
	private static function readEmptyNodes(WsvLineIterator $iterator) : array {
		$nodes = [];
		while ($iterator->isEmptyLine()) {
			$emptyNode = self::readEmptyNode($iterator);
			array_push($nodes, $emptyNode);
		}
		return $nodes;
	}
	
	private static function readEmptyNode(WsvLineIterator $iterator) : SmlEmptyNode {
		$line = $iterator->getLine();
		$emptyNode = new SmlEmptyNode();
		$emptyNode->_setWhitespacesAndComment($line->getWhitespaces(), $line->getComment());
		return $emptyNode;
	}
	
	private static function determineEndKeyword(WsvDocument $wsvDocument) : ?string {
		for ($i=count($wsvDocument->lines)-1; $i>=0; $i--) {
			$values = $wsvDocument->lines[$i]->values;
			if ($values !== null) {
				if (count($values) == 1) {
					return $values[0];
				} else if (count($values) > 1) {
					break;
				}
			}
		}
		throw self::getParserException(count($wsvDocument->lines)-1, self::END_KEYWORD_COULD_NOT_BE_DETECTED);
	}
	
	private static function getParserException(int $line, string $message) {
		return new \Exception(sprintf("%s (%d)", $message, $line + 1));
	}
	
	private static function getException(WsvLineIterator $iterator, string $message) {
		return self::getParserException($iterator->getLineIndex(), $message);
	}

	private static function getLastLineException(WsvLineIterator $iterator, string $message) {
		return self::getParserException($iterator->getLineIndex()-1, $message);
	}
}

?>