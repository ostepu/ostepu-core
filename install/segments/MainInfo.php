<?php
#region MainInfo
class MainInfo
{
    private static $initialized=false; // gibt an, ob das Segment initialisiert wurde
    public static $name = 'MainInfo';
    public static $installed = false; // ob die Installationsroutine ausgelöst wurde
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 0; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
                              // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktion aufrufbar ist
    
    public static $onEvents = array(
                                    '0' =>array(
                                                     'name'=>'MainInfo',
                                                     'event'=>array(
                                                                    'actionInstallMain', // vom Segment selbst gewaehlter Ausloeser
                                                                    'install', // bei der Installation
                                                                    'update', // bei einer Aktualisierung
                                                                    'page'), // beim Seitenaufruf
                                                     'procedure'=>'install' // die im Installationsfall aufzurufende Funktion
                                                     )
                                    );
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        echo '';
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';
        $failure=false;
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        if (!is_dir(Einstellungen::$path) || !is_writable(__FILE__)) {
            $text .= Design::erstelleZeile($console, Sprachen::Get('mainInfo','notWritable'), 'error');
            $failure = true;
        }        
        
        if ($failure)
            echo Design::erstelleBlock($console, Sprachen::Get('mainInfo','title'), $text);
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        return null;
    }
}
#endregion MainInfo