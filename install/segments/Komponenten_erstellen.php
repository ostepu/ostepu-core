<?php
#region Komponenten_erstellen
if (!$console || !isset($segmentKomponentenErstellen)){
    if ($selected_menu === 3 && isset($segmentKomponentenErstellen)){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('generateComponents','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($console, Sprachen::Get('generateComponents','generateComponents'), 'e', '','v',Design::erstelleSubmitButton('actionInstallComponentDefs'), 'h');

        if ($installComponentDefs){        
            if (isset($installComponentDefsResult['components'])){
                $text .= Design::erstelleZeile($console, Sprachen::Get('generateComponents','numberComponents'), 'v', $installComponentDefsResult['componentsCount'],'v');
                $text .= Design::erstelleZeile($console, Sprachen::Get('generateComponents','numberLinks'), 'v', $installComponentDefsResult['linksCount'],'v');
            }

            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error); 
        }

        echo Design::erstelleBlock($console, Sprachen::Get('generateComponents','title'), $text);
    } else {

    }
}

if ($simple && isset($segmentKomponentenErstellen)){
    if ($installComponentDefs){
        $text = "<<< ".Sprachen::Get('generateComponents','title')." >>>\n";
        if (isset($installComponentDefsResult['components'])){
            $text .= Design::erstelleZeile($console, Sprachen::Get('generateComponents','numberComponents'), 'v', $installComponentDefsResult['componentsCount'],'v');
            $text .= Design::erstelleZeile($console, Sprachen::Get('generateComponents','numberLinks'), 'v', $installComponentDefsResult['linksCount'],'v');
        }
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
        echo $text;
    }
}

$segmentKomponentenErstellen = true;
#endregion Komponenten_erstellen