<?php


/**
 * @file Einstellungen.php contains the Einstellungen class
 *
 * @author Till Uhlig
 * @date 2014
 */
 
include_once ( dirname( __FILE__ ) . '/../../Assistants/Structures.php' );
require_once( dirname(__FILE__) . '/../../Assistants/CConfig.php' );

class Einstellungen
{                            
    /**
     * @var string[] $konfiguration Die globalen Konfigurationsdaten
     */
    public static $konfiguration = array();     
    
    /**
     * @var string $path Der Pfad der Konfigurationsdatei
     */
    public static $path = null;     
    
    /**
     * @var bool $accessAllowed true = Zugang gewährt, false = Zugang verboten
     * wird beim Master-Passwort verwendet
     */
    public static $accessAllowed = false;
    
    /**
     * @var bool $masterPassword Das Zugangspasswort
     */
    public static $masterPassword = array();
    
    /**
     * @var string $selected_server Der Name des ausgewählten Servers/ der Konfigurationsdatei
     */
    public static $selected_server = null;
    
    /**
     * @var string[] $serverFiles Eine Liste der Pfade der Konfigurationsdateien
     */
    public static $serverFiles = null; 
    
    /**
     * @var Component $config Die Konfiguration der CInstall
     */
    public static $config = null; 
    
    /**
     * @var string[] $segment Enthält die Bezeichner der Segmente (Seiteninhalte)
     */
    public static $segments = array();
   
    /**
     * Ermittelt alle mit der CInstall am Ausgang $name verknüpften Komponenten
     *
     * @param string $name Der Name des Ausgangs, dessen Ziele gesucht ermittelt werden sollen
     * @return link[] Eine Liste der Komponenten an diesem Ausgang
     */
    public static function getLinks($name)
    {
        if (self::$config === null){
            self::$config=CConfig::loadStaticConfig('','',dirname(__FILE__),'/../component/cinstall_cconfig.json');
        }
        
        return CConfig::getLinks(self::$config->getLinks(),$name);
    }
    
    /**
     * Erzeugt eine neue Konfigurationsdatei
     *
     * @return string Der Pfad der neuen Konfigurationsdatei
     */
    public static function NeuenServerAnlegen()
    {
        // wenn der config Pfad noch nicht existiert, wird er erzeugt
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        
        // nun wird so lange gesucht, bis ein nicht existierender Pfad gefunden wurde
        $i=0;
        while (file_exists(Einstellungen::$path."/unbenannt({$i}).ini")){$i++;}
        return Einstellungen::$path."/unbenannt({$i}).ini";
    }
    
    /**
     * Eine Konfiguration wird umbenannt
     *
     * @param string $serverNameOld Der Name der bisherigen Konfiguration
     * @param string $serverNameNew Der neue Name der Konfiguration
     */
    public static function umbenennenEinstellungen(&$serverNameOld, $serverNameNew)
    {
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        rename(Einstellungen::$path.'/'.$serverNameOld.'.ini',Einstellungen::$path.'/'.$serverNameNew.'.ini');
        $serverNameOld = $serverNameNew;
    }
    
    /**
     * Lädt die globale Konfiguration von $serverName
     *
     * @param string $serverName Der Name der Konfiguration
     * @param string[][] $data Die Serverdaten
     */
    public static function ladeEinstellungen($serverName, &$data)
    {
        ///$begin = microtime(true);
        $serverHash = md5($serverName);
        
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);

        Einstellungen::resetConf();
        Einstellungen::$accessAllowed = false;
        
