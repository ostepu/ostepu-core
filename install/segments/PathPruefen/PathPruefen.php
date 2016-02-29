<?php
/**
 * @file PathPruefen.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

#region PathPruefen
class PathPruefen
{
    public static $name = 'checkPath';
    public static $installed = false;
    public static $page = 0;
    public static $rank = 150;
    public static $enabledShow = true;
    private static $langTemplate='PathPruefen';

    public static $onEvents = array('check'=>array('name'=>'checkPath','event'=>array('actionCheckPath','page','install', 'update')));

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text = '';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('applications','description',self::$langTemplate));

        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if ($content!=null){
            foreach ($content as $moduleName => $res){
                $status = $res[0];
                $desc = $res[1];
                if (!$console){
                    $text .= Design::erstelleZeile($console, $desc, 'e', ($status ? Installation::Get('main','ok') : "<font color='red'>".Installation::Get('main','fail')."</font>"), 'v');
                } else
                    $text .= $desc.' '.($status ? Installation::Get('main','ok') : Installation::Get('main','fail'))."\n";
            }
        } else
            $text .= Design::erstelleZeile($console, "<font color='red'>".Installation::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($console, Installation::Get('applications','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res=null;
        if (constant('ISCLI')){
            Installation::log(array('text'=>Installation::Get('applications','ISCLIEnabled',self::$langTemplate)));
            ///$res = json_decode(Request::get($data['PL']['url'].'/install/install.php/checkModulesExtern',array(),'')['content'],true);
        } else {
            Installation::log(array('text'=>Installation::Get('applications','ISCLIDisabled',self::$langTemplate)));
            $res = PathPruefen::checkModules($data,$fail,$errno,$error);
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function checkModules($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $result = array();

        // sammelt alle Aufrufe ein, welche geprüft werden sollen
        $applications = Installation::collect('checkExecutability',$data);

        // führt die Befehle aus und sammelt die Ergebnisse für die Darstellung
        foreach($applications as $app){
            $result[$app['name']] = array(self::element_exists($app['exec']),$app['desc']);
        }

        Installation::log(array('text'=>Installation::Get('applications','checkResult',self::$langTemplate,array('res'=>json_encode($result)))));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $result;
    }

    public static function element_exists($exec)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Installation::log(array('text'=>Installation::Get('applications','checkExec',self::$langTemplate,array('exec'=>$exec))));
        exec('('.$exec.') 2>&1', $output, $return);
        if ($return === 0){
            $res = true;
        } else {
            $res = false;
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion PathPruefen