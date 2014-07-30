<?php


/**
 * @file Einstellungen.php contains the Einstellungen class
 *
 * @author Till Uhlig
 * @date 2014
 */
 
Einstellungen::ladeEinstellungen();

class Einstellungen
{
    public static $konfiguration = array();
    
    public static function ladeEinstellungen()
    {
        if (file_exists('./config.ini')){
            $temp = file_get_contents('./config.ini');
            $temp = explode("\n",$temp);
            foreach ($temp as $element){
                $pos = strpos($element, '=');
                if ($pos === false || $pos === 0) 
                    continue;
                    
                Einstellungen::$konfiguration[substr($element,0,$pos)] = substr($element,$pos+1);
            }
        }
    }
    
    public static function speichereEinstellungen()
    {
        $filename = './config.ini';
        
        if (!$handle = @fopen($filename, "w")) {
            return;
        }

        foreach (Einstellungen::$konfiguration as $varName => $value){
            if (!fwrite($handle, $varName.'='.$value."\n")){
               fclose($handle);
                return;
            }
                
        }

        fclose($handle);
    }
    
    public static function Set($varName, $value){
        Einstellungen::$konfiguration[$varName] = $value;
    }
    
    public static function Get($varName, $default)
    {
        if (Einstellungen::$konfiguration != null && isset(Einstellungen::$konfiguration[$varName])){
            return Einstellungen::$konfiguration[$varName];
        } else
            return $default;
    }
    
    public static function GetValue($varName, &$CurrentValue){
        if (isset($CurrentValue))
            return;
        
        $val = Einstellungen::Get($varName, null);
        if ($val !== null)
            $CurrentValue = $val;
            
        return;
    }
}

?>