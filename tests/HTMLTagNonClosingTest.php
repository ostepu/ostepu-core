<?php
require_once dirname(__FILE__) . '/../include/HTMLTagNonClosing.php';

/**
 * @file HTMLTagNonClosingTest.php
 * Contains a dummy class to test the HTMLTagNonClosing class.
 */

/**
 * Dummy class for testing the abstract class HTMLTagNonClosing
 */
class HTMLTagNonClosingTest extends HTMLTagNonClosing {
	
}

$test = new HTMLTagNonClosingTest();

assert($test->__toString() == ">\n    ");

$test->setContent("a");
assert($test->getContent() == "a");

$test->addContent("b");
assert($test->getContent() == "a\nb");

$test->removeContent("a");
assert($test->getContent() == "b");

?>