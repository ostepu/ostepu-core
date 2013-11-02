<?php
include dirname(__FILE__) . '/../include/HTMLTag.php';

class HTMLTagTest extends HTMLTag {
	public function __toString()
	{
		return parent::__toString();
	}
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

assert($test->__toString() == " accesskey=\"a\" class=\"b\" dir=\"c\" id=\"d\" lang=\"e\" tabindex=\"f\" title=\"g\"");

?>