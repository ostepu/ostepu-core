<?php

include_once 'include/Helpers.php';

class Header
{
    private $title;
    private $extraInfo;
    private $username;
    private $userid;
    private $points;
    private $backURL = "#";
    private $backTitle = "Veranstaltung wechseln";

    function __construct($title,$extraInfo, $username,
                         $userid)
    {
        $this->title = $title;
        $this->extraInfo = $extraInfo;
        $this->username = $username;
        $this->userid = $userid;
    }

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
            $extraInfoTemplate = file_get_contents('include/Header/Extra-Info-Student.template.html');

            $extraInfoTemplate = str_replace("%points%",
                                           $this->points,
                                           $extraInfoTemplate);
            $prototypeHeader = str_replace('%extraInfo%',
                                           $extraInfoTemplate,
                                           $prototypeHeader);
        }

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
     * @return mixed
     */
    public function getBackURL()
    {
        return $this->backURL;
    }
    
    /**
     * Sets the value of backURL.
     *
     * @param mixed $backURL the back u r l
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
     * @return mixed
     */
    public function getBackTitle()
    {
        return $this->backTitle;
    }
    
    /**
     * Sets the value of backTitle.
     *
     * @param mixed $backTitle the back title
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
     * @param mixed $points the points
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
