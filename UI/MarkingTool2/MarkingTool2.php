<?php
include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * ???
 */
class UIMarkingTool2
{
    private $_component = null;
    public function __construct( )
    {        
        $component = new Model('', dirname(__FILE__), $this, false, false);
        $this->_component=$component;
        $component->run();
    }
    
    private function checkLogin()
    {
        $auth = new Authentication();
        $invalidLogin = Authentication::checkLogin() == false;
        if ($invalidLogin) {
            $response = array(
                "success" => false,
                "error" => "invalidLogin",
                "hint" => "login to access api"
            );
            return $response;
        }
        return true;
    }

    public function getPing( $callName, $input, $params = array() )
    {
        $loggedIn = $this->checkLogin();
        if ($loggedIn !== true){
            return Model::isProblem(json_encode($loggedIn, JSON_PRETTY_PRINT));
        }

        $response = array(
            "success" => true,
            "ping" => "pong"
        );
        return Model::isOk(json_encode($response, JSON_PRETTY_PRINT));
    }
    
    public function getTest( $callName, $input, $params = array() )
    {
        $loggedIn = $this->checkLogin();
        if ($loggedIn !== true){
            return Model::isProblem(json_encode($loggedIn, JSON_PRETTY_PRINT));
        }

        $response = array(
            "success" => true,
            "result" => "It work's! :)"
        );
        return Model::isOk(json_encode($response, JSON_PRETTY_PRINT));
    }
    
    public function getUnknownMode( $callName, $input, $params = array() )
    {
        $loggedIn = $this->checkLogin(); // ist hier wirklich ein eingeloggter Nutzer nötig?
        if ($loggedIn !== true){
            return Model::isProblem(json_encode($loggedIn, JSON_PRETTY_PRINT));
        }

        if ($params['mode'] == ''){
            $response = array(
                "success" => false,
                "error" => "noMethodGiven", // es wäre sicher besser hier von "Modus" zu sprechen
                "hint" => 'use URL .../api/$api-mode/ to access api'
            );
        } else {
            $response = array(
                "success" => false,
                "error" => "notSupportedMethod",
                "method" => $params['mode'], // sollte man die Methode hier wirklich zurück geben?
                "hint" => "this method is not supported by this api"
            );
        }
        return Model::isOk(json_encode($response, JSON_PRETTY_PRINT));
    }
    
    public function getUnknownApiCall( $callName, $input, $params = array() )
    {
        $loggedIn = $this->checkLogin(); // ist hier wirklich ein eingeloggter Nutzer nötig?
        if ($loggedIn !== true){
            return Model::isProblem(json_encode($loggedIn, JSON_PRETTY_PRINT));
        }
        
        $response = array(
            "success" => false,
            "error" => "notSupportedPath",
            "path" => implode('/',$params['path']), // sollte man die Methode hier wirklich zurück geben?
            "hint" => "this path is not supported by this api"
        );
        return Model::isOk(json_encode($response, JSON_PRETTY_PRINT));
    }
    
    public function postUpload( $callName, $input, $params = array() )
    {
        $response = null;
        parse_str($input, $postData); // parst die eingehenden Formulardaten nach $postData
        
        $cid = $params['cid'];
        $sid = $params['sid'];
        
        /* sid und cid existieren hier garantiert
        elseif (!isset($_GET["cid"]) || !isset($_GET["sid"])) {
            $response = array(
                "success" => false,
                "error" => "noCourseOrSheetSetted",
                "hint" => 'GET variables $cid and/or $sid not setted'
            );
        }*/
        
        if (!isset($postData["tasks"])) {
            $response = array(
                "success" => false,
                "error" => "noTasksGiven",
                "hint" => 'add POST variable $tasks with some data'
            );
        }
        else {
            $uid = $_SESSION["UID"];
            
            $positive = function($input) {
                return $input;
            };
            
            $negative = function(){
                return false;
            };

            $rawData = $this->_component->call('getMarkingToolData', array('cid'=>$cid, 'sid'=>$sid), '', 200, $positive, array(), $false, array());
            
            if ($rawData === false){
                // der Aufruf war fehlerhaft
            }
            
            $dbData = json_decode($rawData, true);
            $dbData = $dbData["groups"];
            
            $response = array(
                "success" => true,
                "smalStates" => array(),
                "files" => array()//,			//Eine Liste der neuen Dateiinfos, die hochgeladen wurden
                //"test" => $dbData
            );
            foreach ($postData["tasks"] as $task) {
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
        return Model::isOk(json_encode($response, JSON_PRETTY_PRINT));
    }
}
