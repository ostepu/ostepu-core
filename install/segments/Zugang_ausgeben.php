<?php
#region Zugang_ausgeben
if (!$console || !isset($segmentZugang)){
    if ($selected_menu === 5 && isset($segmentZugang)){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('access','description')."</td></tr>";

        $text .= Design::erstelleZeile($console, Sprachen::Get('access','local'), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'local', 'local', true), 'v');
        
        $text .= Design::erstelleZeile($console, '&nbsp;', '', '', '');
        $text .= Design::erstelleZeile($console, Sprachen::Get('access','ssh'), 'e', Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'ssh', null, true), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('access','username'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_login'], 'data[ZV][zv_ssh_login]', 'root'), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('access','address'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_address'], 'data[ZV][zv_ssh_address]', 'localhost'), 'v');
        $text .= Design::erstelleZeile($console, Sprachen::Get('access','password'), 'e', Design::erstellePasswortzeile($console, $data['ZV']['zv_ssh_password'], 'data[ZV][zv_ssh_password]', ''), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'passwd', 'passwd', true),'h');
        $text .= Design::erstelleZeile($console, Sprachen::Get('access','keyFile'), 'e', Design::erstelleEingabezeile($console, $data['ZV']['zv_ssh_key_file'], 'data[ZV][zv_ssh_key_file]', '/var/public.ppk'), 'v',Design::erstelleGruppenAuswahl($console, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'keyFile', null, true),'h');

        echo Design::erstelleBlock($console, Sprachen::Get('access','title'), $text);
    } else {
        $text = '';
        //$text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root',true);
        echo $text;
    }
}

if ($simple && isset($segmentZugang)){
    // leer
}

$segmentZugang = true;
#endregion Zugang_ausgeben