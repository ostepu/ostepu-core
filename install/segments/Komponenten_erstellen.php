<?php
#region Komponenten_erstellen
if (!$simple)
    if ($selected_menu === 3){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('generateComponents','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('generateComponents','generateComponents'), 'e', '','v',Design::erstelleSubmitButton('actionInstallComponentDefs'), 'h');

        if ($installComponentDefs){        
            if (isset($installComponentDefsResult['components'])){

                $text .= Design::erstelleZeile($simple, Sprachen::Get('generateComponents','numberComponents'), 'v', $installComponentDefsResult['componentsCount'],'v');
                $text .= Design::erstelleZeile($simple, Sprachen::Get('generateComponents','numberLinks'), 'v', $installComponentDefsResult['linksCount'],'v');
            }

            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        }

        echo Design::erstelleBlock($simple, Sprachen::Get('generateComponents','title'), $text);
    } else {

    }
#endregion Komponenten_erstellen