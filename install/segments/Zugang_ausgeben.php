<?php
#region Zugang_ausgeben
if (!$simple)
    if ($selected_menu === 5){
        $text='';
        $text .= "<tr><td colspan='2'>"."Zugang"."</td></tr>";

        $text .= Design::erstelleZeile($simple, "Lokal", 'e', Design::erstelleGruppenAuswahl($simple, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'local', 'local', true), 'v');
        
        $text .= Design::erstelleZeile($simple, '&nbsp;', '', '', '');
        $text .= Design::erstelleZeile($simple, "SSH", 'e', Design::erstelleGruppenAuswahl($simple, $data['ZV']['zv_type'], 'data[ZV][zv_type]', 'ssh', null, true), 'v');
        $text .= Design::erstelleZeile($simple, "Nutzername", 'e', Design::erstelleEingabezeile($simple, $data['ZV']['zv_ssh_login'], 'data[ZV][zv_ssh_login]', 'root'), 'v');
        $text .= Design::erstelleZeile($simple, "Adresse", 'e', Design::erstelleEingabezeile($simple, $data['ZV']['zv_ssh_address'], 'data[ZV][zv_ssh_address]', 'localhost'), 'v');
        $text .= Design::erstelleZeile($simple, "Passwort", 'e', Design::erstellePasswortzeile($simple, $data['ZV']['zv_ssh_password'], 'data[ZV][zv_ssh_password]', ''), 'v',Design::erstelleGruppenAuswahl($simple, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'passwd', 'passwd', true),'h');
        $text .= Design::erstelleZeile($simple, "Schlüsseldatei", 'e', Design::erstelleEingabezeile($simple, $data['ZV']['zv_ssh_key_file'], 'data[ZV][zv_ssh_key_file]', '/var/public.ppk'), 'v',Design::erstelleGruppenAuswahl($simple, $data['ZV']['zv_ssh_auth_type'], 'data[ZV][zv_ssh_auth_type]', 'keyFile', null, true),'h');

        echo Design::erstelleBlock($simple, "Zugang", $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['DB']['db_user_insert'], 'data[DB][db_user_insert]', 'root',true);
        echo $text;
    }
#endregion Zugang_ausgeben
?>