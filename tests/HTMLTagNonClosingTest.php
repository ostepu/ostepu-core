<?php
include dirname(__FILE__) . '/../include/HTMLTagNonClosing.php';

/**
 * Dummy class for testing the abstract class HTMLTagNonClosing
 */
class HTMLTagNonClosingTest extends HTMLTagNonClosing {
	
}

$test = new HTMLTagNonClosingTest();

assert($test->__toString() == ">\n    ");
?>