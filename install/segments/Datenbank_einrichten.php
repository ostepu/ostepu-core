<?php
#region Datenbank_einrichten   
if (!$console || !isset($segmentDatenbankEinrichten)){
    if ($selected_menu === 1 && false && isset($segmentDatenbankEinrichten)){ /// ausgeblendet
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('componentLinkage','description')."</td></tr>";

        $text .= Design::erstelleZeile($console, Sprachen::Get('componentLinkage','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($console, Sprachen::Get('componentLinkage','full'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'full', null, true), 'v', '<img src="./images/VerbindungB.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('componentLinkage','fullAccessPoints'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungC.gif" style="width:128px;height:64;">', 'v');

        echo Design::erstelleBlock($console, Sprachen::Get('componentLinkage','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', true);
        echo $text;
    }
}

if ($simple && isset($segmentDatenbankEinrichten)){
    // leer
}
    
if (!$console || !isset($segmentDatenbankEinrichten)){
    if ($selected_menu === 1 && false && isset($segmentDatenbankEinrichten)){ /// ausgeblendet
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('componentAvailability','description')."</td></tr>";

        $text .= Design::erstelleZeile($console, Sprachen::Get('componentAvailability','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($console, Sprachen::Get('componentAvailability','full'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'full', null, true), 'v', '<img src="./images/VerbindungD.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('componentAvailability','fullAccessPoints'), 'e', Design::erstelleGruppenAuswahl($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungE.gif" style="width:128px;height:64;">', 'v');

        echo Design::erstelleBlock($console, Sprachen::Get('componentAvailability','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', true);
        echo $text;
    }
}

if ($simple && isset($segmentDatenbankEinrichten)){
    // leer
}

$segmentDatenbankEinrichten = true;
#endregion Datenbank_einrichten