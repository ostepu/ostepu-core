<?php
#region Benutzerschnittstelle_einrichten
if (!$simple)
    if ($selected_menu === 6){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('userInterface','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('userInterface','conf'), 'e', Design::erstelleEingabezeile($simple, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true), 'v', Design::erstelleSubmitButton('actionInstallUIConf'), 'h');

        if ($installUiFile) 
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, Sprachen::Get('userInterface','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true);
        echo $text;
    }
#endregion Benutzerschnittstelle_einrichten
?>