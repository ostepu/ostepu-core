<?php
require_once dirname(__FILE__) . '/../include/HTMLTagClosing.php';

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

assert_options(ASSERT_BAIL);

assert($test->__toString() == " />");
?>
