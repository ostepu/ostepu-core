<?php
#region Serverliste
class Serverliste
{
    public static $name = 'servers';
    public static $enabledShow = true;
    public static $rank = 100;
    private static $langTemplate='Serverliste';

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function showInfoBar(&$data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        echo "<tr><td class='e'>".Installation::Get('serverList','serverList',self::$langTemplate)."</td></tr>";
        foreach(Einstellungen::$serverFiles as $serverFile){
            $file = pathinfo($serverFile)['filename'];
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('server',$file,(Einstellungen::$selected_server == $file ? '<font color="maroon">'.$file.'</font>' : $file))."</td></tr>";
        }

        if (Einstellungen::$accessAllowed){
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('actionAddServer','OK',Installation::Get('serverList','addServer',self::$langTemplate).">")."</td></tr>";
        }
        echo Design::erstelleVersteckteEingabezeile(false, Einstellungen::$selected_server, 'selected_server', null);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }
}
#endregion Serverliste