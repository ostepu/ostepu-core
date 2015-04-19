<?php
#region Grundeinstellungen_ausgeben
if (!$simple)
    if ($selected_menu === 2){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('general_settings','description')."</td></tr>";                        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_settings','init'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallInit'), 'h');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('database','db_override'), 'e', Design::erstelleAuswahl($simple, $data['DB']['db_override'], 'data[DB][db_override]', 'override', null, true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('database','db_ignore'), 'e', Design::erstelleAuswahl($simple, $data['DB']['db_ignore'], 'data[DB][db_ignore]', 'ignore', null, true), 'v');

        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_settings','details'), 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', 'details', null, true), 'v');
        
        if ($installInit){
            foreach ($installInitResult as $component => $dat){
                $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($simple, Sprachen::Get('general_settings','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_ignore'], 'data[DB][db_ignore]', null, true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_override'], 'data[DB][db_override]', null, true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', null, true);
        echo $text;
    }
#endregion Grundeinstellungen_ausgeben