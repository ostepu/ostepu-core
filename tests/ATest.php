<?php
require_once dirname(__FILE__) . '/../include/A.php';

/**
 * @file ATest.php
 * Contains tests for the A class.
 */

$a = new A();

assert_options(ASSERT_BAIL);

$a->setHref("a");
assert($a->getHref() == "a");

$b = new A();
$a->setContent($b);
assert($a->getContent() != $b);

?>