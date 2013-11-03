<?php
include 'HTMLTag.php';
//include_once 'Helpers.php';

/**
 * @file HTMLTagNonClosing.php
 * Contains the HTMLTagNonClosing class
 */

/**
 * An abstract class that represents an html element that can have a content
 */
abstract class HTMLTagNonClosing extends HTMLTag {
	
	/**
	 * Content
	 * The content that is inserted between the opening and closing tags of the element
	 */
	protected $content = array();
	
	/**
	* Turns the element into a string.
	*
	* @return The string representation of the element. (the element as html source code)
	*/
	public function __toString() {
		$strVal = parent::__toString();
		$strVal .= ">\n    " . $this->getContent();
		
		return $strVal;
	}
	
	/**
	 * Getter for the content of the element.
	 */
	public function getContent() {
		return join("\n", $this->content);
	}
	
	/**
	 * Setter for the content of the element.
	 * 
	 * @param $content The new content of the element.
	 */
	public function setContent($content) {
		$this->content = array();
		$this->content[] = $content;
		
		return $this;
	}
	
	/**
	 * Add content to an element.
	 * The content that is contained by the element after this method is
	 * called, is the content that was in the element before the method was
	 * called, plus the content that was passed as an argument.
	 *
	 * @param $content The content that the element should contain
	 * @return $this
	 */
	public function addContent($content) {
		$this->content[] = $content;
		
		return $this;
	}
	
	/**
	 * Remove content from the element.
	 *
	 * @param $content The content that should be removed from the element
	 * @return $this
	 */
	public function removeContent($content)
	{
		$contents = $this->content;
		
		// remove $content from $contents
		$contents = unsetValue($contents, $content);
		$this->content = $contents;
		
		return $this;
	}
}
?>