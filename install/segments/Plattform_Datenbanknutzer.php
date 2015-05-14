<?php
#region Plattform_Datenbanknutzer
if (!$console || !isset($segmentPlattformDatenbanknutzer)){
    if ($selected_menu === 2 && isset($segmentPlattformDatenbanknutzer)){
        $text='';        
        $text .= "<tr><td colspan='2'>".Sprachen::Get('createDatabasePlatformUser','description')."</td></tr>";            
        $text .= Design::erstelleZeile($console, Sprachen::Get('createDatabasePlatformUser','db_user_override_operator'), 'e', Design::erstelleAuswahl($console, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', 'override', null, true), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('createDatabasePlatformUser','createUser'), 'e', '', 'v', Design::erstelleSubmitButton("actionInstallDBOperator", Sprachen::Get('main','create')), 'h');
        
        if ($installDBOperator)
            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($console, Sprachen::Get('createDatabasePlatformUser','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_override_operator'], 'data[DB][db_user_override_operator]', null, true);
        echo $text;
    }
}

if ($simple && isset($segmentPlattformDatenbanknutzer)){
    if ($installDBOperator){
        $text = "<<< ".Sprachen::Get('createDatabasePlatformUser','title')." >>>\n";
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
        echo $text;
    }
}

$segmentPlattformDatenbanknutzer = true;
#endregion