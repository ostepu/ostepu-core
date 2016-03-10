<?php
/**
 * @file ModulpruefungAusgeben.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Max Brauer <ma.brauer@live.de>
 * @date 2016
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */

#region ModulpruefungAusgeben
class ModulpruefungAusgeben
{
    public static $name = 'checkModules';
    public static $installed = false;
    public static $page = 0;
    public static $rank = 50;
    public static $enabledShow = true;
    private static $langTemplate='ModulpruefungAusgeben';

    public static $onEvents = array('check'=>array('name'=>'checkModules','event'=>array('actionCheckModules','page','install', 'update')));

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
        $text .= Design::erstelleBeschreibung($console,Installation::Get('modules','description',self::$langTemplate));

        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];
        
        if (isset($content['status']) && $content['status'] == 200){
            if ($content!=null){
                foreach ($content as $moduleName => $status){
                    if (!$console){
                        $text .= Design::erstelleZeile($console, $moduleName, 'e', ($status ? Installation::Get('main','ok') : "<font color='red'>".Installation::Get('main','fail')."</font>"), 'v');
                    } else
                        $text .= $moduleName.' '.($status ? Installation::Get('main','ok') : Installation::Get('main','fail'))."\n";
                }
            } else{
                if (!$console){
                    $text .= Design::erstelleZeile($console, "<font color='red'>".Installation::Get('main','fail')."</font>", 'e');
                } else {
                    $text .= Design::erstelleZeile($console, Installation::Get('main','fail'), 'e');
                }
            }
        } else {
            if (!isset($content['status']) || $content['status'] == 404){
                if (!$console){
                    $text .= Design::erstelleZeile($console, "<font color='red'>".Installation::Get('modules','failUrl',self::$langTemplate,array('url'=>$data['PL']['url'].'/install/install.php/checkModulesExtern'))."</font>", 'e');
                } else {
                    $text .= Design::erstelleZeile($console, Installation::Get('modules','failUrl',self::$langTemplate,array('url'=>$data['PL']['url'].'/install/install.php/checkModulesExtern')), 'e');
                }   
            }            
        }

        echo Design::erstelleBlock($console, Installation::Get('modules','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res=null;
        if (constant('ISCLI')){
            Installation::log(array('text'=>Installation::Get('modules','ISCLIEnabled',self::$langTemplate)));
            $result = Request::get($data['PL']['url'].'/install/install.php/checkModulesExtern',array(),'');
            if ($result['status'] == 200){
                $res = json_decode($result['content'],true);
                $res['status'] = 200;
            } else {
                $res['status'] = 404;
            }
        } elseif (true || constant('ISCGI')){
            Installation::log(array('text'=>Installation::Get('modules','ISCGIEnabled',self::$langTemplate)));
            $result = Request::get($data['PL']['url'].'/install/segments/ModulpruefungAusgeben/external.php',array(),'');
            if ($result['status'] == 200){
                $res = json_decode($result['content'],true);
                $res['status'] = 200;
            } else {
                $res['status'] = 404;
            }
        } else {
            Installation::log(array('text'=>Installation::Get('modules','ISCLIDisabled',self::$langTemplate)));
            Installation::log(array('text'=>Installation::Get('modules','ISCGIDisabled',self::$langTemplate)));
            $res = ModulpruefungAusgeben::checkModules($data,$fail,$errno,$error);
            $res['status'] = 200;
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function checkModules($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $result = array();

        // check if apache modules are existing
        $result['mod_php5'] = self::apache_module_exists('mod_php5');
        $result['mod_rewrite'] = self::apache_module_exists('mod_rewrite');
        $result['mod_deflate'] = self::apache_module_exists('mod_deflate');
        $result['mod_headers(win)'] = self::apache_module_exists('mod_headers');
        $result['mod_filter(win)'] = self::apache_module_exists('mod_filter');
        $result['mod_expires(win)'] = self::apache_module_exists('mod_expires');

        Installation::log(array('text'=>Installation::Get('modules','checkResult',self::$langTemplate,array('res'=>json_encode($result)))));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $result;
    }

    public static function apache_module_exists($module)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Installation::log(array('text'=>Installation::Get('modules','checkModule',self::$langTemplate,array('module'=>$module))));
        if (function_exists('apache_get_modules')){
            $res = in_array($module, apache_get_modules());
        } else {
            $res = getenv('HTTP_'.strtoupper($module))=='On'?TRUE:
            getenv('REDIRECT_HTTP_'.strtoupper($module))=='On'?true:FALSE;
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion ModulpruefungAusgeben