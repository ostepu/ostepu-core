<?php
#region ZugangAusgeben
class ZugangAusgeben
{
    public static $name = 'setAccess';
    public static $page = 5;
    public static $rank = 50;
    public static $enabledShow = false;
    public static $enabledInstall = false;
    private static $langTemplate='ZugangAusgeben';

    public static $onEvents = array();

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Language::Get('main','languageInstantiated')));
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }
   
    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;
           
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        $text='';

        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Language::Get('access','description',self::$langTemplate));

            $text .= Design::erstelleZeile($console, Language::Get('access','local',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'local', 'local', true), 'v');

            $text .= Design::erstelleZeile($console, '&nbsp;', '', '', '');
            $text .= Design::erstelleZeile($console, Language::Get('access','ssh',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'ssh', null, true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('access','username',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_login'], 'data[ZV][zv_ssh_login]', 'root'), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('access','address',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_address'], 'data[ZV][zv_ssh_address]', 'localhost'), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('access','password',self::$langTemplate), 'e', Design::erstellePasswortzeile($console, $data['ZV']['zv_ssh_password'], 'data[ZV][zv_ssh_password]', ''), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'passwd', 'passwd', true),'h');
            $text .= Design::erstelleZeile($console, Language::Get('access','keyFile',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_key_file'], 'data[ZV][zv_ssh_key_file]', '/var/public.ppk'), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'keyFile', null, true),'h');

            echo Design::erstelleBlock($console, Language::Get('access','title',self::$langTemplate), $text);
        }
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
        return null;
    }
}
#endregion ZugangAusgeben