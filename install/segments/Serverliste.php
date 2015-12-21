<?php
#region Serverliste
class Serverliste
{
    public static $name = 'servers';
    public static $enabledShow = true;
    public static $rank = 100;

    public static function showInfoBar(&$data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        echo "<tr><td class='e'>".Language::Get('main','serverList')."</td></tr>";
        foreach(Einstellungen::$serverFiles as $serverFile){
            $file = pathinfo($serverFile)['filename'];
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('server',$file,(Einstellungen::$selected_server == $file ? '<font color="maroon">'.$file.'</font>' : $file))."</td></tr>";
        }

        if (Einstellungen::$accessAllowed){
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('actionAddServer','OK',Language::Get('main','addServer').">")."</td></tr>";
        }
        echo Design::erstelleVersteckteEingabezeile(false, Einstellungen::$selected_server, 'selected_server', null);
        Installation::log(array('text'=>'beende Funktion'));
    }
}
#endregion Serverliste