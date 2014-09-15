<?php
#region PlugInsInstallieren
if (!$simple)
    if ($selected_menu === 3){
        $text='';
        $text .= "<tr><td colspan='2'>".""."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, '<s>ausgewaehlte Installieren</s>', 'e', '', 'v', Design::erstelleSubmitButton('actionInstallPlugins',Sprachen::Get('main','install')), 'h');
        
        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen
        $text .= Design::erstelleZeile($simple, 'Core v0.1', 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null, true), 'v');
        $text .= Design::erstelleZeile($simple, 'OSTEPU-UI v0.1', 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        $text .= Design::erstelleZeile($simple, 'OSTEPU-LOGIC v0.1', 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        $text .= Design::erstelleZeile($simple, 'OSTEPU-DB v0.1', 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        $text .= Design::erstelleZeile($simple, 'OSTEPU-FS v0.1', 'e', Design::erstelleAuswahl($simple, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        
        if ($installPlatform){
            foreach ($installPlatformResult as $component => $dat){
                $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error);
        }
        
        echo Design::erstelleBlock($simple, 'Erweiterungen installieren', $text);
    }
#endregion PlugInsInstallieren
?>