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
    
    public function getButton( $callName, $input, $params = array() )
    {
        $data = json_decode($input, true);
        $res = array('content'=>'');
        if (isset($data['cid'])){
        $content =  '<li>
                        <a class="text-button" href="'.$data['externalURI'].'/UI/MarkingTool2/page/markingtool2/course/'.$params['cid'].'/exercisesheet/'.$params['sid'].'">
                            Korrekturassistent (Entwicklung)
                        </a>
                    </li>';
            $res = array('content'=>$content);
        }
        return Model::isOk(json_encode($res)); 
    }
    
    public function getMarkingTool2Page( $callName, $input, $params = array() )
    {   
        include_once ( dirname(__FILE__) . '/../include/Boilerplate.php' );
     
        $cid = $params['cid'];
        $sid = $params['sid'];

        ob_start();
        
        global $globalUserData;
        global $serverURI;
        global $externalURI;
        global $getSiteURI;
        
        $uid = null;
        if (isset($globalUserData['id'])){
            $uid = $globalUserData['id'];
        }
        
        //Überprüft ob mindestens ein Tutor diese Seite abruft.
        Authentication::checkRights(PRIVILEGE_LEVEL::TUTOR, $cid, $uid, $globalUserData);
        
        // turorid und statusid gibt es hier natürlich nicht
        $URI = $getSiteURI . "/markingtool/user/{$uid}/course/{$cid}/exercisesheet/{$sid}";
        if (isset($tutorID)) {
            $URI .= "/tutor/{$tutorID}";
        }
        if (isset($statusID) && $statusID != 'all' && $statusID != 'notAccepted') {
            $URI .= "/status/{$statusID}";
        }
        $markingTool_data = http_get($URI, true);
        $markingTool_data = json_decode($markingTool_data, true);
        $user_course_data = $markingTool_data['user'];
        //Gibt den HTML Kopf aus, der dann alles nachlädt
        // $menu = MakeNavigationElement($user_course_data,
        //                               PRIVILEGE_LEVEL::TUTOR,true);
        // $h = Template::WithTemplateFile('include/Header/Header.template.html');
        // $h->bind($user_course_data);
        // $h->bind(array('name' => $user_course_data['courses'][0]['course']['name'],
                       // 'navigationElement' => $menu));
                       
        $c = Template::WithTemplateFile('templates/MarkingTool2.template.html');
        $c->bind($markingTool_data);
        $c->bind(array(
            "restricted" => Authentication::checkRight(PRIVILEGE_LEVEL::LECTURER, $cid, $uid, $globalUserData),
            "userLevel" => $userLevel = Authentication::getUserLevel($cid, $uid, $globalUserData),
            "backUrl" => "$externalURI/UI/".PRIVILEGE_LEVEL::$SITES[$userLevel]."?cid=$cid",
            "uid" => $uid
        ));
		$c->bind($params);
        $w = new HTMLWrapper(/*$h, */$c);
        $w->set_config_file('config_marking_tool2.json');
        if (isset($maintenanceMode) && $maintenanceMode === '1')
            $w->add_config_file('../include/configs/config_maintenanceMode.json');
        $w->show();
        //echo "<pre>"; echo json_encode($markingTool_data, JSON_PRETTY_PRINT); echo "</pre>";
        
        $content = ob_get_clean();
        
        return Model::isOk($content);
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
		global $_SESSION;
		
        $response = null;
        parse_str($input, $postData); // parst die eingehenden Formulardaten nach $postData
        
        $cid = $params['cid'];
        $sid = $params['sid'];
        
		header("Content-Type: text/json"); //Damit jQuery das automatisch parst
		
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

            $rawData = $this->_component->call('getMarkingToolData', array('cid'=>$cid, 'sid'=>$sid), '', 200, $positive, array(), $negative, array());
            
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
	
	public function language() {
		Language::loadLanguageFile('de', 'MarkingTool_Editor', 'json', dirname(__FILE__).'/');
		$strings = Language::GetAll('MarkingTool_Editor');
		header("Content-Type: application/javascript");
		$js = file_get_contents(dirname(__FILE__).'/templates/langFile.js');
		$js = str_replace(
			array('"<--2-->"','"<--1-->"'),
			array(Language::$errorValue, json_encode($strings)),
			$js);
		echo $js;
	}
}
