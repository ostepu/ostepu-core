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
    
    public static function ladeSprache($lang)
    {
        if (file_exists('./languages/'.$lang.'.ini')){
            Sprachen::$language = parse_ini_file( 
                                      './languages/'.$lang.'.ini',
                                      TRUE
                                      );
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