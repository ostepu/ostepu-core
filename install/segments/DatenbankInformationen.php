<?php
#region DatenbankInformationen
class DatenbankInformationen
{
    private static $initialized=false;
    public static $name = 'databaseSettings';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 100;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    private static $langTemplate='DatenbankInformationen';

    public static $onEvents = array();

    public static function getSettingsBar(&$data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $defs = self::getDefaults();
        $res = array(
                     'db_name' => array(Installation::Get('database_informations','db_name',self::$langTemplate), $data['DB']['db_name'], $defs['db_name'][1]),
                     'db_path' => array(Installation::Get('database_informations','db_path',self::$langTemplate), $data['DB']['db_path'], $defs['db_path'][1]),
                     'db_user' => array(Installation::Get('databaseAdmin','db_user',self::$langTemplate), $data['DB']['db_user'], $defs['db_user'][1]),
                     'db_user_operator' => array(Installation::Get('databasePlatformUser','db_user_operator',self::$langTemplate), $data['DB']['db_user_operator'], $defs['db_user_operator'][1])
                     );
        Installation::log(array('text'=>Installation::Get('database_informations','barResult',self::$langTemplate,array('res'=>json_encode($res)))));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function getDefaults()
    {
        return array(
                     'db_passwd' => array('data[DB][db_passwd]', null),
                     'db_passwd_operator' => array('data[DB][db_passwd_operator]', null),
                     'db_user_operator' => array('data[PL][localPath]', 'DBOperator'),
                     'db_user' => array('data[DB][db_user]', 'root'),
                     'db_name' => array('data[DB][db_name]', 'uebungsplattform'),
                     'db_path' => array('data[DB][db_path]', 'localhost')
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));
       
        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd'], 'data[DB][db_passwd]', $def['db_passwd'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', $def['db_passwd_operator'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', $def['db_user_operator'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user'], 'data[DB][db_user]', $def['db_user'][1] ,true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_name'], 'data[DB][db_name]', $def['db_name'][1],true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_path'], 'data[DB][db_path]', $def['db_path'][1],true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;
           
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text = '';
        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Installation::Get('database_informations','description',self::$langTemplate));
            $text .= Design::erstelleZeile($console, Installation::Get('database_informations','db_path',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost', true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('database_informations','db_name',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform', true), 'v');

            echo Design::erstelleBlock($console, Installation::Get('database_informations','title',self::$langTemplate), $text);
        }

        $text = '';
        if (!$console){
            $text .= "<tr><td colspan='2'>".Installation::Get('databaseAdmin','description',self::$langTemplate)."</td></tr>";
            $text .= Design::erstelleZeile($console, Installation::Get('databaseAdmin','db_user',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user'], 'data[DB][db_user]', 'root', true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('databaseAdmin','db_passwd',self::$langTemplate), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd'], 'data[DB][db_passwd]', '', true), 'v');

            echo Design::erstelleBlock($console, Installation::Get('databaseAdmin','title',self::$langTemplate), $text);
        }

        $text = '';
        if (!$console){
            $text .= "<tr><td colspan='2'>".Installation::Get('databasePlatformUser','description',self::$langTemplate)."</td></tr>";
            $text .= Design::erstelleZeile($console, Installation::Get('databasePlatformUser','db_user_operator',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('databasePlatformUser','db_passwd_operator',self::$langTemplate), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', '', true), 'v');

            echo Design::erstelleBlock($console, Installation::Get('databasePlatformUser','title',self::$langTemplate), $text);
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }
}
#endregion DatenbankInformationen