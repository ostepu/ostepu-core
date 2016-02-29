<?php
/**
 * @file VeranstaltungenEinrichten.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */

#region VeranstaltungenEinrichten
class VeranstaltungenEinrichten
{
    private static $initialized=false;
    public static $name = 'initCourses';
    public static $installed = false;
    public static $page = 4;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate='VeranstaltungenEinrichten';

    public static $onEvents = array('install'=>array('name'=>'initCourses','event'=>array('actionInstallCourses','install', 'update')));

    public static function getDefaults()
    {
        return array(
                     'c_details' => array('data[C][c_details]', null)
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['C']['c_details'], 'data[C][c_details]', $def['c_details'][1],true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $isUpdate = (isset($data['action']) && $data['action']=='update') ? true : false;

        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('courses','description',self::$langTemplate));

        if (!$console){
            $text .= Design::erstelleZeile($console, Installation::Get('courses','createTables',self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            $text .= Design::erstelleZeile($console, Installation::Get('courses','details',self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['C']['c_details'], 'data[C][c_details]', 'details', null), 'v_c');
        }

        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']]!=null){
           $result =  $result[self::$onEvents['install']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if (self::$installed){
            if (!$console && isset($data['C']['c_details']) && $data['C']['c_details'] === 'details' && !$isUpdate){
                foreach ($content as $courseid => $dat){
                    $text .= "<tr><td class='e' rowspan='1'>({$dat['course']->getId()}) {$dat['course']->getSemester()}</td><td class='v'>{$dat['course']->getName()}</td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Installation::Get('main','ok') : "<font color='red'>".Installation::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
            } else
                $text .= Design::erstelleZeile($console, Installation::Get('courses','countCourses',self::$langTemplate), 'e', count($content) , 'v_c');
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Installation::Get('courses','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();

        if (!$fail){
            // die /course Befehle der LCourse auslösen

            // alle Veranstaltungen abrufen
            $multiRequestHandle = new Request_MultiRequest();
            Installation::log(array('text'=>Installation::Get('courses','createGetCourseQuery',self::$langTemplate,array('url'=>'GET '.$data['PL']['url'].'/DB/DBCourse/course'))));
            $handler = Request_CreateRequest::createGet($data['PL']['url'].'/DB/DBCourse/course',array(),'');
            $multiRequestHandle->addRequest($handler);
            $result = $multiRequestHandle->run();
            Installation::log(array('text'=>Installation::Get('courses','getCourseResult',self::$langTemplate,array('res'=>json_encode($result)))));

            if (isset($result[0]['content']) && isset($result[0]['status']) && $result[0]['status'] === 200){
                // /course ausloesen
                $courses = Course::decodeCourse($result[0]['content']);
                if (!is_array($courses)) $courses = array($courses);

                $multiRequestHandle = new Request_MultiRequest();
                foreach($courses as $course){
                    Installation::log(array('text'=>Installation::Get('courses','createInitCourseQuery',self::$langTemplate,array('url'=>'POST '.$data['PL']['url'].'/logic/LCourse/course'.' Content: '.Course::encodeCourse($course)))));
                    $handler = Request_CreateRequest::createPost($data['PL']['url'].'/logic/LCourse/course',array(),Course::encodeCourse($course));
                    $multiRequestHandle->addRequest($handler);
                }
                $answer = $multiRequestHandle->run();
                Installation::log(array('text'=>Installation::Get('courses','initCourseResult',self::$langTemplate,array('res'=>json_encode($answer)))));

                if (count($courses) != count($answer)){
                    $fail = true;
                    $error = Installation::Get('courses','differentAnswers')."\n".Installation::Get('main','line').':'.__LINE__;
                    Installation::log(array('text'=>Installation::Get('courses','failureInitCourses',self::$langTemplate,array('message'=>$error)), 'logLevel'=>LogLevel::ERROR));
                }

                $i=0;
                foreach($courses as $course){
                    $result = $answer[$i];
                    $res[$course->getId()] = array();
                        $res[$course->getId()]['course'] = $course;
                    if (isset($result['content']) && isset($result['status']) && $result['status'] === 201){
                        $res[$course->getId()]['status'] = 201;
                        Installation::log(array('text'=>Installation::Get('courses','initCourseSuccess',self::$langTemplate,array('name'=>$course->getName()))));
                    } else {
                        $res[$course->getId()]['status'] = 409;
                        $fail = true;
                        if (isset($result['status'])){
                            $errno = $result['status'];
                            $res[$course->getId()]['status'] = $result['status'];
                        }
                        Installation::log(array('text'=>Installation::Get('courses','initCourseError',self::$langTemplate,array('status'=>$res[$course->getId()]['status'])), 'logLevel'=>LogLevel::ERROR));
                    }
                    $i++;
                    if ($i>=count($answer)) break;
                }

            } else {
                $fail = true;
                $error = "GET /DB/DBCourse/course ".Installation::Get('courses','operationFailed',self::$langTemplate);
                if (isset($result[0]['status'])){
                    $errno = $result[0]['status'];
                }
                Installation::log(array('text'=>Installation::Get('courses','failureGetCourses',self::$langTemplate,array('message'=>$error)), 'logLevel'=>LogLevel::ERROR));
            }
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion VeranstaltungenEinrichten