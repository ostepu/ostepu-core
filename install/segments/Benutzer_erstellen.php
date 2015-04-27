<?php
#region Benutzer_erstellen
if (!$simple)
    if ($selected_menu === 4){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('createSuperAdmin','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_user_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root'), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_passwd_insert'), 'e', Design::erstellePasswortzeile($simple, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', ''), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_first_name_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', ''), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_last_name_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', ''), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('createSuperAdmin','db_email_insert'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', ''), 'v', Design::erstelleSubmitButton("actionInstallSuperAdmin", Sprachen::Get('main','create')), 'h');

        if ($installSuperAdmin)
            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 

        echo Design::erstelleBlock($simple, Sprachen::Get('createSuperAdmin','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root',true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_first_name_insert'], 'data[DB][db_first_name_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_last_name_insert'], 'data[DB][db_last_name_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_email_insert'], 'data[DB][db_email_insert]', '',true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_passwd_insert'], 'data[DB][db_passwd_insert]', '');
        echo $text;
    }
#endregion Benutzer_erstellen