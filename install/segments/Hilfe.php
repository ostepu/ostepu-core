<?php
#region Hilfe
class Hilfe
{
    private static $initialized = false;
    public static $name = 'help';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 150;
    public static $enabledShow = true;

    public static $onEvents = array();

    public static function getDefaults()
    {
        return array(
                     'contactUrl' => array('data[HELP][contactUrl]', 'http://URL')
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['HELP']['contactUrl'], 'data[HELP][contactUrl]', $def['contactUrl'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $text = '';
        $text .= Design::erstelleBeschreibung($console,Language::Get('helpSystem','description'));

        if (!$console){
            $text .= Design::erstelleZeile($console, Language::Get('helpSystem','contactUrl'), 'e', Design::erstelleEingabezeile($console, $data['HELP']['contactUrl'], 'data[HELP][contactUrl]', $data['HELP']['contactUrl'], true), 'v');
        }

        echo Design::erstelleBlock($console, Language::Get('helpSystem','title'), $text);
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function platformSetting($data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $result = array('contactUrl'=>$data['HELP']['contactUrl']);
        Installation::log(array('text'=>'beende Funktion'));
        return $result;
    }
}
#endregion Hilfe