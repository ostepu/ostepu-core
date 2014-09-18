<?php
#region Komponenten_erstellen
if (!$simple)
    if ($selected_menu === 3){
        $text='';
        $text .= "<tr><td colspan='2'>".''."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, 'Komponenten eintragen', 'e', '','v',Design::erstelleSubmitButton('actionInstallComponentDefs'), 'h');

        if ($installComponentDefs){        
            if (isset($installComponentDefsResult['components'])){

                $text .= Design::erstelleZeile($simple, 'Komponenten', 'v', $installComponentDefsResult['componentsCount'],'v');
                $text .= Design::erstelleZeile($simple, 'Verknüpfungen', 'v', $installComponentDefsResult['linksCount'],'v');
            }

            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        }

        echo Design::erstelleBlock($simple, 'Komponenten eintragen', $text);
    } else {

    }
#endregion Komponenten_erstellen
?>