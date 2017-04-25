<?php
//Überprüfe Nutzerstatus

//include_once dirname(__FILE__) . '/../include/Boilerplate.php';
include_once dirname(__FILE__) . '/../include/Authentication.php';
include_once dirname(__FILE__) . '/../../Assistants/LArraySorter.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';

$auth = new Authentication();

$invalidLogin = Authentication::checkLogin() == false;

//Sende Antwort Header

header("Content-Type: application/json");

//Sende Daten

if ($invalidLogin) {
	$response = array(
		"success" => false,
		"error" => "invalidLogin",
		"hint" => "login to access api"
	);
	//echo json_encode($response, JSON_PRETTY_PRINT);
	echo json_encode($response);
	return;
}
if (!isset($_GET["mode"])) {
	$response = array(
		"success" => false,
		"error" => "noMethodGiven",
		"hint" => 'use URL .../api/$api-mode/ to access api'
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
	echo json_encode($response, JSON_PRETTY_PRINT);
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
if ($_GET["mode"] == "upload") {
	$response = null;
	if (!isset($_POST["tasks"])) {
		$response = array(
			"success" => false,
			"error" => "noTasksGiven",
			"hint" => 'add POST variable $tasks with some data'
		);
	}
	elseif (!isset($_GET["cid"]) || !isset($_GET["sid"])) {
		$response = array(
			"success" => false,
			"error" => "noCourseOrSheetSetted",
			"hint" => 'GET variables $cid and/or $sid not setted'
		);
	}
	else {
		$cid = $_GET["cid"];
		$sid = $_GET["sid"];
		$uid = $_SESSION["UID"];
		$URI = "{$getSiteURI}/markingtool/course/{$cid}/sheet/{$sid}";
		$dbData = json_decode(http_get($URI, true), true);
		$dbData = $dbData["groups"];
		
		$response = array(
			"success" => true,
			"smalStates" => array(),
			"files" => array()//,			//Eine Liste der neuen Dateiinfos, die hochgeladen wurden
			//"test" => $dbData
		);
		foreach ($_POST["tasks"] as $task) {
			$task  = json_decode($task, true);
			//Schritt 1 - Hole das aktuelle Objekt
			for ($i = 0; $i<count($dbData); ++$i)
				if ($dbData[$i]["leaderId"] == $task["leaderId"]) {
					$exercise = &$dbData[$i]["exercises"];
					for ($i = 0; $i<count($exercise); ++$i) 
						if ($exercise[$i]["id"] == $task["exerciseId"]) {
							$exercise = $exercise[$i];
							break;
						}
					break;
				}
			if (!isset($exercise) || !isset($exercise["id"])) {
				$response["success"] = false;
				$response["smalStates"][] = array(
					"task" => $task,
					"error" => "exerciseNotFound"
				);
				continue;
			}
			//Schritt 2 - Vergleiche Aktualität der Daten
			$newData = array();
			$valid = true;
			$sub = isset($exercise["submission"]);
			$mark = $sub && isset($exercise["submission"]["marking"]);
			if (isset($task["points_old"]) && $mark && floatval($task["points_old"]) != floatval($exercise["submission"]["marking"]["points"])) {
				$newData["points"] = floatval($exercise["submission"]["marking"]["points"]);
				$valid = false;
			}
			if (isset($task["accepted_old"]) && $sub && boolval($task["accepted_old"]) != (intval($exercise["submission"]["accepted"]) != 0)) {
				$newData["accepted"] = intval($exercise["submission"]["accepted"]) != 0;
				$valid = false;
			}
			if (isset($task["status_old"]) && $mark && intval($task["status_old"]) != intval($exercise["submission"]["marking"]["status"])) {
				$newData["status"] = intval($exercise["submission"]["marking"]["status"]);
				$valid = false;
			}
			if (isset($task["tutorComment_old"]) && $mark && strval($task["tutorComment_old"]) != strval($exercise["submission"]["marking"]["tutorComment"])) {
				$newData["tutorComment"] = strval($exercise["submission"]["marking"]["tutorComment"]);
				$valid = false;
			}
			if (isset($task["studentComment_old"]) && $sub && strval($task["studentComment_old"]) != strval($exercise["submission"]["comment"])) {
				$newData["studentComment"] = strval($exercise["submission"]["comment"]);
				$valid = false;
			}
			if (isset($task["userFile_old"]) && $sub && isset($exercise["submission"]["file"]) && intval($task["userFile_old"]) != intval($exercise["submission"]["file"]["fileId"])) {
				$newData["userFile"] = array(
					"id" => $exercise["submission"]["file"]["fileId"],
					"name" => $exercise["submission"]["file"]["displayName"],
					"uploaded" => $exercise["submission"]["file"]["timeStamp"],
					"url" => generateDownloadURL($exercise['submission']['file'])
				);
				$valid = false;
			}
			if (isset($task["tutorFile_old"]) && $mark && isset($exercise["submission"]["marking"]["file"]) && intval($task["tutorFile_old"]) != intval($exercise["submission"]["marking"]["file"]["fileId"])) {
				$newData["tutorFile"] = array(
					"id" => $exercise["submission"]["marking"]["file"]["fileId"],
					"name" => $exercise["submission"]["marking"]["file"]["displayName"],
					"uploaded" => $exercise["submission"]["marking"]["file"]["timeStamp"],
					"url" => generateDownloadURL($exercise['submission']["marking"]['file'])
				);
				$valid = false;
			}
			//Schritt 3 - Sende existierende Unstimmigkeiten zurück
			if (!$valid) {
				$response["success"] = false;
				$response["error"] = "outdatetData";
				$response["smalStates"][] = array(
					"task" => $task,
					"newData" => $newData
				);
				continue;
			}
			//Schritt 4 - Speichere neuen Zustand
			//Schritt 4.1 - Speichere Daten zur Submission (Einsendung)
			//Schritt 4.2 - Speichere Daten zum Marking (Korrektur)
		}
	}
	// if (!$response["success"] && !isset($response["hint"]))
		// $response["hint"] = 'look in $smalStates for more details';
	echo json_encode($response, JSON_PRETTY_PRINT);
	return;
}


else {
	$response = array(
		"success" => false,
		"error" => "notSupportedMethod",
		"method" => $_GET["mode"],
		"hint" => "this method is not supported by this api"
	);
	echo json_encode($response, JSON_PRETTY_PRINT);
	return;
}