<?php
#region Grundeinstellungen_ausgeben
if (!$console || !isset($segmentGrundeinstellungen)){
    if ($selected_menu === 2 && isset($segmentGrundeinstellungen)){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('general_settings','description')."</td></tr>";                        
        $text .= Design::erstelleZeile($console, Sprachen::Get('general_settings','init'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallInit'), 'h');
        $text .= Design::erstelleZeile($console, Sprachen::Get('database','db_override'), 'e', Design::erstelleAuswahl($console, $data['DB']['db_override'], 'data[DB][db_override]', 'override', null, true), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('database','db_ignore'), 'e', Design::erstelleAuswahl($console, $data['DB']['db_ignore'], 'data[DB][db_ignore]', 'ignore', null, true), 'v');

        $text .= Design::erstelleZeile($console, Sprachen::Get('general_settings','details'), 'e', Design::erstelleAuswahl($console, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', 'details', null, true), 'v');
        
        if ($installInit){
            foreach ($installInitResult as $component => $dat){
                $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            
            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Sprachen::Get('general_settings','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_ignore'], 'data[DB][db_ignore]', null, true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_override'], 'data[DB][db_override]', null, true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', null, true);
        echo $text;
    }
}

if ($simple && isset($segmentGrundeinstellungen)){
    if ($installInit){
        $text = "<<< ".Sprachen::Get('general_settings','title')." >>>\n";
        foreach ($installInitResult as $component => $dat){
            $text .= "{$component}: ".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : Sprachen::Get('main','fail')." ({$dat['status']})");
        }
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
    }
}

$segmentGrundeinstellungen = true;
#endregion Grundeinstellungen_ausgeben