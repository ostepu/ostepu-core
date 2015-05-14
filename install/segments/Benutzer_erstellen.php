<?php
#region Benutzer_erstellen
if (!$console || !isset($segmentBenutzerErstellen)){
    if ($selected_menu === 4 && isset($segmentBenutzerErstellen)){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('createSuperAdmin','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_user_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root'), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_passwd_insert'), 'e', Design::erstellePasswortzeile($console, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', ''), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_first_name_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', ''), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_last_name_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', ''), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('createSuperAdmin','db_email_insert'), 'e', Design::erstelleEingabezeile($console, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', ''), 'v', Design::erstelleSubmitButton("actionInstallSuperAdmin", Sprachen::Get('main','create')), 'h');

        if ($installSuperAdmin)
            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($console, Sprachen::Get('createSuperAdmin','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', '');
        echo $text;
    }
}

if ($simple && isset($segmentBenutzerErstellen)){
    if ($installSuperAdmin){
        $text = "<<< ".Sprachen::Get('createSuperAdmin','title')." >>>\n";
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
    }        
}

$segmentBenutzerErstellen = true;
#endregion Benutzer_erstellen