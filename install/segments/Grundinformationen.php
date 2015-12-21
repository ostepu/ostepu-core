<?php
#region Grundinformationen
class Grundinformationen
{
    private static $initialized = false;
    public static $name = 'mainInfo';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 50;
    public static $enabledShow = true;

    public static $onEvents = array();

    public static function getSettingsBar(&$data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $defs = self::getDefaults();
        $res = array(
                     'url' => array(Language::Get('general_informations','url'), $data['PL']['url'], $defs['url'][1]),
                     'localPath' => array(Language::Get('general_informations','localPath'), $data['PL']['localPath'], $defs['localPath'][1]),
                     'urlExtern' => array(Language::Get('general_informations','urlExtern'), $data['PL']['urlExtern'], $defs['urlExtern'][1]),
                     'temp' => array(Language::Get('general_informations','temp'), $data['PL']['temp'], $defs['temp'][1]),
                     'files' => array(Language::Get('general_informations','files'), $data['PL']['files'], $defs['files'][1])
                     );
        Installation::log(array('text'=>'Resultat = '.json_encode($res)));
        Installation::log(array('text'=>'beende Funktion'));
        return $res;
    }

    public static function getDefaults()
    {
        return array(
                     'name' => array('data[SV][name]', ''),
                     'url' => array('data[PL][url]', 'http://localhost/uebungsplattform'),
                     'localPath' => array('data[PL][localPath]', '/var/www/uebungsplattform'),
                     'urlExtern' => array('data[PL][urlExtern]', 'http://localhost/uebungsplattform'),
                     'temp' => array('data[PL][temp]', '/var/www/temp'),
                     'files' => array('data[PL][files]', '/var/www/files')
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['SV']['name'], 'data[SV][name]', $def['name'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['url'], 'data[PL][url]', $def['url'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['localPath'], 'data[PL][localPath]', $def['localPath'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', $def['urlExtern'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', $def['temp'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['files'], 'data[PL][files]', $def['files'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $text = '';
        $text .= Design::erstelleBeschreibung($console,Language::Get('general_informations','description'));

        if (!$console){
            $text .= Design::erstelleZeile($console, Language::Get('general_informations','server_name'), 'e', Design::erstelleEingabezeile($console, $data['SV']['name'], 'data[SV][name]', $data['SV']['name'], false), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('general_informations','url'), 'e', Design::erstelleEingabezeile($console, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('general_informations','localPath'), 'e', Design::erstelleEingabezeile($console, $data['PL']['localPath'], 'data[PL][localPath]', '/var/www/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('general_informations','urlExtern'), 'e', Design::erstelleEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('general_informations','temp'), 'e', Design::erstelleEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('general_informations','files'), 'e', Design::erstelleEingabezeile($console, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true), 'v');
        }

        echo Design::erstelleBlock($console, Language::Get('general_informations','title'), $text);
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }
}
#endregion Grundinformationen