<?php
//Überprüfe Nutzerstatus

//include_once dirname(__FILE__) . '/../include/Boilerplate.php';
include_once dirname(__FILE__) . '/../include/Authentication.php';
include_once dirname(__FILE__) . '/../include/StudIPAuthentication.php';
include_once dirname(__FILE__) . '/../../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';

$auth = new Authentication();
$StudIPauth = new StudIPAuthentication();

$invalidLogin = Authentication::checkLogin() == false;

//Sende Antwort Header

header("Content-Type: application/json");

//Sende Daten

if ($invalidLogin) {
	$response = array(
		"success" => false,
		"error" => "invalidLogin"
	);
	//echo json_encode($response, JSON_PRETTY_PRINT);
	echo json_encode($response);
	return;
}
if (!isset($_GET["mode"])) {
	$response = array(
		"success" => false,
		"error" => "noMethodGiven"
	);
	echo json_encode($response, JSON_PRETTY_PRINT);
	return;
}
if ($_GET["mode"] == "ping") {
	$response = array(
		"success" => true,
		"ping" => "pong"
	);
	//echo json_encode($response, JSON_PRETTY_PRINT);
	json_encode($response);
	return;
}
if ($_GET["mode"] == "test") {
	$response = array(
		"success" => true,
		"result" => "It work's! :)"
	);
	echo json_encode($response, JSON_PRETTY_PRINT);
	return;
}


else {
	$response = array(
		"success" => false,
		"error" => "notSupportedMethod",
		"method" => $_GET["mode"]
	);
	echo json_encode($response, JSON_PRETTY_PRINT);
	return;
}