        // erzeuge die Liste der Passwörter anhand des masterPasswort für die Verschlüsselung
        $keys = array();
        if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){
            $keys = self::makeKeys(self::$masterPassword[$serverHash]);
            $keys = array_reverse($keys,false);
        }

        if (file_exists(Einstellungen::$path.'/'.$serverName.".ini") && is_readable(Einstellungen::$path.'/'.$serverName.".ini")){
            $temp = file_get_contents(Einstellungen::$path.'/'.$serverName.".ini");
            $temp = explode("\n",$temp);
            foreach ($temp as $element){
                if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){                    
                    if (trim($element)=='') continue;
                    foreach ($keys as $key){
                        if ($key === '_BASE64'){
                            $add = 4-(strlen($element)%4);
                            if ($add == 4) $add = 0;
                            
                            $element = str_pad($element, strlen($element)+$add, '=', STR_PAD_RIGHT);
                            $element2 = @base64_decode($element,true);
                            
                            if ($element2===false) {
                                // die base64 dekodierung ist fehlgeschlagen
                                Einstellungen::$konfiguration = array();
                                return;
                            }
                            
                            $element = $element2;
                        } else {
                            $element = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $element, MCRYPT_MODE_ECB);
                        }
                    }
                } else {
                    if (trim($element)=='') continue;
                }
                
                $pos = strpos($element, '=');
                if ($pos === false || $pos === 0){
                    // es wurde kein gültiges '=' gefunden
                    Einstellungen::$konfiguration = array();
                    return;
                }
                $value = substr($element,$pos+1);                    
                $dat = @parse_ini_string('a='.$value);
                if ($dat === false){
                    // die Daten konnten nicht geparst werden
                    Einstellungen::$konfiguration = array();
                    return;
                }
                $dat = trim($dat['a']);
                    
                Einstellungen::$konfiguration[substr($element,0,$pos)] = $dat;
            }
        }
        
        if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){
            if (count(Einstellungen::$konfiguration) === 0){
                Einstellungen::$accessAllowed = true;
            } elseif (!isset(Einstellungen::$konfiguration['data[SV][hash]']) && count(Einstellungen::$konfiguration)>0){
                Einstellungen::$accessAllowed = false;
            } else {
                $existingHash = Einstellungen::$konfiguration['data[SV][hash]'];
                unset(Einstellungen::$konfiguration['data[SV][hash]']);
                $hash = self::makeHash(Einstellungen::$konfiguration);
                if ($existingHash == $hash){
                    Einstellungen::$accessAllowed = true;
                } else {
                    Einstellungen::$accessAllowed = false;
                }
            }
        } else {
            Einstellungen::$accessAllowed = true;
        }
        
        ///echo "Ladezeit: ".(round((microtime(true) - $begin)*1000,2)). 'ms<br>';
    }
    
    /**
     * Ermittelt eine Liste aller Standardwerte der Segmente
     *
     * @return string[] Die Standardwerte der Segmente
     */
    public static function getAllDefaults()
    {
        $defaults = array();
        foreach(Einstellungen::$segments as $segs){
            if (!is_callable("{$segs}::getDefaults")) continue;
            $def = $segs::getDefaults();
            if (count($def)>0){
                foreach ($def as $key => $values){
                    if (!isset($values[0]) || !isset($values[1])) continue;
                    $defaults[$values[0]] = $values[1];
                }
            }
        }  
        return $defaults;
    }
    
    /**
     * Wandelt eine Liste der Form $conf['data[ab][c]'] = '12' in
     * $conf[ab][c] = '12' um
     *
     * @param string[] $conf Die Liste, welche durchsucht und aufgespalten werden soll
     * @param string $name Der Präfix der Werte, nach denen gesucht werden soll (Bsp. 'Data')
     * @return string[][] Die aufgespaltene Liste
     */
    public static function extractData($conf, $name)
    {
        $res = array();
        foreach($conf as $key => $value){
            if (strpos($key,$name.'[') === false) continue;
            $key = substr($key,strlen($name));
            $matches = array();
            preg_match_all("/\[([\w]+)\]/",$key,$matches);
            if (!isset($matches[1][0]) || !isset($matches[1][1])) continue;
            if (!isset($res[$matches[1][0]])) $res[$matches[1][0]] = array();
            $res[$matches[1][0]][$matches[1][1]] = $value;
        }
        return $res;
    }
    
    /**
     * Lädt die Konfiguration von $serverName und gibt sie direkt zurück
     * Achtung: wird nicht der globalen Konfiguration zugewiesen
     *
     * @param string $serverName Der Name der Konfiguration
     * @param string[][] $data Die Serverdaten
     * @return string[][] Die Konfiguration, null im Fehlerfall
     */
    public static function ladeEinstellungenDirekt($serverName, $data)
    {
        $serverHash = md5($serverName);
        $path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath($path);

        // erzeuge die Liste der Passwörter anhand des masterPasswort für die Verschlüsselung
        $keys = array();
        if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){
            $keys = self::makeKeys(self::$masterPassword[$serverHash]);
            $keys = array_reverse($keys,false);
        }
        
        $default = self::getAllDefaults();
        $konfiguration = array();
        $data = array();
        if (file_exists($path.'/'.$serverName.".ini") && is_readable($path.'/'.$serverName.".ini")){
            $temp = file_get_contents($path.'/'.$serverName.".ini");
            $temp = explode("\n",$temp);
            foreach ($temp as $element){
                if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){ 
                    if (trim($element)=='') continue;
                    foreach ($keys as $key){
                        if ($key === '_BASE64'){
                            $add = 4-(strlen($element)%4);
                            if ($add == 4) $add = 0;
                            
                            $element = str_pad($element, strlen($element)+$add, '=', STR_PAD_RIGHT);
                            $element2 = @base64_decode($element,true);
                            
                            if ($element2===false) {
                                // die base64 dekodierung ist fehlgeschlagen
                                return null;
                            }
                            
                            $element = $element2;
                        } else {
                            $element = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $element, MCRYPT_MODE_ECB);
                        }
                    }
                } else {
                    if (trim($element)=='') continue;
                }
                
                $pos = strpos($element, '=');
                if ($pos === false || $pos === 0){
                    return null;
                }
                    
                $dat = @parse_ini_string('a='.substr($element,$pos+1));
                
                if ($dat === false){
                    return null;
                }
                $dat = $dat['a'];
                $dat = trim($dat);
                
                $konfiguration[substr($element,0,$pos)] = $dat;
            }
        }
        
        if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){
            if (count($konfiguration) === 0){
                // leer
            } elseif (!isset($konfiguration['data[SV][hash]']) && count($konfiguration)>0){
                return null;
            } else {
                $existingHash = $konfiguration['data[SV][hash]'];
                unset($konfiguration['data[SV][hash]']);
                $hash = self::makeHash($konfiguration);
                if ($existingHash == $hash){
                    // leer
                } else {
                    return null;
                }
            }
        } else {
            // leer
        }
        
        $konfiguration = self::extractData($konfiguration,'data');
        $default = self::extractData($default,'data');
        
        ///////////////////////////        
        if (isset($_POST['update'])) 
            $_POST['action'] = 'update';
        
        if (isset($_POST['actionInstall'])) 
            $_POST['action'] = 'install';
        
        if (isset($_POST['actionUpdate'])) 
            $_POST['action'] = 'update';
        
        if (!isset($konfiguration['PL']['language']))
            $konfiguration['PL']['language'] = 'de';
            
        if (!isset($konfiguration['PL']['init']))
            $konfiguration['PL']['init'] = 'DB/CControl';
            
        if (isset($konfiguration['PL']['url'])) $konfiguration['PL']['url'] = rtrim($konfiguration['PL']['url'], '/');
        if (isset($konfiguration['PL']['urlExtern'])) $konfiguration['PL']['urlExtern'] = rtrim($konfiguration['PL']['urlExtern'], '/');
        if (isset($konfiguration['PL']['temp'])) $konfiguration['PL']['temp'] = rtrim($konfiguration['PL']['temp'], '/');
        if (isset($konfiguration['PL']['files'])) $konfiguration['PL']['files'] = rtrim($konfiguration['PL']['files'], '/');
        if (isset($konfiguration['PL']['init'])) $konfiguration['PL']['init'] = rtrim($konfiguration['PL']['init'], '/');
        ///////////////////////////
        
        $konfiguration = array_merge($default, $konfiguration);
        return $konfiguration;
    }

    /**
     * Erstellt einen Kodier-/Dekodierablauf zu einem gegebenen Schlüssel
     *
     * @param string $key Ein Passwort
     * @return string[] Der Ablauf, welcher für den Schlüssel entwurfen wurde
     */
    public static function makeKeys($key)
    {
        for ($i=0;$i<10;$i++){
            $key = hash('sha512', $key, true);
        }
        
        $keys = array();
        $A = str_split($key, 32);
        
        $keys = array_merge($keys, $A);
        $keys = array_merge($keys, $keys);
        $keys = array_merge($keys, $keys);
        $keys = array_merge($keys, array('_BASE64'));
        return $keys;
    }
    
    /**
     * Erzeugt einen Hash für ein assoc Array
     *
     * @param string[] $data Die Eingabedaten
     * @return string Der resultierende Hash
     */
    public static function makeHash($data)
    {
        $content = '';
        foreach ($data as $key => $value){
            $content.=$key.'_'.trim($value);
        }
        return md5($content);
    }
    
    /**
     * Speichert die aktuelle Konfiguration
     *
     * @param string $servername Der Servername für den Speicherort
     * @param string[][] $data Die Serverdaten
     */
    public static function speichereEinstellungen($serverName, $data)
    {
        $serverHash = md5($serverName);
        
        if (!Einstellungen::$accessAllowed) return;
        
        $filename = Einstellungen::$path."/../config/".$serverName.".ini";
        
        if (!$handle = @fopen($filename, "w")) {
            return;
        }

        // mischt die Einträge der Konfiguration
        /*$tmp_keys = array_keys(Einstellungen::$konfiguration);
        $tmp_new = array();
        shuffle($tmp_keys);
        foreach($tmp_keys as $key) {
            $tmp_new[$key] = Einstellungen::$konfiguration[$key];
        }
        Einstellungen::$konfiguration = $tmp_new;*/
                
        // hänge den Hash der Daten an
        if (isset(Einstellungen::$konfiguration['data[SV][hash]'])) unset(Einstellungen::$konfiguration['data[SV][hash]']);
        Einstellungen::$konfiguration['data[SV][hash]'] = self::makeHash(Einstellungen::$konfiguration);
        
        // erzeuge die Ablaufliste für das Verschlüsseln anhand des masterPasswort für die Verschlüsselung
        $keys = array();
        if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){
            $keys = self::makeKeys(self::$masterPassword[$serverHash]);
        }
        
        // ab hier werden die Konfigurationsdaten in die Zieldatei geschrieben
        foreach (Einstellungen::$konfiguration as $varName => $value){
            $write = str_replace(array("\\","\""),array("\\\\","\\\""),$value);
            $line = $varName.'="'.$write.'"';

            if (isset(self::$masterPassword[$serverHash]) && trim(self::$masterPassword[$serverHash]) != ''){
                foreach ($keys as $key){
                    if ($key === '_BASE64'){
                        $line = base64_encode($line);
                        $line = trim($line, "=");
                    } else {
                        $line = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $line, MCRYPT_MODE_ECB);
                    }
                }
            }
            
            if (!fwrite($handle, $line."\n")){
               fclose($handle);
                return;
            }
        }

        fclose($handle);
    }
    
    /**
     * Setzt den Wert einer Variablen
     *
     * @param string $varName Der Name der Variablen
     * @param mixed $value Der neue Wert
     */
    public static function Set($varName, $value)
    {
        Einstellungen::$konfiguration[$varName] = $value;
    }
    
    /**
     * Liefert den Wert einer Variablen
     *
     * @param string $varName Der Name der Variablen
     * @param mixed $default Der Standardwert, falls die Variable nicht gesetzt oder noch unbekannt ist
     * @return mixed Der Wert der Variablen
     */
    public static function Get($varName, $default)
    {
        if (Einstellungen::$konfiguration != null && isset(Einstellungen::$konfiguration[$varName])){
            return Einstellungen::$konfiguration[$varName];
        } else
            return $default;
    }
    
    /**
     * Liefert den Wert einer Variablen aus $konfiguration
     *
     * @param mixed[] $konfiguration Eine Konfiguration
     * @param string $varName Der Name der Variablen
     * @param mixed $default Der Standardwert, falls die Variable nicht gesetzt oder noch unbekannt ist
     * @return mixed Der Wert der Variablen
     */
    public static function GetDirekt($konfiguration, $varName, $default)
    {
        if ($konfiguration != null && isset($konfiguration[$varName])){
            return $konfiguration[$varName];
        } else
            return $default;
    }
    
    /**
     * Weist den Wert $CurrentValue zu oder lädt dessen Wert
     *
     * @param string $varName Der Variablenname
     * @param mixed $CurrentValue Der aktuelle Wert der Variablen oder null
     */
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
    
    /**
     * Weist den Wert $CurrentValue zu oder lädt dessen Wert
     *
     * @param mixed[] $konfiguration Eine Konfiguration
     * @param string $varName Der Variablenname
     * @param mixed $CurrentValue Der aktuelle Wert der Variablen oder null
     */
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
     * Setzt die globalen Konfigurationsdaten auf den Ursprungszustand zurück
     */
    public static function resetConf()
    {
        Einstellungen::$konfiguration=array();
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
