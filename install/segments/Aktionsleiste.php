<?php
#region Aktionsleiste
class Aktionsleiste
{
    public static $name = 'actionBar';
    public static $enabledShow = true;
    
    public static function showInfoBar(&$data)
    {
        if (Einstellungen::$accessAllowed){
            // Aktionen
            echo "<tr><td class='e'>".Language::Get('main','actions')."</td></tr>";
        
            // update-Button
            echo "<tr><td class='v'>".Design::erstelleSubmitButtonFlach('update','OK',Language::Get('main','simpleUpdate').">")."</td></tr>";
        }
    }
}
#endregion Aktionsleiste