<?php 
include_once 'include/Header/Header.php';
    /**
    * Wraps the header and the contents in a default HTML 
    */
    class HTMLWrapper
    {
        private $header;
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
         * 
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

                    foreach ($this->contentElements as $contentElement) {
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