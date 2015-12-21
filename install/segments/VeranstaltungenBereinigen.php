<?php
#region VeranstaltungenBereinigen
class VeranstaltungenBereinigen
{
    private static $initialized=false;
    public static $name = 'cleanCourses';
    public static $installed = false;
    public static $page = 4;
    public static $rank = 250;
    public static $enabledShow = true;

    public static $onEvents = array('collectCleanCourses'=>array('procedure'=>'collectCleanCourses','name'=>'collectCleanCourses','event'=>array('actionCollectCleanCourses')),'cleanCourses'=>array('procedure'=>'cleanCourses','name'=>'cleanCourses','event'=>array('actionCleanCourses')));

    public static function getDefaults()
    {
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {   Installation::log(array('text'=>'starte Funktion'));
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $executedEvents = array();
        foreach($result as $key => $value){
           $executedEvents[] = $key;
        }

        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('cleanCourses','description'));

        if (!$console){
            $text .= Design::erstelleZeile($console, Language::Get('cleanCourses','getAmount'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['collectCleanCourses']['event'][0],Language::Get('cleanCourses','collectAmount')), 'h');
        }

        if (isset($result[self::$onEvents['collectCleanCourses']['name']]) && $result[self::$onEvents['collectCleanCourses']['name']]!=null){
           $result =  $result[self::$onEvents['collectCleanCourses']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if (self::$installed){
            if (!$console && isset($data['C']['c_details']) && $data['C']['c_details'] === 'details'){
                if (isset($content)){
                    foreach ($content as $component => $dat){
                        $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'><div align ='center'>{$dat['amount']}</align></td></tr>";
                    }
                }
            } else {
                $count=0;
                if (isset($content)){
                    foreach ($content as $component => $dat){
                        $count+=$dat['amount'];
                    }
                }

                $text .= Design::erstelleZeile($console, Language::Get('cleanCourses','dirtyRows'), 'e', $count , 'v_c');
            }

            if (!$console && in_array(self::$onEvents['collectCleanCourses']['name'],$executedEvents)){
                $text .= Design::erstelleZeile($console, Language::Get('cleanCourses','cleanCourses'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['cleanCourses']['event'][0],Language::Get('cleanCourses','clean')), 'h');
            } elseif (!$console && in_array(self::$onEvents['cleanCourses']['name'],$executedEvents)){
                $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
            }
        }

        echo Design::erstelleBlock($console, Language::Get('cleanCourses','title'), $text);
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function collectCleanCourses($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $res = array();

        if (!$fail){
            $cleanLinks = Einstellungen::getLinks('getCleanAmount');

            // alle Veranstaltungen abrufen
            $multiRequestHandle = new Request_MultiRequest();
            Installation::log(array('text'=>'erstelle Anfrage: GET '.$data['PL']['url'].'/DB/DBCourse/course'));
            $handler = Request_CreateRequest::createGet($data['PL']['url'].'/DB/DBCourse/course',array(),'');
            $multiRequestHandle->addRequest($handler);
            $result = $multiRequestHandle->run();
            Installation::log(array('text'=>'Resultat: '.json_encode($result)));

            if (isset($result[0]['content']) && isset($result[0]['status']) && $result[0]['status'] === 200){
                // /course ausloesen
                $courses = Course::decodeCourse($result[0]['content']);
                if (!is_array($courses)) $courses = array($courses);

                $offset = count($courses)-50; // nur die letzten 50 Veranstaltungen werden bereinigt
                $offset = ($offset<0?0:$offset);
                $courses = array_slice($courses,$offset);

                foreach($courses as $course){

                    $multiRequestHandle = new Request_MultiRequest();
                    for ($i=0;$i<count($cleanLinks);$i++){
                        // inits all components
                        Installation::log(array('text'=>'erstelle Anfrage: GET '.$cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId()));
                        $handler = Request_CreateRequest::createGet($cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId(),array(), '');
                        $multiRequestHandle->addRequest($handler);
                    }
                    $answer = $multiRequestHandle->run();
                    Installation::log(array('text'=>'Resultat: '.json_encode($answer)));

                    //$res[$course->getId()] = array();
                    foreach ($answer as $result){
                        if (isset($result['content']) && isset($result['status']) && $result['status'] === 200){
                            $tables = json_decode($result['content'],true);
                            foreach ($tables as $table){
                                if (!isset($res[$table['component']])){
                                    $res[$table['component']] = array('amount'=>0,'dirtyTables'=>0,'cleanTables'=>0);
                                }

                                if (isset($table['amount'])){
                                    if ($table['amount'] == 0){
                                        $res[$table['component']]['cleanTables']++;
                                    } else {
                                        $res[$table['component']]['amount']+=$table['amount'];
                                        $res[$table['component']]['dirtyTables']++;
                                    }
                                } else {
                                    $res[$table['component']]['cleanTables']++;
                                }

                            }
                        }
                    }
                }

            } else {
                $fail = true;
                $error = "GET /DB/DBCourse/course ".Language::Get('courses','operationFailed');
                if (isset($result[0]['status'])){
                    $errno = $result[0]['status'];
                }
                Installation::log(array('text'=>'Fehler: '.$error, 'logLevel'=>LogLevel::ERROR));
            }
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }

    public static function cleanCourses($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $res = array();

        if (!$fail){
            $cleanLinks = Einstellungen::getLinks('deleteClean');

            // alle Veranstaltungen abrufen
            $multiRequestHandle = new Request_MultiRequest();
            Installation::log(array('text'=>'erstelle Anfrage: GET '.$data['PL']['url'].'/DB/DBCourse/course'));
            $handler = Request_CreateRequest::createGet($data['PL']['url'].'/DB/DBCourse/course',array(),'');
            $multiRequestHandle->addRequest($handler);
            $result = $multiRequestHandle->run();
            Installation::log(array('text'=>'Resultat: '.json_encode($result)));

            if (isset($result[0]['content']) && isset($result[0]['status']) && $result[0]['status'] === 200){
                // /course ausloesen
                $courses = Course::decodeCourse($result[0]['content']);
                if (!is_array($courses)) $courses = array($courses);

                $offset = count($courses)-50; // nur die letzten 50 Veranstaltungen werden bereinigt
                $offset = ($offset<0?0:$offset);
                $courses = array_slice($courses,$offset);

                foreach($courses as $course){

                    $multiRequestHandle = new Request_MultiRequest();
                    $answer=array();
                    for ($i=0;$i<count($cleanLinks);$i++){
                        // inits all components
                        Installation::log(array('text'=>'erstelle Anfrage: DELETE '.$cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId()));
                        $handler = Request_CreateRequest::createDelete($cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId(),array(), '');
                        $multiRequestHandle->addRequest($handler);
                    }
                    $answer = $multiRequestHandle->run();
                    Installation::log(array('text'=>'Resultat: '.json_encode($answer)));
                }

                $res['status'] = 201;

            } else {
                $fail = true;
                $error = "GET /DB/DBCourse/course ".Language::Get('courses','operationFailed');
                if (isset($result[0]['status'])){
                    $errno = $result[0]['status'];
                }
                Installation::log(array('text'=>'Fehler: '.$error, 'logLevel'=>LogLevel::ERROR));
            }
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }
}
#endregion VeranstaltungenBereinigen