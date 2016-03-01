<?php
/**
 * @file PruefungErweiterungen.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */

#region PruefungErweiterungen
class PruefungErweiterungen
{
    public static $name = 'checkExtensions';
    public static $installed = false;
    public static $page = 0;
    public static $rank = 50;
    public static $enabledShow = true;
    private static $langTemplate='PruefungErweiterungen';

    public static $onEvents = array('check'=>array('name'=>'checkExtensions','event'=>array('actionCheckExtensions','page','install', 'update')));

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
        $text .= Design::erstelleBeschreibung($console,Installation::Get('extensions','description',self::$langTemplate));

        if (isset($result[self::$onEvents['check']['name']]) && $result[self::$onEvents['check']['name']]!=null){
           $result =  $result[self::$onEvents['check']['name']];
        } else
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if ($content!=null){
            foreach ($content as $extensionName => $status){
                if (!$console){
                    $text .= Design::erstelleZeile($console, $extensionName, 'e', ($status ? Installation::Get('main','ok') : "<font color='red'>".Installation::Get('main','fail')."</font>"), 'v');
                } else
                    $text .= $extensionName.' '.($status ? Installation::Get('main','ok') : Installation::Get('main','fail'))."\n";
            }
        } else
            $text .= Design::erstelleZeile($console, "<font color='red'>".Installation::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($console, Installation::Get('extensions','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $result = array();
        // check if php extensions are existing
        $result['curl'] = self::apache_extension_exists('curl');
        $result['mysql'] = self::apache_extension_exists('mysql');
        $result['mysqli'] = self::apache_extension_exists('mysqli');
        $result['json'] = self::apache_extension_exists('json');
        $result['mbstring'] = self::apache_extension_exists('mbstring');
        $result['openssl'] = self::apache_extension_exists('openssl');
        $result['fileinfo'] = self::apache_extension_exists('fileinfo');
        $result['sockets'] = self::apache_extension_exists('sockets');
        $result['gd'] = self::apache_extension_exists('gd');

        Installation::log(array('text'=>Installation::Get('extensions','checkResult',self::$langTemplate,array('res'=>json_encode($result)))));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $result;
    }

    public static function apache_extension_exists($extension)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        if (!function_exists('extension_loaded')){
            Installation::log(array('text'=>Installation::Get('extensions','missingFunctionExtension_loaded',self::$langTemplate), 'logLevel'=>LogLevel::ERROR));
            Installation::log(array('text'=>Installation::Get('main','functionEnd')));
            return false;
        }

        Installation::log(array('text'=>Installation::Get('extensions','checkExtension',self::$langTemplate,array('extension'=>$extension))));
        $res = extension_loaded($extension);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion PruefungErweiterungen