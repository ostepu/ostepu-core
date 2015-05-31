<?php
#region Grundinformationen
class Grundinformationen
{
    private static $initialized=false;
    public static $name = 'mainInfo';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 50;
    public static $enabledShow = true;
    
    public static $onEvents = array();
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['SV']['name'], 'data[SV][name]', '', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['localPath'], 'data[PL][localPath]', '/var/www/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://localhost/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text = '';
        $text .= Design::erstelleBeschreibung($console,Sprachen::Get('general_informations','description'));
         
        if (!$console){
            $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','server_name'), 'e', Design::erstelleEingabezeile($console, $data['SV']['name'], 'data[SV][name]', $data['SV']['name'], false), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','url'), 'e', Design::erstelleEingabezeile($console, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','localPath'), 'e', Design::erstelleEingabezeile($console, $data['PL']['localPath'], 'data[PL][localPath]', '/var/www/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','urlExtern'), 'e', Design::erstelleEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','temp'), 'e', Design::erstelleEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true), 'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','files'), 'e', Design::erstelleEingabezeile($console, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true), 'v');
        }
        
        echo Design::erstelleBlock($console, Sprachen::Get('general_informations','title'), $text);
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        return null;
    }
}
#endregion Grundinformationen