<?php
#region Prüfung_der_Erweiterungen_ausgeben
if (!$simple)
    if ($selected_menu === 0){

        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('extensions','description')."</td></tr>";

        if ($extensions!=null){
            foreach ($extensions as $extensionName => $status){
                $text .= Design::erstelleZeile($simple, $extensionName, 'e', ($status ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')."</font>"), 'v');
            }
        } else 
            $text .= Design::erstelleZeile($simple, "<font color='red'>".Sprachen::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($simple, Sprachen::Get('extensions','title'), $text);
    }
#endregion Prüfung_der_Erweiterungen_ausgeben