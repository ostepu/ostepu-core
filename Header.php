<?php
include 'include/HTML.php';
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
		/**
		 * generate session info
		 */
		$sessionTitle = new Div();
		$sessionTitle->setClass("session-title")
					 ->setContent($this->title);
		
		$changeSession = new A();
		$changeSession->setClass("change-session")
					  ->setHref("#")
					  ->setContent("Veranstaltung wechseln");
		
		$sessionInfo = new Div();
		$sessionInfo->setClass("session-info")
					->setContent($sessionTitle)
					->addContent($changeSession);
		
		/**
		 * generate session points
		 */
		$pointsHuge = new Div();
		$pointsHuge->setClass("points-huge")
				   ->setContent("75%");
		
		$totalPoints = new Div();
		$totalPoints->setClass("total-points")
					->setContent("Gesamtpunkte");
		
		$sessionPoints = new Div();
		$sessionPoints->setClass("session-points")
					  ->setContent($pointsHuge)
					  ->addContent($totalPoints);
		
		/**
		 * generate user-info
		 */
		$userName = new Div();
		$userName->setClass("user-name")
				 ->setContent($this->username);
		
		$userId = new Div();
		$userId->setClass("user-id")
			   ->setContent($this->userid);
		
		$logOut = new A();
		$logOut->setClass("log-out")
			   ->setHref("#")
			   ->setContent("Ausloggen");
		
		$userInfo = new Div();
		$userInfo->setClass("user-info")
				 ->addContent($userName)
				 ->addContent($userId)
				 ->addContent($logOut);
		
		/**
		 * generate header
		 */
		$header = new Div();
		$header->setContent($sessionInfo)
			   ->addContent($sessionPoints)
			   ->addContent($userInfo)
			   ->setId("header");
		
		print $header;
	}
}
?>
