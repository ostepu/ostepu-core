<?php

include 'include/Helpers.php';

class Header {
    private $title;
    private $extraInfo;
    private $username;
    private $userid;
    private $points;

    function __construct($title,$extraInfo, $username,
                         $userid, $points = 0) {
        $this->title = $title;
        $this->extraInfo = $extraInfo;
        $this->username = $username;
        $this->userid = $userid;
        $this->points = $points;
    }

    public function show() {
        $prototypeHeader = getIncludeContents('Header.template.html');

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

        print $prototypeHeader;
    }
}
?>
