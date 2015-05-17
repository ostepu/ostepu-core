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
        $args = func_get_args();
        $data = array_shift($args);
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
        return array_pop($args);
    }
    
    public static function PlattformZusammenstellen($data)
    {   
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
                                            $data['PL']['urlExtern']
                                            );
        return $platform;
    }
    
    public static function GibServerDateien()
    {
        $serverFiles = array();
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        if ($handle = opendir(Einstellungen::$path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file=='.' || $file=='..') continue;
                $serverFiles[] = $file;
            }
            closedir($handle);
        }
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
    public static function read_all_files($root = '.'){ 
      $files  = array('files'=>array(), 'dirs'=>array()); 
      $directories  = array(); 
      $last_letter  = $root[strlen($root)-1]; 
      $root  = ($last_letter == '/') ? $root : $root.'/'; 
      
      $directories[]  = $root; 
      
      while (sizeof($directories)) { 
        $dir  = array_pop($directories); 
        if ($handle = opendir($dir)) { 
          while (false !== ($file = readdir($handle))) { 
            if ($file == '.' || $file == '..') { 
              continue; 
            } 
            $file  = $dir.$file; 
            if (is_dir($file)) { 
              $directory_path = $file.'/'; 
              array_push($directories, $directory_path); 
              $files['dirs'][]  = $directory_path; 
            } elseif (is_file($file)) { 
              $files['files'][]  = $file; 
            } 
          } 
          closedir($handle); 
        } 
      } 
      
      return $files; 
    } 
}