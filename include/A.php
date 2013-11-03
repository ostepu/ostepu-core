<?php
require_once 'HTMLTagNonClosing.php';

/**
 * @file A.php
 * Contains the A class
 */

/**
* A class that represents an <a> element
*/
class A extends HTMLTagNonClosing
{
	/**
	 * Add content to an element.
	 * The content that is contained by the element after this method is
	 * called, is the content that was in the element before the method was
	 * called, plus the content that was passed as an argument.
	 *
	 * @param $content The content that the element should contain
	 * @return $this
	 * @see HTMLTagNonClosing::addContent()
	 */
	public function addContent($content)
	{
		/**
		 * According to the html 4.0 standart an a element may not have an
		 * a element as its content
		 */
		if (($content instanceof A) == false) {
			parent::addContent($content);
		}
	}
	
	
	/**
	 * Setter for the content of the element.
	 * 
	 * @param $content The new content of the element.
	 * @see HTMLTagNonClosing::setContent()
	 */
	public function setContent($content)
	{
		/**
		 * According to the html 4.0 standart an a element may not have an
		 * a element as its content
		 */
		if (($content instanceof A) == false) {
			parent::setContent($content);
		}
	}
	
	/**
	 * Set the URL of the page the link goes to
	 *
	 * @param $href The URL of the page the link goes to.
	 * @return $this
	 */
	public function setHref($href)
	{
		$this->attributes['href'] = $href;
		
		return $this;
	}
	
	/**
	 * Get the URL of the page the link goes to
	 *
	 * @return The URL of the page the link goes to.
	 */
	public function getHref()
	{
		return $this->attributes['href'];
	}
}


?>