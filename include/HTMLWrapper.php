<?php 
/**
 * @file HTMLWrapper.php
 * Contains the HTMLWrapper class
 */

include_once 'include/Header/Header.php';
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
                print "content=\"text/html; charset=utf-8\">
                <link rel=\"stylesheet\" type=\"text/css\" ";
                print "href=\"CSSReset.css\"> 
                <link rel=\"stylesheet\" type=\"text/css\" href=\"Uebungsplattform.css\"> 
                <title>Übungsplattform</title>
            </head>
            <body>
                <div id=\"body-wrapper\" class=\"body-wrapper\">";

                    $this->header->show();

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

                    print '</div> <!-- end: content-wrapper -->
                </div>
            </body>
            </html>';
        }
    }
    ?>