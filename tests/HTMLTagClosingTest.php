<?php
include dirname(__FILE__) . '/../include/HTMLTagClosing.php';

/**
 * @file HTMLTagClosingTest.php
 * Contains a dummy class to test the HTMLTagClosing class.
 */

/**
 * Dummy class for testing the abstract class HTMLTagClosing
 */
class HTMLTagClosingTest extends HTMLTagClosing {
	
}

$test = new HTMLTagClosingTest();

assert($test->__toString() == " />");
?>
