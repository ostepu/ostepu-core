<?php

include_once 'include/Helpers.php';

class Header {
    private $title;
    private $extraInfo;
    private $username;
    private $userid;
    private $points;
    private $backURL = "#";
    private $backTitle = "Veranstaltung wechseln";

    function __construct($title,$extraInfo, $username,
                         $userid, $points = 0) {
        $this->title = $title;
        $this->extraInfo = $extraInfo;
        $this->username = $username;
        $this->userid = $userid;
        $this->points = $points;
    }

    public function show() {
        $prototypeHeader = file_get_contents('include/Header/Header.template.html');

        $prototypeHeader = str_replace("%title%",
                                       $this->title,
                                       $prototypeHeader);

        $prototypeHeader = str_replace("%username%",
                                       $this->username,
                                       $prototypeHeader);

        $prototypeHeader = str_replace("%points%",
                                       $this->points,
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
     * @return mixed
     */
    public function getBackURL() {
        return $this->backURL;
    }
    
    /**
     * Sets the value of backURL.
     *
     * @param mixed $backURL the back u r l
     *
     * @return self
     */
    public function setBackURL($backURL) {
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
}
?>
