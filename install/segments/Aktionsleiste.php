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
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Language::Get('main','languageInstantiated')));
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }
    
    public static function showInfoBar(&$data)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        if (Einstellungen::$accessAllowed){
            // Aktionen
            echo "<tr><td class='e'>".Language::Get('main','actions',self::$langTemplate)."</td></tr>";

            // update-Button
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('update','OK',Language::Get('main','simpleUpdate',self::$langTemplate).">")."</td></tr>";
        }
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }
}
#endregion Aktionsleiste