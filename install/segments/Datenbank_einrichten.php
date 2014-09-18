<?php
#region Datenbank_einrichten   
if (!$simple)
    /*if ($selected_menu === 3){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('componentDefinitions','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentDefinitions','componentsSql'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['componentsSql'], 'data[DB][componentsSql]', '../DB/Components2.sql', true), 'v', Design::erstelleSubmitButton('actionInstallComponents'), 'h');
        if ($installComponentFile)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, Sprachen::Get('componentDefinitions','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['componentsSql'], 'data[DB][componentsSql]', '../DB/Components2.sql', true);
        echo $text;
    }*/
    
if (!$simple)
    if ($selected_menu === 7){
        $text='';
        $text .= "<tr><td colspan='2'>".''."</td></tr>";
        
        //$text .= Design::erstelleZeile($simple, 'erstelle Verknüpfungen', 'e','','v', Design::erstelleSubmitButton('actionGenerateComponentLinks'), 'h');
        $text .= Design::erstelleZeile($simple, "nur Lokal", 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($simple, "Vollständig (direkt)", 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'full', null, true), 'v', '<img src="./images/VerbindungB.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($simple, "<s>Vollständig (über Zugriffspunkte)</s>", 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungC.gif" style="width:128px;height:64;">', 'v');
        
        //if ($installComponentFile)
        //    $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, 'Komponentenverknüpfungen', $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['CO']['co_link_type'], 'data[CO][co_link_type]', 'local', true);
        echo $text;
    }
    
if (!$simple)
    if ($selected_menu === 7){
        $text='';
        $text .= "<tr><td colspan='2'>".''."</td></tr>";
        
        //$text .= Design::erstelleZeile($simple, 'erstelle Verknüpfungen', 'e','','v', Design::erstelleSubmitButton('actionGenerateComponentLinks'), 'h');
        $text .= Design::erstelleZeile($simple, "nur Lokal", 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', 'local', true), 'v', '<img src="./images/VerbindungA.gif" style="width:128px;height:64;">', 'v' );
        $text .= Design::erstelleZeile($simple, "Vollständig (direkt)", 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'full', null, true), 'v', '<img src="./images/VerbindungD.gif" style="width:128px;height:64;">', 'v');
        $text .= Design::erstelleZeile($simple, "<s>Vollständig (über Zugriffspunkte)</s>", 'e', Design::erstelleGruppenAuswahl($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'fullAccessPoints', null, true), 'v', '<img src="./images/VerbindungE.gif" style="width:128px;height:64;">', 'v');
        
        //if ($installComponentFile)
        //    $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, 'Komponentenverfügbarkeit', $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['CO']['co_link_availability'], 'data[CO][co_link_availability]', 'local', true);
        echo $text;
    }
#endregion Datenbank_einrichten
?>