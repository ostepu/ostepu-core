<?php
#region Aktionsleiste
class Aktionsleiste
{
    public static $name = 'actionBar';
    public static $enabledShow = true;
    public static $rank = 150;
    private static $langTemplate='Aktionsleiste';

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
        if (Einstellungen::$accessAllowed){
            // Aktionen
            echo "<tr><td class='e'>".Installation::Get('main','actions',self::$langTemplate)."</td></tr>";

            // update-Button
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('update','OK',Installation::Get('main','simpleUpdate',self::$langTemplate).">")."</td></tr>";
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }
}
#endregion Aktionsleiste