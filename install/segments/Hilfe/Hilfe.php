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
    private static $langTemplate='Hilfe';

    public static $onEvents = array();

    public static function getDefaults()
    {
        return array(
                     'contactUrl' => array('data[HELP][contactUrl]', 'http://URL')
                     );
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['HELP']['contactUrl'], 'data[HELP][contactUrl]', $def['contactUrl'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text = '';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('helpSystem','description',self::$langTemplate));

        if (!$console){
            $text .= Design::erstelleZeile($console, Installation::Get('helpSystem','contactUrl',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['HELP']['contactUrl'], 'data[HELP][contactUrl]', $data['HELP']['contactUrl'], true), 'v');
        }

        echo Design::erstelleBlock($console, Installation::Get('helpSystem','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function platformSetting($data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $result = array('contactUrl'=>$data['HELP']['contactUrl']);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $result;
    }
}
#endregion Hilfe