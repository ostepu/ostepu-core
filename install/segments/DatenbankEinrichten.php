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

    public static $onEvents = array();  

    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        self::$initialized = true;
    }  

    public static function show($console, $result, $data)
    {  
        return null;
    }  

    public static function install($data, &$fail, &$errno, &$error)
    {
        return null;
    }
}
/*if (!$console || !isset($segmentDatenbankEinrichten)){
    if ($selected_menu === 1 && false && isset($segmentDatenbankEinrichten)){ /// ausgeblendet
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('componentLinkage','description'));  

        $text .= Design::erstelleZeile($console, Language::Get('componentLinkage','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($console, Language::Get('componentLinkage','full'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'full', null, true), 'v', '<img src="./images/VerbindungB.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($console, Language::Get('componentLinkage','fullAccessPoints'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungC.gif" style="width:128px;height:64;">', 'v');  

        echo Design::erstelleBlock($console, Language::Get('componentLinkage','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', true);
        echo $text;
    }
}  

if (!$console || !isset($segmentDatenbankEinrichten)){
    if ($selected_menu === 1 && false && isset($segmentDatenbankEinrichten)){ /// ausgeblendet
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('componentAvailability','description'));  

        $text .= Design::erstelleZeile($console, Language::Get('componentAvailability','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($console, Language::Get('componentAvailability','full'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'full', null, true), 'v', '<img src="./images/VerbindungD.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($console, Language::Get('componentAvailability','fullAccessPoints'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungE.gif" style="width:128px;height:64;">', 'v');  

        echo Design::erstelleBlock($console, Language::Get('componentAvailability','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', true);
        echo $text;
    }
}*/
#endregion DatenbankEinrichten