<?php
include 'HTMLTag.php';

/**
 * HTMLTagContained
 * An abstract class that represents an html element that is self-closing (such as <br />)
 */
abstract class HTMLTagClosing extends HTMLTag {
	
	/**
	* Turns the element into a string.
	*
	* @return The string representation of the element. (the element as html source code)
	*/
	public function __toString()
	{
		$strVal = parent::__toString();
		$strVal .= " />";
	}
}
?>