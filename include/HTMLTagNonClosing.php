<?php
include 'HTMLTag.php';

/**
 * HTMLTagContained
 * An abstract class that represents an html element that can have a content
 */
abstract class HTMLTagNonClosing extends HTMLTag {
	protected $content;
	
	/**
	* Turns the element into a string.
	*/
	public function __toString()
	{
		$strVal = parent::__toString();
		$strVal .= ">\n    {$content}";
	}
}
?>