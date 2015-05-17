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
    public static $accessAllowed = true;
    
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
    
    public static function ladeEinstellungen($serverName, &$data)
    {
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);

        Einstellungen::$konfiguration = array();

        ///$pass = $data['P']['masterPassword'];
        if (file_exists(Einstellungen::$path.'/'.$serverName.".ini") && is_readable(Einstellungen::$path.'/'.$serverName.".ini")){
            $temp = file_get_contents(Einstellungen::$path.'/'.$serverName.".ini");
            $temp = explode("\n",$temp);
            foreach ($temp as $element){
                $pos = strpos($element, '=');
                if ($pos === false || $pos === 0) 
                    continue;
                    
                $dat = parse_ini_string('a='.substr($element,$pos+1))['a'];
                /*$dat = base64_decode($dat,true);
                if ($dat===false) {
                    $dat = '???';
                } else
                    $dat = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$pass,$dat,MCRYPT_MODE_ECB,''),"\x00..\x1F");*/
                    
                //echo substr($element,0,$pos).'='.$dat."<br>";
                
               /// if ('data[P][masterPassword]'!=substr($element,0,$pos))
                    Einstellungen::$konfiguration[substr($element,0,$pos)] = $dat;
            }
        }
        
        /*echo $serverName.'__'.(isset($data['SV']['name'])?$data['SV']['name']:'').'__'.(isset(Einstellungen::$konfiguration['data[SV][name]'])?Einstellungen::$konfiguration['data[SV][name]']:'').'<br>';
        echo strlen($serverName).'__'.strlen((isset($data['SV']['name'])?$data['SV']['name']:'')).'__'.strlen((isset(Einstellungen::$konfiguration['data[SV][name]'])?Einstellungen::$konfiguration['data[SV][name]']:'')).'<br>';
        
        if (!isset(Einstellungen::$konfiguration['data[SV][name]']) && !isset($data['SV']['name'])){
            Variablen::Zuruecksetzen($data);
            Einstellungen::$konfiguration=array();
            Einstellungen::$accessAllowed = true;
            echo "fail2<br>";
        } elseif ($serverName != (isset(Einstellungen::$konfiguration['data[SV][name]'])?Einstellungen::$konfiguration['data[SV][name]']:'') && $serverName != (isset($data['SV']['name'])?$data['SV']['name']:'')){
            // daten zurücksetzen
            foreach(Einstellungen::$konfiguration as $key => $dat)
                Einstellungen::$konfiguration[$key] = '???';
            echo "fail<br>";
            //Einstellungen::$konfiguration=array();
            Variablen::Zuruecksetzen($data);
            Einstellungen::$accessAllowed = false;
        } else{echo "fail4<br>";
            Einstellungen::$accessAllowed = true;
        }*/
    }
    
    public static function ladeEinstellungenDirekt($serverName, $data)
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
                    
                $dat = parse_ini_string('a='.substr($element,$pos+1))['a'];
                /*$dat = base64_decode($dat,true);
                if ($dat===false) {
                    $dat = '???';
                } else
                    $dat = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$data['P']['masterPassword'],$dat,MCRYPT_MODE_ECB,'');*/
                
                $konfiguration[substr($element,0,$pos)] = $dat;
            }
        }
        
        if ($serverName != $konfiguration['data[SV][name]']){
            // daten zurücksetzen
            foreach($konfiguration as $key => $dat)
                $konfiguration[$key] = '???';
            
            Variablen::Zuruecksetzen($data);
            $accessAllowed = false;
        } else
            Variablen::EinsetzenDirekt($konfiguration,$data);
        
        ///////////////////////////
        $data['P']['masterPassword'] = (isset($data['P']['masterPassword']) ? $data['P']['masterPassword'] : '');
        
        if (isset($_POST['update'])) 
            $_POST['action'] = 'update';
        
        if (isset($_POST['actionInstall'])) 
            $_POST['action'] = 'install';
        
        if (isset($_POST['actionUpdate'])) 
            $_POST['action'] = 'update';
        
        if (!isset($data['PL']['language']))
            $data['PL']['language'] = 'de';
            
        if (!isset($data['PL']['init']))
            $data['PL']['init'] = 'DB/CControl';
            
        if (isset($data['PL']['url'])) $data['PL']['url'] = rtrim($data['PL']['url'], '/');
        if (isset($data['PL']['urlExtern'])) $data['PL']['urlExtern'] = rtrim($data['PL']['urlExtern'], '/');
        if (isset($data['PL']['temp'])) $data['PL']['temp'] = rtrim($data['PL']['temp'], '/');
        if (isset($data['PL']['files'])) $data['PL']['files'] = rtrim($data['PL']['files'], '/');
        if (isset($data['PL']['init'])) $data['PL']['init'] = rtrim($data['PL']['init'], '/');
        ///////////////////////////
        
        return $data;
    }
    
    public static function speichereEinstellungen($serverName, $data)
    {
        ///if (!Einstellungen::$accessAllowed) return;
        
        $filename = Einstellungen::$path."/../config/".$serverName.".ini";
        
        if (!$handle = @fopen($filename, "w")) {
            return;
        }
///echo "saved<br>";
        foreach (Einstellungen::$konfiguration as $varName => $value){
            if ('data[P][masterPassword]'==$varName) continue;
            
            $write = str_replace(array("\\","\""),array("\\\\","\\\""),$value);
            ///$write = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$data['P']['masterPassword'],$write,MCRYPT_MODE_ECB,''));
            if (!fwrite($handle, $varName.'="'.$write."\"\n")){
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
