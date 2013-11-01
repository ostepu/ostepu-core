<?php
    class Header {
	private $title;
	private $extraInfo;
	private $username;
	private $userid;

	function __construct($title, $extraInfo, $username, $userid) {
	    $this->title = $title;
	    $this->extraInfo = $extraInfo;
	    $this->username = $username;
	    $this->userid = $userid;
	}
	
	public function getTitle() {
	    return $this->title;
	}

	public function show() {
	    echo '<div id="header">';
	    echo '    <div class="session-info">';
	    echo '        <div class="session-title">' . $this->title . '</div>';
	    echo '        <a class="change-session" href="#">Veranstaltung wechseln</a>';
	    echo '    </div>';
	    echo '    <div class="session-points">';
	    echo '        <div class="points-huge">75%</div>';
	    echo '        <div class="total-points">Gesamtpunkte</div>';
	    echo '    </div>';
	    echo '    <div class="user-info">';
	    echo '        <div class="user-name">' . $this->username . '</div>';
	    echo '        <div class="user-id">' . $this->userid . '</div>';
	    echo '        <a class="log-out" href="#">Ausloggen</a>';
	    echo '    </div>';
	    echo '</div>';
	}
    }
?>
