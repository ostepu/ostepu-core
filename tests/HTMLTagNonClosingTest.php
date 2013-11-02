<?php
include dirname(__FILE__) . '/../include/HTMLTagNonClosing.php';
class HTMLTagNonClosingTest extends HTMLTagNonClosing {
	
}

$test = new HTMLTagNonClosingTest();

assert($test->__toString() == ">\n    ");
?>