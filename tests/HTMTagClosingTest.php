<?php
include dirname(__FILE__) . '/../include/HTMLTagClosing.php';

/**
 * Dummy class for testing the abstract class HTMLTagClosing
 */
class HTMLTagClosingTest extends HTMLTagClosing {
	
}

$test = new HTMLTagClosingTest();

assert($test->__toString() == " />");
?>
