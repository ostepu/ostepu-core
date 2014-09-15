<?php
#region PlattformEinrichten
if (!$simple)
    if ($selected_menu === 4){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('platform','description')."</td></tr>";                       
        $text .= Design::erstelleZeile($simple, Sprachen::Get('platform','createTables'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallPlatform'), 'h');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('platform','details'), 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        
        if ($installPlatform){
            foreach ($installPlatformResult as $component => $dat){
                $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($simple, Sprachen::Get('platform','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', null,true);
        echo $text;
    }
#endregion PlattformEinrichten
?>