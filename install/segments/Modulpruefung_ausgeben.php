<?php
#region Modulprüfung_ausgeben
if (!$console || !isset($segmentModulpruefung)){
    if ($selected_menu === 0 && isset($segmentModulpruefung)){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('modules','description')."</td></tr>";

        if ($modules!=null){
            foreach ($modules as $moduleName => $status){
                $text .= Design::erstelleZeile($console, $moduleName, 'e', ($status ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')."</font>"), 'v');
            }
        } else
            $text .= Design::erstelleZeile($console, "<font color='red'>".Sprachen::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($console, Sprachen::Get('modules','title'), $text);
    }
}

if ($simple && isset($segmentModulpruefung)){
    $text = "<<< ".Sprachen::Get('modules','title')." >>>\n";
    if ($modules!=null){
        foreach ($modules as $moduleName => $status){
            $text .= $moduleName.' '.($status ? Sprachen::Get('main','ok') : Sprachen::Get('main','fail'))."\n";
        }
    } else
        $text .= Sprachen::Get('main','fail')."\n";
    $text .= "\n";
    echo $text;
}

$segmentModulpruefung = true;
#endregion Modulprüfung_ausgeben