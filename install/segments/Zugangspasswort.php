<?php
#region Zugangspasswort
class Zugangspasswort
{
    public static $name = 'login';
    public static $enabledShow = true;
    private static $initialized=false;
    public static $rank = 125;
    private static $langTemplate='Zugangspasswort';

    public static function showInfoBar(&$data)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        // master-Passwort abfragen
        echo "<tr><td class='e'>".Language::Get('access','masterPassword',self::$langTemplate)."</td></tr>";
        $serverHash = md5(Einstellungen::$selected_server);
        echo "<tr><td class='v'>".Design::erstellePasswortzeile(false, Einstellungen::$masterPassword[$serverHash], 'tmp['.$serverHash.'][masterPassword]', Einstellungen::$masterPassword[$serverHash])."</td></tr>";

        if (!Einstellungen::$accessAllowed){
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('login','1',Language::Get('access','getAccess',self::$langTemplate))."</td></tr>";
        } else {
            if (trim(Einstellungen::$masterPassword[$serverHash])==''){
                echo "<tr><td class='error_light'>".Language::Get('access','emptyMasterPassword',self::$langTemplate)."</td></tr>";
            }
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('changeMasterPassword','1',Language::Get('access','changeMasterPassword',self::$langTemplate).">")."</td></tr>";
        }
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Language::Get('main','languageInstantiated')));
        
        foreach (Einstellungen::$masterPassword as $key => $tm){
            if (trim($tm) != ''){
                echo Design::erstelleVersteckteEingabezeile($console, $tm, 'tmp['.$key.'][oldMasterPassword]', '', false);
            }
        }
        self::$initialized = true;
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }
}
#endregion Zugangspasswort