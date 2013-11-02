<?php
include 'HTMLTag.php';

/**
 * HTMLTagContained
 * An abstract class that represents an html element that can have a content
 */
abstract class HTMLTagNonClosing extends HTMLTag {
	
	/**
	 * Content
	 * The content that is inserted between the opening and closing tags of the element
	 */
	protected $content;
	
	/**
	* Turns the element into a string.
	*
	* @return The string representation of the element. (the element as html source code)
	*/
	public function __toString()
	{
		$strVal = parent::__toString();
		$strVal .= ">\n    {$content}";
	}
	
	/**
	 * Getter for the content element.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Setter for the content of the element.
	 * 
	 * @param $content The new content of the element.
	 */
	public function setContent($content) {
		$this->content = $content;
	}
}
?>