<?php
/**
 * @file HTMLWrapper.php
 * Contains the HTMLWrapper class
 */

include_once 'include/Header/Header.php';
include_once '../../Assistants/Logger.php';

    /**
    * Wraps the header and the contents in a default HTML
    */
    class HTMLWrapper
    {
        /**
         * @var Header The element that should be displayed
         * as the page header
         */
        private $header;

        /**
         * @var array An array of elements that make up the pages contents
         */
        private $contentElements;

        /**
         * @var string A navigation bar that should be inserted between
         * header and body
         */
        private $navigationElement;

        /**
         * @var array defines all links in the document head
         */
        private $config;

        /**
         * The default contructor.
         *
         * @param Header $header The element that should be displayed
         * as the page header.
         * @param mixed ... Page elements that should be displayed as
         * the page content
         */
        public function __construct(Header $header)
        {
            $this->header = $header;
            $arguments = func_get_args();
            array_shift($arguments);
            $this->contentElements = $arguments;
        }

        /**
         * insert an element into te content area.
         *
         * @param mixed $element The element that should be inserted;
         * @return self
         */
        public function insert($element)
        {
            $this->contentElements[] = $element;

            return $this;
        }

        /**
         * A function that displays the wrapper
         */
        public function show()
        {
            print "<!DOCTYPE HTML>
            <html>
            <head>
                <meta http-equiv=\"content-type\" ";
                // print content-type (content-dev,charset)
                print "content=\"{$this->config['content']}; charset={$this->config['charset']}\">\n";
                // print stylesheets
                foreach ($this->config['stylesheets'] as $stylesheet) {
                    print "<link rel=\"stylesheet\" type=\"text/css\" href=\"$stylesheet\">\n";
                }
                // print javascripts
                foreach ($this->config['javascripts'] as $javascript) {
                    print "<script src=\"$javascript\"></script>\n";
                }
                // print title
                print "<title>{$this->config['title']}</title>
            </head>
            <body>
                <div id=\"body-wrapper\" class=\"body-wrapper\">";

                    $this->header->show();

                    if (!is_null($this->navigationElement)) {
                        print $this->navigationElement;
                    }

                    print '<div id="content-wrapper" class="content-wrapper">';

                    // try to print all the elements in contentElements
                    foreach ($this->contentElements as $contentElement) {
                        // check check if we can somehow print the content
                        if (method_exists($contentElement, 'show')) {
                            $contentElement->show();
                        } elseif (method_exists($contentElement, '__toString')) {
                            print $contentElement;
                        } elseif (is_string($contentElement)) {
                            print $contentElement;
                        }
                    }

                    print '</div> <!-- end: content-element -->
                </div>
            </body>
            </html>';
        }

    /**
     * Sets the value of navigationElement.
     *
     * @param $navigationElement the navigation element
     *
     * @return self
     */
    public function setNavigationElement($navigationElement)
    {
        $this->navigationElement = $navigationElement;

        return $this;
    }

    /**
     * Sets a configfile for links etc. for the head area
     *
     * @param string $configdata is the configfile;
     */
    public function set_config_file($configdata)
    {
        $fileContents = file_get_contents($configdata);
        // check if file is loaded
        if ($fileContents == false) {
            Logger::Log("Could not open file: {$configdata}", LogLevel::WARNING);
        }
        $this->config = json_decode($fileContents, true);
        // check if file is valid JSON
        if ($this->config== false || !is_array($this->config)) {
            Logger::Log("Invalid JSON in file: {$configdata}", LogLevel::WARNING);
        }
    }
}
    ?>