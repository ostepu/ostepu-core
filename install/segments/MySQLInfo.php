<?php
#region MySQLInfo
class MySQLInfo // der Name der Klasse muss mit dem Dateinamen uebereinstimmen
{
    public static $name = 'MySQLInfo';
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 100; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
                              // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktion aufrufbar ist
    private static $langTemplate='MySQLInfo';

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Language::Get('main','languageInstantiated')));
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }
   
    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;
           
        Installation::log(array('text'=>Language::Get('main','functionBegin')));
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('mySQLInfo','description',self::$langTemplate));
        echo Design::erstelleBlock($console, Language::Get('mySQLInfo','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Language::Get('main','functionEnd')));
    }
}
#endregion MySQLInfo