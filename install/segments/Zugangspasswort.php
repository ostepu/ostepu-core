<?php
#region Zugangspasswort
class Zugangspasswort
{
    public static $name = 'login';
    public static $enabledShow = true;
    private static $initialized=false;
    public static $rank = 125;
    
    public static function showInfoBar(&$data)
    {
        // master-Passwort abfragen
        echo "<tr><td class='e'>".Language::Get('main','masterPassword')."</td></tr>";
        $serverHash = md5(Einstellungen::$selected_server);
        echo "<tr><td class='v'>".Design::erstellePasswortzeile(false, Einstellungen::$masterPassword[$serverHash], 'tmp['.$serverHash.'][masterPassword]', Einstellungen::$masterPassword[$serverHash])."</td></tr>";
                
        if (!Einstellungen::$accessAllowed){
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('login','1',Language::Get('main','getAccess'))."</td></tr>";
        } else {
            if (trim(Einstellungen::$masterPassword[$serverHash])==''){
                echo "<tr><td class='error_light'>".Language::Get('main','emptyMasterPassword')."</td></tr>";
            }
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('changeMasterPassword','1',Language::Get('main','changeMasterPassword').">")."</td></tr>";
        }
    }
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        foreach (Einstellungen::$masterPassword as $key => $tm){
            if (trim($tm) != ''){
                echo Design::erstelleVersteckteEingabezeile($console, $tm, 'tmp['.$key.'][oldMasterPassword]', '', false);
            }
        }
        self::$initialized = true;
    }
    
}
#endregion Zugangspasswort