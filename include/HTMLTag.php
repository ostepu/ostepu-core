<?php
/**
 * HTMLTag class
 * abstract class that represents an html element
 */
abstract class HTMLTag {
	/**
	 * Specifies a shortcut key to activate/focus the element.
	 */
	protected $accesskey;
	
	/**
	 * Specifies one or more classnames for the element (refers to a class in a style sheet).
	 */
	protected $class;
	
	/**
	 * Specifies the text direction for the content in the element
	 */
	protected $dir;
	
	/**
	 * Specifies a unique id for the element.
	 */
	protected $id;
	
	/**
	 * Specifies the language of the element's content.
	 */
	protected $lang;
	
	
	/**
	 * Specifies the tabbing order of an element.
	 */
	protected $tabindex;
	
	/**
	 * Specifies extra information about the element.
	 */
	protected $title; 
	
	
	/**
	 * Getter for the accesskey attribute.
	 *
	 * @return The current accesskey of the element.
	 */
	public function getAccesskey() {
		return $this->accesskey;
	}
	
	/**
	 * Setter for the accesskey attribute.
	 *
	 * @param $accesskey The new acesskey for the html element.
	 */
	public function setAccesskey($accesskey) {
		$this->accesskey = $accesskey;
	}
	
	/**
	 * Getter for the class attribute.
	 *
	 * @return The current class of the element.
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * Setter for the class attribute.
	 *
	 * @param $class The new class of the element.
	 */
	public function setClass($class) {
		$this->class = $class;
	}

	/**
	 * Getter for the dir attribute.
	 *
	 * @return The currend text direction of the element.
	 */
	public function getDir() {
		return $this->dir;
	}

	/**
	 * Setter for the dir attribute.
	 * 
	 * @param $dir The new text direction in the element.
	 */
	public function setDir($dir) {
		$this->dir = $dir;
	}

	/**
	 * Getter for the id attribute.
	 *
	 * @return The current id of the Element.
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Setter for the id attribute.
	 * 
	 * @param $id The new id of the element.
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Getter for the lang attribute.
	 * 
	 * @return The current laguage ot the element.
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * Setter for the lang attribute.
	 *
	 * @param $lang The new language of the element;
	 */
	public function setLang($lang) {
		$this->lang = $lang;
	}

	/**
	 * Getter for the tabindex attribute.
	 *
	 * @return The current tabindex of the element.
	 */
	public function getTabindex() {
		return $this->tabindex;
	}

	/**
	 * Setter for the tabindex attribute.
	 * 
	 * @param $tabindex The new tabindex of the element.
	 */
	public function setTabindex($tabindex) {
		$this->tabindex = $tabindex;
	}

	/**
	 * Setter for the tile attribute.
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Setter for the title attribute.
	 * Sets the title attribute of the html tag.
	 *
	 * @param $title the new title of the html tag
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * A method to output the tag.
	 */
	public function print() {
		if ($this->accesskey != NULL && $this->accesskey != "") {
			echo "accesskey=\"" . $this->accesskey . "\" ";
		}
		
		if ($this->class != NULL && $this->class != "") {
			echo "class=\"" . $this->class . "\" ";
		}
		
		if ($this->dir != NULL && $this->dir != "") {
			echo "dir=\"" . $this->dir . "\" ";
		}
		
		if ($this->id != NULL && $this->id != "") {
			echo "id=\"" . $this->id . "\" ";
		}
		
		if ($this->lang != NULL && $this->lang != "") {
			echo "lang=\"" . $this->lang . "\" ";
		}
	}
}
?>