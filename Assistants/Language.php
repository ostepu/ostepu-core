<?php


/**
 * @file Language.php contains the Language class
 *
 * @author Till Uhlig
 * @date 2014
 */

class Language
{
    
    /**
     * @var string[][][] $language Die geladene Sprache
     * Struktur: self::$language[Paketname][Bereich][Platzhalter]
     */
    public static $language = array();
    
    /**
     * @var string $selectedLanguage Die ausgewählte/geladene Sprache
     */
    public static $selectedLanguage = null;
    
    /**
     * @var string $preferedLanguage Die bevorzugte Sprache
     */
    public static $preferedLanguage = null;
    
    /**
     * @var string[][][] $defaultLanguage Die geladene Standardsprache
     * Struktur: self::$defaultLanguage[Paketname][Bereich][Platzhalter]
     */
    public static $defaultLanguage = array();
    
    /**
     * @var string Die ausgwählte/geladene Standardsprache
     */
    public static $selectedDefaultLanguage = null;   

    /**
     * @var string $default Die vom Sprachsystem vorgegebene Standardsprache
     */
    public static $default = 'de';
        
    /**
     * Setzt die bevorzugte Sprache
     *
     * @param string $lang Der Sprachbezeichner Bsp.: de, en
     */
    public static function setPreferedLanguage($lang)
    {
        self::$preferedLanguage = $lang;
    }
        
    /**
     * Lädt die Sprachdatei von $lang
     *
     * @param string $lang Der Bezeichner der Sprachedatei
     * @param string $name Ein zusätzlicher optionaler Präfix der Sprachdatei und Name des Platzhalterpakets
     * @param string $type Der Typ der Sprachdatei (json oder ini)
     * @param string $path Der Pfad der Sprachdatei
     */
    public static function loadLanguageFile($lang, $name='default', $type='json', $path='')
    {
        self::loadLanguage($lang, $name, $type, $path);
    }
        
    /**
     * Lädt die Sprache $lang
     *
     * @param string $lang Der Bezeichner der Sprachedatei
     * @param string $name Ein zusätzlicher optionaler Präfix der Sprachdatei und Name des Platzhalterpakets
     * @param string $type Der Typ der Sprachdatei (json oder ini)
     * @param string $path Der Pfad der Sprachdatei
     */
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
        
    /**
     * Liefert den Text zu dem Platzhalter $cell im Bereich $area
     *
     * @param string $area Der Name des Bereichs
     * @param string $cell Der Name des Platzhalters
     * @param string $name Ein optionaler Sprachbezeichner (Bsp.: de, en)
     * @return string Der Text aus der geladenen Sprache, der Standardsprache oder ??? im Fehlerfall
     */
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
