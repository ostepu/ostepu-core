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
    
    public static $defaultLanguage = array();
    public static $selectedDefaultLanguage = null;
    public static $default = 'de';
    
    public static function loadLanguageFile($lang, $name='default', $type='json', $path='')
    {
        self::loadLanguage($lang, $name, $type, $path);
    }
    
    public static function loadLanguage($lang, $name='default', $type='json', $path='')
    {
        $nameAdd = ($name == 'default') ? '' : $name.'_';
            
        if (Language::$selectedDefaultLanguage==null || Language::$selectedDefaultLanguage != Language::$default || !isset(Language::$defaultLanguage[$name])){
            if (file_exists($path.'languages/'.$nameAdd.Language::$default.'.'.$type)){
                if (!isset(Language::$defaultLanguage[$name]))
                    Language::$defaultLanguage[$name] = array();
                
                if ($type == 'ini'){
                    Language::$defaultLanguage[$name] = parse_ini_file( 
                                              $path.'languages/'.$nameAdd.Language::$default.'.'.$type,
                                              TRUE
                                              );
                    Language::$selectedDefaultLanguage = Language::$default;
                } elseif ($type == 'json') {
                    Language::$defaultLanguage[$name] = json_decode( 
                                              file_get_contents($path.'languages/'.$nameAdd.Language::$default.'.'.$type),
                                              TRUE
                                              );
                    Language::$selectedDefaultLanguage = Language::$default;
                }
            }
        }
    
        if (Language::$selectedLanguage === $lang || $lang === null || isset(Language::$language[$name])) return;
        if (file_exists($path.'languages/'.$nameAdd.$lang.'.'.$type)){
            if (!isset(Language::$language[$name]))
                Language::$language[$name] = array();
            
            if ($type == 'ini'){
                Language::$language[$name] = parse_ini_file( 
                                          $path.'languages/'.$nameAdd.$lang.'.'.$type,
                                          TRUE
                                          );
                Language::$selectedLanguage = $lang;
            } elseif ($type == 'json') {
                Language::$language[$name] = json_decode( 
                                          file_get_contents($path.'languages/'.$nameAdd.$lang.'.'.$type),
                                          TRUE
                                          );
                Language::$selectedLanguage = $lang;
            }
            
        }
    }
    
    public static function Get($area, $cell, $name='default')
    {        
        if (Language::$selectedLanguage != null && isset(Language::$language[$name]) && isset(Language::$language[$name][$area]) && isset(Language::$language[$name][$area][$cell])){
            return Language::$language[$name][$area][$cell];
        } elseif (Language::$selectedDefaultLanguage != null && isset(Language::$defaultLanguage[$name]) && isset(Language::$defaultLanguage[$name][$area]) && isset(Language::$defaultLanguage[$name][$area][$cell])){
            return Language::$defaultLanguage[$name][$area][$cell];
        } else
            return '???';
    }
}
