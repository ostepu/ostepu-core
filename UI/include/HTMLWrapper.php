<?php
/**
 * @file HTMLWrapper.php
 * Contains the HTMLWrapper class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2013-2014
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013
 *
 * @todo Replace the class by a template based solution
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

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
        
        public static $anchorName=0;

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
        public function __construct($header)
        {
            $this->header = array($header);
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
        
        public function insertTop($element)
        {
            array_unshift($this->contentElements,$element);
            
            return $this;
        }
        
        /**
         * defines a form starting from the first element and ending with the
         * last element in arguments.
         *
         * @param string $target The target for the form;
         * @param Template $arguments Some Templates which have to be in a
         * form tag;
         * @return self
         */
        public function defineForm($target, $fileupload)
        {
            $arguments = func_get_args();
            array_shift($arguments);
            array_shift($arguments);

            // get position of the Templates in contentElements
            $first = array_values($arguments)[0];

            $firstkey = array_search($first, $this->contentElements, true);
            $end = end($arguments);
            $endkey = array_search($end, $this->contentElements, true);

            // define form
            if ($fileupload == false) {
                $formstart = "<form id=\"".md5(HTMLWrapper::$anchorName)."\" name=\"".md5(HTMLWrapper::$anchorName)."\" action=\"{$target}#".md5(HTMLWrapper::$anchorName)."\" method=\"POST\">";
            } else {
                $formstart = "<form id=\"".md5(HTMLWrapper::$anchorName)."\" name=\"".md5(HTMLWrapper::$anchorName)."\" action=\"{$target}#".md5(HTMLWrapper::$anchorName)."\" method=\"POST\" enctype=\"multipart/form-data\">";
            }
            HTMLWrapper::$anchorName++;
            $formend = "</form>";

            // insert formtags before and after the given range
            $this->contentElements = array_merge(
                                        array_slice($this->contentElements,
                                                    0,
                                                    $firstkey),
                                        array(0 => $formstart),
                                        array_slice($this->contentElements,
                                                    $firstkey,
                                                    $endkey-$firstkey+1),
                                        array(0 => $formend),
                                        array_slice($this->contentElements,
                                                    $endkey+1)
                                                );
        }

        public function defineHeaderForm($target, $fileupload)
        {
            $arguments = func_get_args();
            array_shift($arguments);
            array_shift($arguments);

            // define form
            if ($fileupload == false) {
                $formstart = "<form id=\"".md5(HTMLWrapper::$anchorName)."\" name=\"".md5(HTMLWrapper::$anchorName)."\" action=\"{$target}#".md5(HTMLWrapper::$anchorName)."\" method=\"POST\">";
            } else {
                $formstart = "<form id=\"".md5(HTMLWrapper::$anchorName)."\" name=\"".md5(HTMLWrapper::$anchorName)."\" action=\"{$target}#".md5(HTMLWrapper::$anchorName)."\" method=\"POST\" enctype=\"multipart/form-data\">";
            }
            HTMLWrapper::$anchorName++;
            $formend = "</form>";

            // insert formtags before and after the given range
            $this->header = array_merge(
                                        array(0 => $formstart),
                                        $this->header,
                                        array(0 => $formend)
                                                );
        }

        /**
         * A function that displays the wrapper
         */
        public function show()
        {
            $default = array('content'=>'text/html','charset'=>'utf-8','title'=>'','stylesheets'=>array(),'javascripts'=>array());
            if (!isset($this->config)) $this->config = $default;
            foreach($default as $defKey => $def){
                if (!isset($this->config[$defKey])){
                    $this->config[$defKey] = $def;
                }
            }
            
            print "<!DOCTYPE HTML>
            <html>
            <head>
                <meta http-equiv=\"content-type\" ";
                // print content-type (content-dev,charset)
                print "content=\"{$this->config['content']};";
                print " charset={$this->config['charset']}\">\n";

                // print stylesheets
                foreach ($this->config['stylesheets'] as $stylesheet) {
                    print "<link rel=\"stylesheet\" type=\"text/css\"";
                    print " href=\"$stylesheet\">\n";
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

                    foreach($this->header as $head){
                        if (is_string($head)){
                            echo $head;
                        } else {
                            $head->show();
                        }
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
     * Sets a configfile for links etc. for the head area
     *
     * @param string $configdata is the configfile;
     */
    public function set_config_file($configdata)
    {
        $fileContents = file_get_contents($configdata);
        // check if file is loaded
        if ($fileContents == false) {
            Logger::Log("Could not open file: {$configdata}",
                        LogLevel::WARNING);
        }

        $this->config = json_decode($fileContents, true);
        // check if file is valid JSON
        if ($this->config == false || is_array($this->config) == false) {
            Logger::Log("Invalid JSON in file: {$configdata}",
                        LogLevel::WARNING);
        }
    }
    
    public function add_config_file($configdata)
    {
        $fileContents = file_get_contents($configdata);
        // check if file is loaded
        if ($fileContents == false) {
            Logger::Log("Could not open file: {$configdata}",
                        LogLevel::WARNING);
        }

        $conf = json_decode($fileContents, true);
        // check if file is valid JSON
        if ($conf == false || is_array($conf) == false) {
            Logger::Log("Invalid JSON in file: {$configdata}",
                        LogLevel::WARNING);
        }
        
        if (!isset($this->config)) $this->config = array();
        
        foreach($conf as $key => $val){
            if (!isset($this->config[$key])){
                $this->config[$key] = $val;
            } else {
                if (is_array($this->config[$key])){
                    $this->config[$key] = array_merge($this->config[$key], $val);
                } else {
                    $this->config[$key] = $val;                    
                }
            }
        }
    }
}