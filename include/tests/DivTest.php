<?php
require_once dirname(__FILE__) . '/../include/Div.php';

/**
 * @file DivTest.php
 * Contains tests for the Div class.
 */

$d = new Div();

assert_options(ASSERT_BAIL);

assert($d->__toString() == "<div>\n    \n</div>");

$d->addContent("a");
assert($d->__toString() == "<div>\n    a\n</div>");

$d->addContent("b");
assert($d->__toString() == "<div>\n    a\nb\n</div>");
?>
