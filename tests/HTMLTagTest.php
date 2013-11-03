<?php
include dirname(__FILE__) . '/../include/HTMLTag.php';

/**
 * @file HTMLTagTest.php
 * Contains a dummy class to test the HTMLTag class.
 */

/**
 * Dummy class for testing the abstract class HTMLTag
 */
class HTMLTagTest extends HTMLTag {
	
}

$test = new HTMLTagTest();

assert_options(ASSERT_BAIL);

$test->setAccesskey("a");
assert($test->getAccesskey() == "a");

$test->setClass("b");
assert($test->getClass() == "b");

$test->setDir("c");
assert($test->getDir() == "c");

$test->setId("d");
assert($test->getId() == "d");

$test->setLang("e");
assert($test->getLang() == "e");

$test->setTabindex("f");
assert($test->getTabindex() == "f");

$test->setTitle("g");
assert($test->getTitle() == "g");

assert($test->__toString() == " accesskey=\"a\" class=\"b\" dir=\"c\" " . 
	"id=\"d\" lang=\"e\" tabindex=\"f\" title=\"g\"");

$test->setAttribute("test1", "test2");
assert($test->getAttribute("test1") == "test2");

$test->setClass(NULL);
assert($test->getClass() == NULL);

$test->addClass("a")
	 ->addClass("b")
	 ->addClass("c")
	 ->removeClass("a");
assert($test->getClass() == "b c");

?>