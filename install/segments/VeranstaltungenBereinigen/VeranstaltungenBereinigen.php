<?php
/**
 * @file VeranstaltungenBereinigen.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */

#region VeranstaltungenBereinigen
class VeranstaltungenBereinigen
{
    private static $initialized=false;
    public static $name = 'cleanCourses';
    public static $installed = false;
    public static $page = 4;
    public static $rank = 250;
    public static $enabledShow = true;
    private static $langTemplate='VeranstaltungenBereinigen';

    public static $onEvents = array('collectCleanCourses'=>array('procedure'=>'collectCleanCourses','name'=>'collectCleanCourses','event'=>array('actionCollectCleanCourses')),'cleanCourses'=>array('procedure'=>'cleanCourses','name'=>'cleanCourses','event'=>array('actionCleanCourses')));

    public static function getDefaults()
    {
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {   Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $executedEvents = array();
        foreach($result as $key => $value){
           $executedEvents[] = $key;
        }

        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('cleanCourses','description',self::$langTemplate));

        if (!$console){
            $text .= Design::erstelleZeile($console, Installation::Get('cleanCourses','getAmount',self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['collectCleanCourses']['event'][0],Installation::Get('cleanCourses','collectAmount',self::$langTemplate)), 'h');
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
                        $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'><div align ='center'>".$dat['amount'].' ('.Installation::Get('cleanCourses','rounded',self::$langTemplate).Design::formatBytes($dat['size']).')'."</align></td></tr>";
                    }
                }
            } else {
                $count=0;
                $size=0;
                if (isset($content)){
                    foreach ($content as $component => $dat){
                        $count+=$dat['amount'];
                        $size+=$dat['size'];
                    }
                }

                $text .= Design::erstelleZeile($console, Installation::Get('cleanCourses','dirtyRows',self::$langTemplate), 'e', $count.' ('.Installation::Get('cleanCourses','rounded',self::$langTemplate).Design::formatBytes($size).')' , 'v_c');
            }

            if (!$console && in_array(self::$onEvents['collectCleanCourses']['name'],$executedEvents)){
                $text .= Design::erstelleZeile($console, Installation::Get('cleanCourses','cleanCourses',self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['cleanCourses']['event'][0],Installation::Get('cleanCourses','clean',self::$langTemplate)), 'h');
            } elseif (!$console && in_array(self::$onEvents['cleanCourses']['name'],$executedEvents)){
                $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('cleanCourses','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function collectCleanCourses($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();

        if (!$fail){
            $cleanLinks = Einstellungen::getLinks('getCleanAmount');

            // alle Veranstaltungen abrufen
            $multiRequestHandle = new Request_MultiRequest();
            Installation::log(array('text'=>Installation::Get('cleanCourses','createGetCoursesQuery',self::$langTemplate,array('url'=>'GET '.$data['PL']['url'].'/DB/DBCourse/course'))));
            $handler = Request_CreateRequest::createGet($data['PL']['url'].'/DB/DBCourse/course',array(),'');
            $multiRequestHandle->addRequest($handler);
            $result = $multiRequestHandle->run();
            Installation::log(array('text'=>Installation::Get('cleanCourses','GetCoursesQueryResult',self::$langTemplate,array('res'=>json_encode($result)))));

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
                        Installation::log(array('text'=>Installation::Get('cleanCourses','createCollectQuery',self::$langTemplate,array('url'=>'GET '.$cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId()))));
                        $handler = Request_CreateRequest::createGet($cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId(),array(), '');
                        $multiRequestHandle->addRequest($handler);
                    }
                    $answer = $multiRequestHandle->run();
                    Installation::log(array('text'=>Installation::Get('cleanCourses','CollectQueryResult',self::$langTemplate,array('res'=>json_encode($answer)))));

                    //$res[$course->getId()] = array();
                    foreach ($answer as $result){
                        if (isset($result['content']) && isset($result['status']) && $result['status'] === 200){
                            $tables = json_decode($result['content'],true);
                            foreach ($tables as $table){
                                if (!isset($res[$table['component']])){
                                    $res[$table['component']] = array('size'=>0,'amount'=>0,'dirtyTables'=>0,'cleanTables'=>0);
                                }

                                if (!isset($table['amount'])) $table['amount'] = 0;
                                if (!isset($table['size'])) $table['size'] = 0;

                                if (isset($table['amount'])){
                                    if ($table['amount'] == 0){
                                        $res[$table['component']]['cleanTables']++;
                                    } else {
                                        $res[$table['component']]['amount']+=$table['amount'];
                                        $res[$table['component']]['size']+=$table['size'];
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
                $error = "GET /DB/DBCourse/course ".Installation::Get('courses','operationFailed',self::$langTemplate);
                if (isset($result[0]['status'])){
                    $errno = $result[0]['status'];
                }
                Installation::log(array('text'=>Installation::Get('cleanCourses','failureCollect',self::$langTemplate,array('message'=>$error)), 'logLevel'=>LogLevel::ERROR));
            }
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function cleanCourses($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();

        if (!$fail){
            $cleanLinks = Einstellungen::getLinks('deleteClean');

            // alle Veranstaltungen abrufen
            $multiRequestHandle = new Request_MultiRequest();
            Installation::log(array('text'=>Installation::Get('cleanCourses','createGetCoursesQuery',self::$langTemplate,array('url'=>'GET '.$data['PL']['url'].'/DB/DBCourse/course'))));
            $handler = Request_CreateRequest::createGet($data['PL']['url'].'/DB/DBCourse/course',array(),'');
            $multiRequestHandle->addRequest($handler);
            $result = $multiRequestHandle->run();
            Installation::log(array('text'=>Installation::Get('cleanCourses','GetCoursesQueryResult',self::$langTemplate,array('res'=>json_encode($result)))));

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
                        Installation::log(array('text'=>Installation::Get('cleanCourses','createCleanQuery',self::$langTemplate,array('url'=>'DELETE '.$cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId()))));
                        $handler = Request_CreateRequest::createDelete($cleanLinks[$i]->getAddress(). '/clean/clean/course/'.$course->getId(),array(), '');
                        $multiRequestHandle->addRequest($handler);
                    }
                    $answer = $multiRequestHandle->run();
                    Installation::log(array('text'=>Installation::Get('cleanCourses','CleanResult',self::$langTemplate,array('res'=>json_encode($answer)))));
                }

                $res['status'] = 201;

            } else {
                $fail = true;
                $error = "GET /DB/DBCourse/course ".Installation::Get('courses','operationFailed',self::$langTemplate);
                if (isset($result[0]['status'])){
                    $errno = $result[0]['status'];
                }
                Installation::log(array('text'=>Installation::Get('cleanCourses','failureClean',self::$langTemplate,array('message'=>$error)), 'logLevel'=>LogLevel::ERROR));
            }
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion VeranstaltungenBereinigen