<?php
/**
 * @file Header.php
 * Contains the Header class.
 */

include_once 'include/Helpers.php';

/**
 * @class Header
 * Represents the page header
 *
 * @todo Make the class more convenient to use, e.g. make it easier to display
 * different data
 */
class Header
{
    /**
     * Strings that should be displayed in the header
     */
    private $title;
    private $extraInfo;
    private $username;
    private $userid;
    private $points;
    private $backURL = "index.php";
    private $backTitle = "Veranstaltung wechseln";

    /**
     * Contruct a page header.
     * Place
     *
     * @param string $title The title that should be displayed in the header. (left side)
     * @param string $extraInfo Additional info that should be displayed in the header
     * @param string $username The name of the user that sees the page
     * @param string $userid The user's user-ID
     */
    function __construct($title, $extraInfo, $username,
                         $userid)
    {
        $this->title = $title;
        $this->extraInfo = $extraInfo;
        $this->username = $username;
        $this->userid = $userid;
    }

    /**
     * Print the header to the page
     */
    public function show()
    {
        $prototypeHeader = file_get_contents('include/Header/Header.template.html');

        $prototypeHeader = str_replace("%title%",
                                       $this->title,
                                       $prototypeHeader);

        $prototypeHeader = str_replace("%username%",
                                       $this->username,
                                       $prototypeHeader);

        if (!is_null($this->points)) {
            /**
             * @todo This should be moved outside the header class
             */
            $extraInfoTemplate = file_get_contents('include/Header/Extra-Info-Student.template.html');

            $extraInfoTemplate = str_replace("%points%",
                                           $this->points,
                                           $extraInfoTemplate);
        } else {
            $extraInfoTemplate = "";
        }

        $prototypeHeader = str_replace('%extraInfo%',
                                       $extraInfoTemplate,
                                       $prototypeHeader);

        $prototypeHeader = str_replace("%userid%",
                                       $this->userid,
                                       $prototypeHeader);

        $prototypeHeader = str_replace("%backURL%",
                                       $this->backURL,
                                       $prototypeHeader);

        $prototypeHeader = str_replace("%backTitle%",
                                       $this->backTitle,
                                       $prototypeHeader);


        print $prototypeHeader;
    }

    /**
     * Gets the value of backURL.
     *
     * @return string
     */
    public function getBackURL()
    {
        return $this->backURL;
    }

    /**
     * Sets the value of backURL.
     *
     * @param string $backURL the back u r l
     *
     * @return self
     */
    public function setBackURL($backURL)
    {
        $this->backURL = $backURL;

        return $this;
    }

    /**
     * Gets the value of backTitle.
     *
     * @return string
     */
    public function getBackTitle()
    {
        return $this->backTitle;
    }

    /**
     * Sets the value of backTitle.
     *
     * @param string $backTitle the back title
     *
     * @return self
     */
    public function setBackTitle($backTitle)
    {
        $this->backTitle = $backTitle;

        return $this;
    }

    /**
     * Sets the value of points.
     *
     * @param string $points the points
     *
     * @return self
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }
}
?>
