<?php


/**
 * @file Sprachen.php contains the Sprachen class
 *
 * @author Till Uhlig
 * @date 2014
 */
 
class Sprachen
{
    public static $language = array();
    public static $selectedLanguage = null;
    
    public static function ladeSprache($lang)
    {
        if (Sprachen::$selectedLanguage === $lang) return;
        if (file_exists('./languages/'.$lang.'.ini')){
            Sprachen::$language = parse_ini_file( 
                                      './languages/'.$lang.'.ini',
                                      TRUE
                                      );
            Sprachen::$selectedLanguage = $lang;
        }
    }
    
    public static function Get($area, $cell)
    {
        if (isset(Sprachen::$language[$area]) && isset(Sprachen::$language[$area][$cell])){
            return Sprachen::$language[$area][$cell];
        } else
        return '???';
    }
}

?>