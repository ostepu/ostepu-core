<?php
include 'HTMLTagNonClosing.php';

/**
 * @file Div.php
 * Contains the Div class.
 */

/**
 * A class that represents a div element
 */
class Div extends HTMLTagNonClosing {
	public function __toString()
	{
		$strVal = "<div" . parent::__toString();
		$strVal .= "\n</div>";
		
		return $strVal;
	}
}

?>