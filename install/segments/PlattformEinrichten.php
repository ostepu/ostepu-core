<?php
#region PlattformEinrichten
if (!$console || !isset($segmentPlattformEinrichten)){
    if ($selected_menu === 4 && isset($segmentPlattformEinrichten)){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('platform','description')."</td></tr>";                       
        $text .= Design::erstelleZeile($console, Sprachen::Get('platform','createTables'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallPlatform'), 'h');
        $text .= Design::erstelleZeile($console, Sprachen::Get('platform','details'), 'e', Design::erstelleAuswahl($console, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        
        if ($installPlatform){
            foreach ($installPlatformResult as $component => $dat){
                $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Sprachen::Get('platform','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['pl_details'], 'data[PL][pl_details]', null,true);
        echo $text;
    }
} 

if ($simple && isset($segmentPlattformEinrichten)) {
    if ($installPlatform){
        $text = "<<< ".Sprachen::Get('platform','title')." >>>\n";
        $text .= Design::erstelleZeile($console, Sprachen::Get('platform','countComponents'), 'e', count($installPlatformResult), 'v');
        
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
        echo $text;
    }
}

$segmentPlattformEinrichten = true;
#endregion PlattformEinrichten