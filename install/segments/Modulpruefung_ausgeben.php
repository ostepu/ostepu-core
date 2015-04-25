<?php
#region Modulprüfung_ausgeben
if (!$simple)
    if ($selected_menu === 0){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('modules','description')."</td></tr>";

        if ($modules!=null){
            foreach ($modules as $moduleName => $status){
                $text .= Design::erstelleZeile($simple, $moduleName, 'e', ($status ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')."</font>"), 'v');
            }
        } else
            $text .= Design::erstelleZeile($simple, "<font color='red'>".Sprachen::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($simple, Sprachen::Get('modules','title'), $text);
    }
#endregion Modulprüfung_ausgeben