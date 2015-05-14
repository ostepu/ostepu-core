<?php
#region Benutzerschnittstelle_einrichten
if (!$console || !isset($segmentBenutzerschnittstelleEinrichten)){
    if ($selected_menu === 6 && isset($segmentBenutzerschnittstelleEinrichten)){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('userInterface','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($console, Sprachen::Get('userInterface','conf'), 'e', Design::erstelleEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true), 'v', Design::erstelleSubmitButton('actionInstallUIConf'), 'h');
        $text .= Design::erstelleZeile($console, Sprachen::Get('userInterface','siteKey'), 'e', Design::erstelleEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4', true), 'v');
        
        if ($installUiFile) 
            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($console, Sprachen::Get('userInterface','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4', true);
        echo $text;
    }
}

if ($simple && isset($segmentBenutzerschnittstelleEinrichten)){
    if ($installUiFile){
        $text = "<<< ".Sprachen::Get('userInterface','title')." >>>\n";
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
        echo $text;
    }
}

$segmentBenutzerschnittstelleEinrichten = true;
#endregion Benutzerschnittstelle_einrichten