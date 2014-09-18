<?php


/**
 * @file Einstellungen.php contains the Einstellungen class
 *
 * @author Till Uhlig
 * @date 2014
 */

class Einstellungen
{
    public static $konfiguration = array();
    public static $path = null;
    
    public static function NeuenServerAnlegen()
    {
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        $i=0;
        while (file_exists(Einstellungen::$path."/unbenannt({$i}).ini")){$i++;}
        return Einstellungen::$path."/unbenannt({$i}).ini";
    }
    
    public static function umbenennenEinstellungen(&$serverNameOld, $serverNameNew)
    {
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        rename(Einstellungen::$path.'/'.$serverNameOld.'.ini',Einstellungen::$path.'/'.$serverNameNew.'.ini');
        $serverNameOld = $serverNameNew;
    }
    
    public static function ladeEinstellungen($serverName)
    {
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        
        $konfiguration = array();
        if (file_exists(Einstellungen::$path.'/'.$serverName.".ini") && is_readable(Einstellungen::$path.'/'.$serverName.".ini")){
            $temp = file_get_contents(Einstellungen::$path.'/'.$serverName.".ini");
            $temp = explode("\n",$temp);
            foreach ($temp as $element){
                $pos = strpos($element, '=');
                if ($pos === false || $pos === 0) 
                    continue;
                    
                Einstellungen::$konfiguration[substr($element,0,$pos)] = substr($element,$pos+1);
            }
        }
    }
    
    public static function ladeEinstellungenDirekt($serverName)
    {
        $path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath($path);
        
        $konfiguration = array();
        $data = array();
        if (file_exists($path.'/'.$serverName.".ini") && is_readable($path.'/'.$serverName.".ini")){
            $temp = file_get_contents($path.'/'.$serverName.".ini");
            $temp = explode("\n",$temp);
            foreach ($temp as $element){
                $pos = strpos($element, '=');
                if ($pos === false || $pos === 0) 
                    continue;
                    
                $konfiguration[substr($element,0,$pos)] = substr($element,$pos+1);
            }
        }
        Variablen::EinsetzenDirekt($konfiguration,$data);
        return $data;
    }
    
    public static function speichereEinstellungen($serverName)
    {
        $filename = Einstellungen::$path."/../config/".$serverName.".ini";
        
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
    
    public static function Set($varName, $value)
    {
        Einstellungen::$konfiguration[$varName] = $value;
    }
    
    public static function Get($varName, $default)
    {
        if (Einstellungen::$konfiguration != null && isset(Einstellungen::$konfiguration[$varName])){
            return Einstellungen::$konfiguration[$varName];
        } else
            return $default;
    }
    
    public static function GetDirekt($konfiguration, $varName, $default)
    {
        if ($konfiguration != null && isset($konfiguration[$varName])){
            return $konfiguration[$varName];
        } else
            return $default;
    }
    
    public static function GetValue($varName, &$CurrentValue)
    {
        if (isset($CurrentValue)){
            Einstellungen::Set($varName, $CurrentValue);
            return;
        }
        
        $val = Einstellungen::Get($varName, null);
        if ($val !== null)
            $CurrentValue = $val;
            
        Einstellungen::Set($varName, $CurrentValue);
        return;
    }
    
    public static function GetValueDirekt($konfiguration, $varName, &$CurrentValue)
    {
        if (isset($CurrentValue)){
            return;
        }
        
        $val = Einstellungen::GetDirekt($konfiguration,$varName, null);
        if ($val !== null)
            $CurrentValue = $val;
    }
    
    /**
     * Creates the path, if necessary.
     *
     * @param string $path The path which should be created.
     * @see http://php.net/manual/de/function.mkdir.php#83265
     */
    public static function generatepath( $path, $mode = 0755 )
    {
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        $e = explode("/", ltrim($path, "/"));
        if(substr($path, 0, 1) == "/") {
            $e[0] = "/".$e[0];
        }
        $c = count($e);
        $cp = $e[0];
        for($i = 1; $i < $c; $i++) {
            if(!is_dir($cp) && !@mkdir($cp, $mode)) {
                return false;
            }
            $cp .= "/".$e[$i];
        }
        return @mkdir($path, $mode);
    }
}

?>