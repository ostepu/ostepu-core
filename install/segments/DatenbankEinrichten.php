<?php
#region DatenbankEinrichten
class DatenbankEinrichten
{
    private static $initialized=false;
    public static $name = 'initDatabase';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 150;
    public static $enabledShow = true;
    private static $langTemplate='DatenbankEinrichten';

    public static $onEvents = array();


    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));
      
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    { 
        if (!Einstellungen::$accessAllowed) return;
            Installation::log(array('text'=>Installation::Get('main','functionBegin')));
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
/*if (!$console || !isset($segmentDatenbankEinrichten)){
    if ($selected_menu === 1 && false && isset($segmentDatenbankEinrichten)){ /// ausgeblendet
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('componentLinkage','description',self::$langTemplate));

        $text .= Design::erstelleZeile($console, Installation::Get('componentLinkage','local',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($console, Installation::Get('componentLinkage','full',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'full', null, true), 'v', '<img src="./images/VerbindungB.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($console, Installation::Get('componentLinkage','fullAccessPoints',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungC.gif" style="width:128px;height:64;">', 'v');

        echo Design::erstelleBlock($console, Installation::Get('componentLinkage','title',self::$langTemplate), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', true);
        echo $text;
    }
}

if (!$console || !isset($segmentDatenbankEinrichten)){
    if ($selected_menu === 1 && false && isset($segmentDatenbankEinrichten)){ /// ausgeblendet
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('componentAvailability','description',self::$langTemplate));

        $text .= Design::erstelleZeile($console, Installation::Get('componentAvailability','local',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($console, Installation::Get('componentAvailability','full',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'full', null, true), 'v', '<img src="./images/VerbindungD.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($console, Installation::Get('componentAvailability','fullAccessPoints',self::$langTemplate), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungE.gif" style="width:128px;height:64;">', 'v');

        echo Design::erstelleBlock($console, Installation::Get('componentAvailability','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', true);
        echo $text;
    }
}*/
#endregion DatenbankEinrichten