<?php
#region ZugangAusgeben
class ZugangAusgeben
{
    public static $name = 'setAccess';
    public static $page = 5;
    public static $rank = 50;
    public static $enabledShow = false;
    public static $enabledInstall = false;

    public static $onEvents = array();

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $text='';

        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Language::Get('access','description'));

            $text .= Design::erstelleZeile($console, Language::Get('access','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'local', 'local', true), 'v');

            $text .= Design::erstelleZeile($console, '&nbsp;', '', '', '');
            $text .= Design::erstelleZeile($console, Language::Get('access','ssh'), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'ssh', null, true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('access','username'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_login'], 'data[ZV][zv_ssh_login]', 'root'), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('access','address'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_address'], 'data[ZV][zv_ssh_address]', 'localhost'), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('access','password'), 'e', Design::erstellePasswortzeile($console, $data['ZV']['zv_ssh_password'], 'data[ZV][zv_ssh_password]', ''), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'passwd', 'passwd', true),'h');
            $text .= Design::erstelleZeile($console, Language::Get('access','keyFile'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_key_file'], 'data[ZV][zv_ssh_key_file]', '/var/public.ppk'), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'keyFile', null, true),'h');

            echo Design::erstelleBlock($console, Language::Get('access','title'), $text);
        }
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }
}
#endregion ZugangAusgeben