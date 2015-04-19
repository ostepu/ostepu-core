<?php


/**
 * @file Sprachen.php contains the Sprachen class
 *
 * @author Till Uhlig
 * @date 2014
 */
 
Sprachen::ladeSprache(null);

class Sprachen
{
    public static $language = array();
    public static $selectedLanguage = null;
    
    public static $defaultLanguage = array();
    public static $selectedDefaultLanguage = null;
    public static $default = 'de';
    
    public static function ladeSprache($lang)
    {
        if (Sprachen::$selectedDefaultLanguage==null || Sprachen::$selectedDefaultLanguage != Sprachen::$default){
            if (file_exists(dirname(__FILE__) . '/../languages/'.Sprachen::$default.'.ini')){
                Sprachen::$defaultLanguage = parse_ini_file( 
                                          dirname(__FILE__) . '/../languages/'.Sprachen::$default.'.ini',
                                          TRUE
                                          );
                Sprachen::$selectedDefaultLanguage = Sprachen::$default;
            }
        }
    
        if (Sprachen::$selectedLanguage === $lang || $lang === null) return;
        if (file_exists(dirname(__FILE__) . '/../languages/'.$lang.'.ini')){
            Sprachen::$language = parse_ini_file( 
                                      './languages/'.$lang.'.ini',
                                      TRUE
                                      );
            Sprachen::$selectedLanguage = $lang;
        }
    }
    
    public static function Get($area, $cell)
    {
        if (Sprachen::$selectedLanguage != null && isset(Sprachen::$language[$area]) && isset(Sprachen::$language[$area][$cell])){
            return Sprachen::$language[$area][$cell];
        } elseif (Sprachen::$selectedDefaultLanguage != null && isset(Sprachen::$defaultLanguage[$area]) && isset(Sprachen::$defaultLanguage[$area][$cell])){
            return Sprachen::$defaultLanguage[$area][$cell];
        } else
            return '???';
    }
}
