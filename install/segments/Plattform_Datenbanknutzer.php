<?php
#region Plattform_Datenbanknutzer
if (!$simple)
    if ($selected_menu === 2){
        $text='';        
        $text .= "<tr><td colspan='2'>".Sprachen::Get('createDatabasePlatformUser','description')."</td></tr>";            
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createDatabasePlatformUser','db_user_override_operator'), 'e', Design::erstelleAuswahl($simple, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', 'override', null, true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createDatabasePlatformUser','createUser'), 'e', '', 'v', Design::erstelleSubmitButton("actionInstallDBOperator", Sprachen::Get('main','create')), 'h');
        
        if ($installDBOperator)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, Sprachen::Get('createDatabasePlatformUser','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', null, true);
        echo $text;
    }
#endregion