<?php
/**
 * @file Template.php
 * Contains the Template class.
 */

include_once 'Helpers.php';
include_once '../Assistants/Logger.php';

/**
 * Template class.
 *
 * Applies templates to format data.
 * @author Florian Lücke
 */
class Template
{
    protected $template;

    protected $content;

    /**
     * Construct a new template.
     *
     * @param string $template A template string.
     * @sa Template::WithTemplateFile($fileName)
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Construct a new template.
     *
     * @param string $fileName The name of a file in which a template is stored
     */
    public function WithTemplateFile($fileName)
    {
        $templateString = file_get_contents($fileName);

        if ($templateString === FALSE) {
            Logger::Log("Could not open file: " .  $fileName, LogLevel::WARNING);
        }

        $t = new Template($templateString);
        return $t;
    }

    /**
     * Bind content to the template.
     *
     * @param array $data An associative array that contains the content for
     * the template.
     */
    public function bind(array $data)
    {
        $this->content = $data;
    }

    /**
     * Show the template to the user.
     */
    public function show()
    {
        echo $this->__toString();
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
        $success = eval("?>" . $this->template);

        // stop buffering and return the buffer's content
        $s = ob_get_contents();
        ob_end_clean();

        if ($success === FALSE) {
            Logger::Log("Parse error in template: " . $this->template, LogLevel::WARNING);
        }

        return $s;
    }
}
?>