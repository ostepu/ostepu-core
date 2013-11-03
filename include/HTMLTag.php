<?php
/**
 * An abstract class that represents an html element. 
 * Provides attributes for all global HTML attributes. Can be turned
 * into a string.
 */
abstract class HTMLTag {
	/**
	 * Attributes
	 *
	 * An associative array that holds the attributes for the represented
	 * element
	 */
	protected $attributes = array();
	
	/**
	* Getter for the accesskey attribute.
	*
	* @return The current accesskey of the element.
	*/
	public function getAccesskey() {
		return $this->attributes['accesskey'];
	}
	
	/**
	* Setter for the accesskey attribute.
	*
	* @param $accesskey The new acesskey for the html element.
	* @return $this
	*/
	public function setAccesskey($accesskey) {
		$this->attributes['accesskey'] = $accesskey;
		
		return $this;
	}
	
	/**
	* Getter for the class attribute.
	*
	* @return The current class of the element.
	*/
	public function getClass() {
		return $this->attributes['class'];
	}

	/**
	* Setter for the class attribute.
	*
	* @param $class The new class of the element.
	* @return $this
	*/
	public function setClass($class) {
		$this->attributes['class'] = $class;
		
		return $this;
	}

	/**
	* Getter for the dir attribute.
	*
	* @return The currend text direction of the element.
	*/
	public function getDir() {
		return $this->attributes['dir'];
	}

	/**
	* Setter for the dir attribute.
	* 
	* @param $dir The new text direction in the element.
	* @return $this
	*/
	public function setDir($dir) {
		$this->attributes['dir'] = $dir;
		
		return $this;
	}

	/**
	* Getter for the id attribute.
	*
	* @return The current id of the Element.
	*/
	public function getId() {
		return $this->attributes['id'];
	}

	/**
	* Setter for the id attribute.
	* 
	* @param $id The new id of the element.
	* @return $this
	*/
	public function setId($id) {
		$this->attributes['id'] = $id;
		
		return $this;
	}

	/**
	* Getter for the lang attribute.
	* 
	* @return The current laguage ot the element.
	*/
	public function getLang() {
		return $this->attributes['lang'];
	}

	/**
	* Setter for the lang attribute.
	*
	* @param $lang The new language of the element;
	* @return $this
	*/
	public function setLang($lang) {
		$this->attributes['lang'] = $lang;
		
		return $this;
	}

	/**
	* Getter for the tabindex attribute.
	*
	* @return The current tabindex of the element.
	*/
	public function getTabindex() {
		return $this->attributes['tabindex'];
	}

	/**
	* Setter for the tabindex attribute.
	* 
	* @param $tabindex The new tabindex of the element.
	* @return $this
	*/
	public function setTabindex($tabindex) {
		$this->attributes['tabindex'] = $tabindex;
		
		return $this;
	}

	/**
	* Setter for the tile attribute.
	*/
	public function getTitle() {
		return $this->attributes['title'];
	}

	/**
	* Setter for the title attribute.
	* Sets the title attribute of the html tag.
	*
	* @param $title the new title of the html tag
	* @return $this
	*/
	public function setTitle($title) {
		$this->attributes['title'] = $title;
		
		return $this;
	}
	
	/**
	 * Get the value of an attribute.
	 * 
	 * @param $attributeName The name of the attribute to return
	 * @return The current value of the attribute named $attributeName
	 */
	public function getAttribute($attributeName)
	{
		return $this->attributes[$attributeName];
	}
	
	/**
	 * Set an attribute.
	 * Set an attribute that does not have its own accessor method
	 *
	 * @param $attributeName The name of the attribute as in name="value"
	 * @param $attributeValue The value of the atribute as in name="value"
	 */
	public function setAttribute($attributeName, $attributeValue)
	{
		$this->attributes[$attributeName] = $attributeValue;
		
		return $this;
	}
	
	/**
	* Turns the element into a string.
	*
	* @return The string representation of the element. (the element as html source code)
	*/
	public function __toString() {
		$strVal = "";
			
		foreach ($this->attributes as $key => $value) {
			$strVal .= " {$key}=\"{$value}\"";
		}
		
		return $strVal;
	}
}
?>