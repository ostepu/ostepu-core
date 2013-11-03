<?php
require_once 'HTMLTagNonClosing.php';

/**
 * @file Div.php
 * Contains the Div class.
 */

/**
 * A class that represents a div element
 */
class Div extends HTMLTagNonClosing {
	
	/**
	* Turns the element into a string.
	*
	* @return The string representation of the element. (the element as 
	* html source code)
	*/
	public function __toString()
	{
		$strVal = "<div" . parent::__toString();
		$strVal .= "\n</div>";
		
		return $strVal;
	}
}

?>