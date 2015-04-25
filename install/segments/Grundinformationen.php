<?php
#region Grundinformationen
if (!$simple)
    if ($selected_menu === 1){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('general_informations','description')."</td></tr>";
         
        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','server_name'), 'e', Design::erstelleEingabezeile($simple, $server, 'data[SV][name]', $server, false), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','url'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','urlExtern'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://MyURL.de/uebungsplattform', true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','temp'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true), 'v');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('general_informations','files'), 'e', Design::erstelleEingabezeile($simple, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true), 'v');

        echo Design::erstelleBlock($simple, Sprachen::Get('general_informations','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://MyURL.de/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true);
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true);
        echo $text;
    }
#endregion Grundinformationen