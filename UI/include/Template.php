<?php
/**
 * @file Template.php
 * Contains the Template class.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013
 */

include_once ( dirname(__FILE__) . '/Helpers.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

/**
 * Template class.
 *
 * Applies templates to format data.
 */
class Template
{
    protected $template;
    public $templateFile;

    protected $content;

    /**
     * Construct a new template.
     *
     * @param string $template A template string.
     * @see Template::WithTemplateFile($fileName)
     */
    public function __construct($template)
    {
        $this->content = array();
        $this->template = $template;
    }

    /**
     * Construct a new template.
     *
     * @param string $fileName The name of a file in which a template is stored
     */
    public static function WithTemplateFile($fileName)
    {
        //$templateString = file_get_contents($fileName);
        $t = new Template('');
        $t->templateFile = $fileName;

        /*if ($templateString === FALSE) {
            Logger::Log("Could not open file: " .  $fileName, LogLevel::WARNING);
        }*/

        //$t = new Template($templateString);
        return $t;
    }

    /**
     * Bind content to the template.
     *
     * @param array $data An associative array that contains the content for
     * the template.
     */
    public function bind($data)
    {
        if (!isset($data)) {
            return;
        }

        $this->content = $this->content + $data;
    }

    /**
     * Show the template to the user.
     */
    public function show()
    {
        print $this->__toString();
    }

    /**
     * Return the the template as a string.
     *
     * Content is inserted into the template in this function
     */
    public function __toString()
    {
        // make the content available as if variables with the names of its
        // attributes had been declared
        $extractedCount = extract($this->content);

        if ($extractedCount != count($this->content)) {
            Logger::Log("Unable to extract all content.", LogLevel::WARNING);
        }

        // buffer the output
        ob_start();

        // evaluate the template as a php script
        // a closing php tag is needed before the template, so HTML can be
        // used in the template.
        $success=true;
        /*$success = eval("?>" . $this->template);*/
        include($this->templateFile);

        // stop buffering and return the buffer's content
        $s = ob_get_contents();
        ob_end_clean();

        if ($success === FALSE) {
            Logger::Log("Parse error in template: " . $this->template, LogLevel::WARNING);
        }

        return $s;
    }
}