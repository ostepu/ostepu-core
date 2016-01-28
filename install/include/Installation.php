<?php
require_once dirname(__FILE__) . '/../../UI/include/Authentication.php';
require_once dirname(__FILE__) . '/../../Assistants/Structures.php';
require_once dirname(__FILE__) . '/../../Assistants/Request.php';
require_once dirname(__FILE__) . '/../../Assistants/DBRequest.php';
require_once dirname(__FILE__) . '/../../Assistants/DBJson.php';

/**
 * @file Installation.php contains the Installation class
 *
 * @author Till Uhlig
 * @date 2014
 */

class Installation
{
    public static $logFile = null;

    public static $enableLogs = true;

    public static $logLevel = LogLevel::NONE;

   /**
     * Orders an array by given keys.
     *
     * This methods accepts multiple arguments. That means you can define more than one key.
     * e.g. orderby($data, 'key1', SORT_DESC, 'key2', SORT_ASC).
     *
     * @param array $data The array which will be sorted.
     * @param string $key The key of $data.
     * @param mixed $sortorder Either SORT_ASC to sort ascendingly or SORT_DESC to sort descendingly.
     *
     * @return array An array ordered by given parameters.
     */
    public static function orderBy()
    {
        Installation::log(array('text'=>'starte Funktion'));
        $args = func_get_args();
        $data = array_shift($args);
        if ($data === null) $data = array();
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        Installation::log(array('text'=>'beende Funktion'));
        return array_pop($args);
    }

    /*
     * @param array $components The array which will be filtered.
     * @param string $type '', 'RegEx'
     * @param string $root '', 'Command', 'ComponentName'
     */
    public static function filterComponents($components, $type, $root)
    {
        Installation::log(array('text'=>'starte Funktion'));
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function collectPlatformSettings($data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $settings = array();
        foreach(Einstellungen::$segments as $segs){
            if (!is_callable("{$segs}::platformSetting")) continue;
            $settings = array_merge($settings,$segs::platformSetting($data));
        }
        Installation::log(array('text'=>'beende Funktion'));
        return $settings;
    }

    /**
     * Extrahiert die relevanten Daten der Plattform und erzeugt
     * daraus ein Platform-Objekt
     *
     * @param string[][] $data Die Serverdaten
     * @return Patform Die Plattformdaten
     */
    public static function PlattformZusammenstellen($data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $settings = self::collectPlatformSettings($data);

        // hier aus den Daten ein Plattform-Objekt zusammenstellen
        $platform = Platform::createPlatform(
                                            $data['PL']['url'],
                                            $data['DB']['db_path'],
                                            $data['DB']['db_name'],
                                            null,
                                            null,
                                            $data['DB']['db_user_operator'],
                                            $data['DB']['db_passwd_operator'],
                                            $data['PL']['temp'],
                                            $data['PL']['files'],
                                            $data['PL']['urlExtern'],
                                            $settings
                                            );
        $tempPlatform = clone $platform;
        $tempPlatform->setDatabaseRootPassword('*****');
        $tempPlatform->setDatabaseOperatorPassword('*****');
        Installation::log(array('text'=>'Platform = '.json_encode($tempPlatform)));
        Installation::log(array('text'=>'beende Funktion'));
        return $platform;
    }

    /**
     * Ermittelt alle vorhandenen Serverkonfigurationsdateien
     * aus dem config Ordners
     *
     * @return string[] Die Dateipfade
     */
    public static function GibServerDateien()
    {
        Installation::log(array('text'=>'starte Funktion'));
        $serverFiles = array();
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        if (is_dir(Einstellungen::$path)) {
            Installation::log(array('text'=>'lese Konfigurationen'));
            if ($handle = opendir(Einstellungen::$path)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file=='.' || $file=='..') continue;
                    $serverFiles[] = $file;
                }
                closedir($handle);
            }
        } else {
            Installation::log(array('text'=>'der Pfad existiert nicht: '.Einstellungen::$path,'logLevel'=>LogLevel::ERROR));
            return array();
        }


        Installation::log(array('text'=>'ermittelte Konfigurationen = '.implode(',',$serverFiles)));
        Installation::log(array('text'=>'beende Funktion'));
        return $serverFiles;
    }

   /**
    * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively.
    * Will return an array of the form
    * array(
    *   'files' => [],
    *   'dirs'  => [],
    * )
    * @author sreekumar
    * @param string $root
    * @result array
    */
    public static function read_all_files($root = '.', $exclude = array())
    {
        
      Installation::log(array('text'=>'starte Funktion'));
      $files  = array('files'=>array(), 'dirs'=>array());
      $directories  = array();
      $root = realpath($root);
      
      foreach($exclude as &$ex){
          $ex = realpath($ex);
      }
      
      $last_letter  = $root[strlen($root)-1];
      $root  = ($last_letter == DIRECTORY_SEPARATOR) ? $root : $root.DIRECTORY_SEPARATOR;

      $directories[]  = $root;

      while (sizeof($directories)) {
        $dir  = array_pop($directories);
        
        if ($handle = opendir($dir)) {
          while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
              continue;
            }
            $file  = $dir.$file;
            if (!in_array($file, $exclude)){
                if (is_dir($file)) {
                  $directory_path = $file.DIRECTORY_SEPARATOR;
                  array_push($directories, $directory_path);
                  $files['dirs'][]  = $directory_path;
                } elseif (is_file($file)) {
                  $files['files'][]  = $file;
                }
            }
          }
          closedir($handle);
        }
      }

      Installation::log(array('text'=>'beende Funktion'));
      return $files;
    }

    public static function log($data = array())
    {
        if (!isset($data['text'])) $data['text'] = null;
        if (!isset($data['logLevel'])) $data['logLevel'] = LogLevel::INFO;

        if (!isset(Installation::$logFile)){
            $path = dirname(__FILE__) . '/../logs';
            Einstellungen::generatepath($path);
            Installation::$logFile = $path.'/install_'.date('Ymd_His').'.log';
        }


        if (!isset($data['name'])){
            $info = debug_backtrace();
            $infoString = '';
            if (isset($info[1])) {
                $callerInfo = $info[1];
                if (isset($callerInfo['class']))$infoString .= $callerInfo['class'];
                if (isset($callerInfo['type']))$infoString .= $callerInfo['type'];
                if (isset($callerInfo['function']))$infoString .= $callerInfo['function'];
            } else {
                $callerInfo = $info[0];
                if (isset($callerInfo['file']))$infoString .=  basename($callerInfo['file']);
            }

            if (isset($info[0]['line'])) {
                $infoString .= ':' . $info[0]['line'] . ')';
            } elseif (isset($info[1]['line'])) {
                $infoString .= ' (' . $info[1]['line'] . ')';
            }
            $data['name'] = $infoString;
        }

        Logger::Log($data['text'],$data['logLevel'],false,Installation::$logFile, LogLevel::$names[$data['logLevel']] . ','.$data['name'],false,Installation::$logLevel);
    }
    
    public static function Get($area, $cell, $name='default', $params=array())
    {  
        $value = Language::Get($area, $cell, $name, $params);
        if ($value === Language::$errorValue){
            Installation::log(array('text'=>Language::Get('main','unknownPlaceholder'),'logLevel'=>LogLevel::ERROR));
        }
        return $value;
    }
}
