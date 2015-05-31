<?php
#region ZugangAusgeben
class ZugangAusgeben
{
    private static $initialized=false;
    public static $name = 'setAccess';
    public static $installed = false;
    public static $page = 5;
    public static $rank = 50;
    public static $enabledShow = true;
    public static $enabledInstall = false;
    
    public static $onEvents = array();
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $text = '';
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';
        
        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Sprachen::Get('access','description'));

            $text .= Design::erstelleZeile($console, Sprachen::Get('access','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'local', 'local', true), 'v');
            
            $text .= Design::erstelleZeile($console, '&nbsp;', '', '', '');
            $text .= Design::erstelleZeile($console, Sprachen::Get('access','ssh'), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'ssh', null, true), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('access','username'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_login'], 'data[ZV][zv_ssh_login]', 'root'), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('access','address'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_address'], 'data[ZV][zv_ssh_address]', 'localhost'), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('access','password'), 'e', Design::erstellePasswortzeile($console, $data['ZV']['zv_ssh_password'], 'data[ZV][zv_ssh_password]', ''), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'passwd', 'passwd', true),'h');
            $text .= Design::erstelleZeile($console, Sprachen::Get('access','keyFile'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_key_file'], 'data[ZV][zv_ssh_key_file]', '/var/public.ppk'), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'keyFile', null, true),'h');

            echo Design::erstelleBlock($console, Sprachen::Get('access','title'), $text);
        }
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        return null;
    }
}
#endregion ZugangAusgeben