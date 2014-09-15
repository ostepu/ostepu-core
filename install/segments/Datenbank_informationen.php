<?php
#region Datenbank_informationen
if (!$simple)
    if ($selected_menu === 1){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('database_informations','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('database_informations','db_path'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost', true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('database_informations','db_name'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform', true), 'v');

        echo Design::erstelleBlock($simple, Sprachen::Get('database_informations','title'), $text);
            
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('databaseAdmin','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('databaseAdmin','db_user'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_user'], 'data[DB][db_user]', 'root', true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('databaseAdmin','db_passwd'), 'e', Design::erstellePasswortzeile($simple, $data['DB']['db_passwd'], 'data[DB][db_passwd]', ''), 'v');
                    
        echo Design::erstelleBlock($simple, Sprachen::Get('databaseAdmin','title'), $text);
            
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('databasePlatformUser','description')."</td></tr>";
        
        $text .= Design::erstelleZeile($simple, Sprachen::Get('databasePlatformUser','db_user_operator'), 'e', Design::erstelleEingabezeile($simple, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('databasePlatformUser','db_passwd_operator'), 'e', Design::erstellePasswortzeile($simple, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', ''), 'v');

        echo Design::erstelleBlock($simple, Sprachen::Get('databasePlatformUser','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_passwd'], 'data[DB][db_passwd]', null);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_passwd_operator'], 'data[DB][db_passwd_operator]', null);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_operator'], 'data[DB][db_user_operator]', 'DBOperator',true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user'], 'data[DB][db_user]', 'root' ,true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_name'], 'data[DB][db_name]', 'uebungsplattform',true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_path'], 'data[DB][db_path]', 'localhost',true);
        echo $text;
    }
#endregion Datenbank_informationen
?>