<?php
/**
 * @file Language.php contains the Language class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.4
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
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
     * @var string $errorValue Dieser Text wird ausgegeben, wenn kein Platzhalter gefunden werden kann
     */
    public static $errorValue = '???';
        
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
	 * Lädt für loadLanguage eine Sprachdatei
	 * 
	 * @param string $lang           Der Bezeichner der Sprachdatei
	 * @param string $name           Der normale Name der Sprachdatei
	 * @param string $nameAdd        Der erweiterte Name der Sprachdatei
	 * @param string $type           Der Typ der Sprachdatei ("json", "ini")
	 * @param string $path           Der Pfad der Sprachdatei
	 * @param string $languageTarget Der Name der Zielvariable wo die Sprachdaten hinsollen 
	 *                               ("defaultLanguage", "language")
	 * @param string $selectTarget   Der Name der Zielvariable die den Namen der Sprache haben soll 
	 *                               ("selectedDefaultLanguage", "defaultLanguage")
	 */
	private static function loadSingleLanguageFile($lang, $name, $nameAdd, $type, $path, $languageTarget, $selectTarget) {
		if (file_exists($path . 'languages/' . $nameAdd . $lang . '.' . $type)){
			$language = &self::$$languageTarget; //Umgeht den Bug, der ein Array nach self::$$abc nicht zulässt.
            if (!isset($language[$name]))
                $language[$name] = array();
            
            if ($type == 'ini'){
                $language[$name] = parse_ini_file( 
					$path . 'languages/' . $nameAdd . $lang . '.' . $type,
                    true);
            } elseif ($type == 'json') {
                $language[$name] = json_decode( 
					file_get_contents($path . 'languages/' . $nameAdd . $lang . '.' . $type),
                    true);
            }
			else return false;
			self::$$selectTarget = $lang;
            return true;
        }
		else return false;
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
			self::loadSingleLanguageFile(self::$default, $name, $nameAdd, $type, $path, 'defaultLanguage', 'selectedDefaultLanguage');
        }
    
        if (self::$selectedLanguage === $lang || $lang === null || isset(self::$language[$name])) return;
        if (self::$preferedLanguage !== null && file_exists($path.'languages/'.$nameAdd.self::$preferedLanguage.'.'.$type)){
            $lang = self::$preferedLanguage;
        }
        
		self::loadSingleLanguageFile($lang, $name, $nameAdd, $type, $path, 'language', 'selectedLanguage');
    }
	
	/**
	 * Formatiert einen String und ersetzt alle Platzhalter
	 * 
	 * @param  string   $res    Der String, welcher formatiert werden soll
	 * @param  string[] $params Eine Liste aus Platzhalterersetzungen
	 * @return string           Der verarbeitete Text
	 */
	private static function formatString($res, $params) {
		$matches = array();
        preg_match_all('/[^\\\\]\$\[([\w,]+)\]/', $res, $matches);
        foreach ($matches[1] as $match){
            $splitted = explode(',',$match);
            if (count($splitted<2)){
                $elem = self::$errorValue;
            } else if (count($splitted)==2){
                $elem = self::Get($splitted[0],$splitted[1]);
            } else if (count($splitted)==3){
                $elem = self::Get($splitted[0],$splitted[1],$splitted[2]);
            }
            $res = preg_replace('/([^\\\\])(\$\['.$match.'\])/','${1}'.$elem,$res);
        }
        
        foreach($params as $key => $value){
            if (!is_string($value) && !is_int($value)){
                $value = json_encode($value);
            }
            $value = htmlspecialchars($value);
            $res = preg_replace('/([^\\\\])(\$'.$key.')/','${1}'.$value,$res);
        }
        $res = preg_replace('/([^\\\\])(\$[\w]+)/','${1}???',$res);
        $res = str_replace('\$','$',$res);
        
        return $res;
	}
        
    /**
     * Liefert den Text zu dem Platzhalter $cell im Bereich $area
     *
     * @param string $area Der Name des Bereichs
     * @param string $cell Der Name des Platzhalters
     * @param string $name Ein optionaler Sprachbezeichner (Bsp.: de, en)
     * @param string[] $params Ersetzungen für Platzhalter. Bsp.: array('abc'=>'test') ersetzt $abc mit 'test'... $ muss maskiert werden
     * @return string Der Text aus der geladenen Sprache, der Standardsprache oder ??? im Fehlerfall
     */
    public static function Get($area, $cell, $name='default', $params=array())
    {        
        if (self::$selectedLanguage !== null && 
			isset(self::$language[$name]) && 
			isset(self::$language[$name][$area]) && 
			isset(self::$language[$name][$area][$cell]))
		{
			$res = self::$language[$name][$area][$cell];
        } 
		elseif (self::$selectedDefaultLanguage !== null && 
			isset(self::$defaultLanguage[$name]) && 
			isset(self::$defaultLanguage[$name][$area]) && 
			isset(self::$defaultLanguage[$name][$area][$cell]))
		{
            $res = self::$defaultLanguage[$name][$area][$cell];
        } else {
            $res = self::$errorValue;
        }
		
		return self::formatString($res, $params);
    }
	
	/**
	 * Verarbeitet alle Strings zu einem Bereich und fügt diesen zur Ergebnismenge hinzu.
	 * 
	 * @param string       $selectName Der Name für die Variable für die ausgewählte Sprache
	 * @param string       $sourceName Der Name für die Variable für die aktuelle Sprachdaten
	 * @param string       $name       Ein Bezeichner für die Rubrik
	 * @param string       &$res       Die Ergebnismenge
	 * @param string[][][] $params     Die mitgelieferten Parameter
	 */
	private static function addToResult($selectName, $sourceName, $name, &$res, $params) {
		$source = &self::$$sourceName;
		if (self::$$selectName !== null &&
			isset($source[$name]))
		{
			foreach ($source[$name] as $akey => $area) {
				foreach ($area as $ckey => $cell) {
					if (!isset($res[$akey])) $res[$akey] = array();
					if (isset($res[$akey][$ckey])) continue;
					
					$p = isset($params[null]) ? $params[null] : array();
					if (isset($params[$akey]) && isset($params[$akey][null]))
						$p = array_merge($p, $params[$akey][null]);
					if (isset($params[$akey]) && isset($params[$akey][$ckey]))
						$p = array_merge($p, $params[$akey][$ckey]);
					
					$res[$akey][$ckey] = self::formatString($cell, $p);
				}
			}
		}
	}
	
	/**
	 * Sucht alle Strings zu einer Rubrik heraus und verarbeitet diesen
	 * 
	 * @param string       $name   Der Name der ausgewählten Rubrik
	 * @param string[][][] $params Eine Auflistung für alle Parameter, die bei jedem der Strings 
	 *                             zur Formatierung genutzt werden. Hierbei gilt folgende Struktur:
	 *                             $params[] :
	 *                                [null] = array() => Defaultwerte für alle Strings
	 *                                [$area][] : (alle Bereiche innerhalb der Rubrik)
	 *                                   [null] = array() => Defaultwerte für alle Strings innerhalb eines Bereichs
	 *                                   [$cell] = array() => Parameterwerte für genau einen String
	 *                             Die Parameter selbst folgen den Konventionen der Funktion Get()
	 * @return string[][]          Alle berechneten Strings
	 */
	public static function GetAll($name='default', $params=array()) {
		$res = array();
		
		self::addToResult("selectedLanguage", "language", $name, $res, $params);
		self::addToResult("selectedDefaultLanguage", "defaultLanguage", $name, $res, $params);
		
		return $res;
	}
}
