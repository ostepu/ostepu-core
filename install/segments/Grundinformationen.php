<?php
#region Grundinformationen
if (!$console || !isset($segmentGrundinformationen)){
    if ($selected_menu === 1 && isset($segmentGrundinformationen)){
        $text = '';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('general_informations','description')."</td></tr>";
         
        $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','server_name'), 'e', Design::erstelleEingabezeile($console, $server, 'data[SV][name]', $server, false), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','url'), 'e', Design::erstelleEingabezeile($console, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','urlExtern'), 'e', Design::erstelleEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://MyURL.de/uebungsplattform', true), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','temp'), 'e', Design::erstelleEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('general_informations','files'), 'e', Design::erstelleEingabezeile($console, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true), 'v');

        echo Design::erstelleBlock($console, Sprachen::Get('general_informations','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://MyURL.de/uebungsplattform', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true);
        echo $text;
    }
}

if ($simple && isset($segmentGrundinformationen)){
    // leer
}

$segmentGrundinformationen = true;
#endregion Grundinformationen