<?php
#region MySQLInfo
class MySQLInfo // der Name der Klasse muss mit dem Dateinamen uebereinstimmen
{
    public static $name = 'MySQLInfo';
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 100; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
                              // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktion aufrufbar ist  

    public static function show($console, $result, $data)
    {
        $text='';     
        $text .= Design::erstelleBeschreibung($console,Language::Get('mySQLInfo','description'));   
        echo Design::erstelleBlock($console, Language::Get('mySQLInfo','title'), $text);
    }
}
#endregion MySQLInfo