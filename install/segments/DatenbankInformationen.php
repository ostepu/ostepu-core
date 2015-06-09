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
    
    public static $onEvents = array();
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd'], 'data[DB][db_passwd]', null);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', null);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user'], 'data[DB][db_user]', 'root' ,true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost',true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text = '';
        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Language::Get('database_informations','description'));
            $text .= Design::erstelleZeile($console, Language::Get('database_informations','db_path'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost', true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('database_informations','db_name'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform', true), 'v');

            echo Design::erstelleBlock($console, Language::Get('database_informations','title'), $text);
        }
            
        $text = '';
        if (!$console){
            $text .= "<tr><td colspan='2'>".Language::Get('databaseAdmin','description')."</td></tr>";
            $text .= Design::erstelleZeile($console, Language::Get('databaseAdmin','db_user'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user'], 'data[DB][db_user]', 'root', true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('databaseAdmin','db_passwd'), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd'], 'data[DB][db_passwd]', ''), 'v');
        
            echo Design::erstelleBlock($console, Language::Get('databaseAdmin','title'), $text);
        }
            
        $text = '';
        if (!$console){
            $text .= "<tr><td colspan='2'>".Language::Get('databasePlatformUser','description')."</td></tr>";
            $text .= Design::erstelleZeile($console, Language::Get('databasePlatformUser','db_user_operator'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('databasePlatformUser','db_passwd_operator'), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', ''), 'v');

            echo Design::erstelleBlock($console, Language::Get('databasePlatformUser','title'), $text);
        }
        
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        return null;
    }
}
#endregion DatenbankInformationen