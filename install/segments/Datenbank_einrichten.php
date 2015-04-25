<?php
#region Datenbank_einrichten   
if (!$simple)
    if ($selected_menu === 1 && false){ /// ausgeblendet
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('componentLinkage','description')."</td></tr>";

        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentLinkage','local'), 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentLinkage','full'), 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'full', null, true), 'v', '<img src="./images/VerbindungB.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentLinkage','fullAccessPoints'), 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungC.gif" style="width:128px;height:64;">', 'v');

        echo Design::erstelleBlock($simple, Sprachen::Get('componentLinkage','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', true);
        echo $text;
    }
    
if (!$simple)
    if ($selected_menu === 1 && false){ /// ausgeblendet
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('componentAvailability','description')."</td></tr>";

        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentAvailability','local'), 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentAvailability','full'), 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'full', null, true), 'v', '<img src="./images/VerbindungD.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentAvailability','fullAccessPoints'), 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungE.gif" style="width:128px;height:64;">', 'v');

        echo Design::erstelleBlock($simple, Sprachen::Get('componentAvailability','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', true);
        echo $text;
    }
#endregion Datenbank_einrichten