<?php
#region Prüfung_der_Erweiterungen_ausgeben
if (!$console || !isset($segmentPruefungErweiterungen)){
    if ($selected_menu === 0 && isset($segmentPruefungErweiterungen)){

        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('extensions','description')."</td></tr>";

        if ($extensions!=null){
            foreach ($extensions as $extensionName => $status){
                $text .= Design::erstelleZeile($console, $extensionName, 'e', ($status ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')."</font>"), 'v');
            }
        } else 
            $text .= Design::erstelleZeile($console, "<font color='red'>".Sprachen::Get('main','fail')."</font>", 'e');

        echo Design::erstelleBlock($console, Sprachen::Get('extensions','title'), $text);
    }
}

if ($simple && isset($segmentPruefungErweiterungen)){
    $text = "<<< ".Sprachen::Get('extensions','title')." >>>\n";
    if ($extensions!=null){
        foreach ($extensions as $extensionName => $status){
            $text .= Design::erstelleZeile($console, $extensionName, 'e', ($status ? Sprachen::Get('main','ok') : Sprachen::Get('main','fail')), 'v');
        }
    } else 
        $text .= Design::erstelleZeile($console, Sprachen::Get('main','fail'), 'e');
    $text .= "\n";
    echo $text;
}

$segmentPruefungErweiterungen = true;
#endregion Prüfung_der_Erweiterungen_ausgeben