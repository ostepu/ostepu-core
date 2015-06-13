<?php


/**
 * @file Language.php contains the Language class
 *
 * @author Till Uhlig
 * @date 2014
 */

class Language
{
    public static $language = array();
    public static $selectedLanguage = null;
    public static $preferedLanguage = null;
    
    public static $defaultLanguage = array();
    public static $selectedDefaultLanguage = null;
    public static $default = 'de';
    
    public static function setPreferedLanguage($lang)
    {
        self::$preferedLanguage = $lang;
    }
    
    public static function loadLanguageFile($lang, $name='default', $type='json', $path='')
    {
        self::loadLanguage($lang, $name, $type, $path);
    }
    
    public static function loadLanguage($lang, $name='default', $type='json', $path='')
    {
        $nameAdd = ($name == 'default') ? '' : $name.'_';
            
        if (self::$selectedDefaultLanguage==null || self::$selectedDefaultLanguage != self::$default || !isset(self::$defaultLanguage[$name])){
            if (file_exists($path.'languages/'.$nameAdd.self::$default.'.'.$type)){
                if (!isset(self::$defaultLanguage[$name]))
                    self::$defaultLanguage[$name] = array();
                
                if ($type == 'ini'){
                    self::$defaultLanguage[$name] = parse_ini_file( 
                                              $path.'languages/'.$nameAdd.self::$default.'.'.$type,
                                              TRUE
                                              );
                    self::$selectedDefaultLanguage = self::$default;
                } elseif ($type == 'json') {
                    self::$defaultLanguage[$name] = json_decode( 
                                              file_get_contents($path.'languages/'.$nameAdd.self::$default.'.'.$type),
                                              TRUE
                                              );
                    self::$selectedDefaultLanguage = self::$default;
                }
            }
        }
    
        if (self::$selectedLanguage === $lang || $lang === null || isset(self::$language[$name])) return;
        if (self::$preferedLanguage !== null && file_exists($path.'languages/'.$nameAdd.self::$preferedLanguage.'.'.$type)){
            $lang = self::$preferedLanguage;
        }
        
        if (file_exists($path.'languages/'.$nameAdd.$lang.'.'.$type)){
            if (!isset(self::$language[$name]))
                self::$language[$name] = array();
            
            if ($type == 'ini'){
                self::$language[$name] = parse_ini_file( 
                                          $path.'languages/'.$nameAdd.$lang.'.'.$type,
                                          TRUE
                                          );
                self::$selectedLanguage = $lang;
            } elseif ($type == 'json') {
                self::$language[$name] = json_decode( 
                                          file_get_contents($path.'languages/'.$nameAdd.$lang.'.'.$type),
                                          TRUE
                                          );
                self::$selectedLanguage = $lang;
            }
            
        }
    }
    
    public static function Get($area, $cell, $name='default')
    {        
        if (self::$selectedLanguage != null && isset(self::$language[$name]) && isset(self::$language[$name][$area]) && isset(self::$language[$name][$area][$cell])){
            return self::$language[$name][$area][$cell];
        } elseif (self::$selectedDefaultLanguage != null && isset(self::$defaultLanguage[$name]) && isset(self::$defaultLanguage[$name][$area]) && isset(self::$defaultLanguage[$name][$area][$cell])){
            return self::$defaultLanguage[$name][$area][$cell];
        } else
            return '???';
    }
}
