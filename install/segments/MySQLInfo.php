<?php
#region MySQLInfo
class MySQLInfo // der Name der Klasse muss mit dem Dateinamen uebereinstimmen
{
    private static $initialized=false; // gibt an, ob das Segment initialisiert wurde
    public static $name = 'MySQLInfo';
    public static $installed = false; // ob die Installationsroutine ausgelöst wurde
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 100; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
                              // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktion aufrufbar ist
    
    public static $onEvents = array(
                                    '0' =>array(
                                                     'name'=>'Beispielname',
                                                     'event'=>array(
                                                                    'actionInstallMySQLInfo',
                                                                    'page')
                                                     )
                                    );
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';     
        $text .= Design::erstelleBeschreibung($console,Language::Get('mySQLInfo','description'));   
        echo Design::erstelleBlock($console, Language::Get('mySQLInfo','title'), $text);
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        return null;
    }
}
#endregion MySQLInfo