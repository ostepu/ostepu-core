<?php
#region BEISPIEL
class BEISPIEL // der Name der Klasse muss mit dem Dateinamen uebereinstimmen
{
    private static $initialized=false; // gibt an, ob das Segment initialisiert wurde
    public static $name = 'Beispielname';
    public static $installed = false; // ob die Installationsroutine ausgelöst wurde
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 50; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
                              // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktionen aufrufbar sind

    public static $onEvents = array(
                                    '0' =>array(
                                                     'name'=>'Beispielname',
                                                     'event'=>array(
                                                                    'actionInstallBeispielname', // vom Segment selbst gewaehlter Ausloeser
                                                                    'install', // bei der Installation
                                                                    'update', // bei einer Aktualisierung
                                                                    'page'), // beim Seitenaufruf
                                                     'procedure'=>'install' // die im Installationsfall aufzurufende Funktion
                                                     )
                                    );


    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        echo '';
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        echo '';
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }
}
#endregion BEISPIEL