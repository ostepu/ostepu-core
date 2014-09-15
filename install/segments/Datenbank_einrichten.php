<?php
#region Datenbank_einrichten
if (!$simple)
    if ($selected_menu === 3){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('componentDefinitions','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('componentDefinitions','componentsSql'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['componentsSql'], 'data[DB][componentsSql]', '../DB/Components2.sql', true), 'v', Design::erstelleSubmitButton('actionInstallComponents'), 'h');
        if ($installComponentFile)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, Sprachen::Get('componentDefinitions','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['componentsSql'], 'data[DB][componentsSql]', '../DB/Components2.sql', true);
        echo $text;
    }
#endregion Datenbank_einrichten
?>