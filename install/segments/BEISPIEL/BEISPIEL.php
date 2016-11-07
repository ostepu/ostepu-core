<?php

/**
 * @file BEISPIEL.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
#region BEISPIEL
class BEISPIEL  { // der Name der Klasse muss mit dem Dateinamen uebereinstimmen

    private static $initialized = false; // gibt an, ob das Segment initialisiert wurde
    public static $name = 'Beispielname';
    public static $installed = false; // ob die Installationsroutine ausgelöst wurde
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 50; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
    // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktionen aufrufbar sind
    private static $langTemplate = 'BEISPIEL';
    public static $onEvents = array(
        '0' => array(
            'name' => 'Beispielname',
            'event' => array(
                'actionInstallBeispielname', // vom Segment selbst gewaehlter Ausloeser
                'install', // bei der Installation
                'update', // bei einer Aktualisierung
                'page'), // beim Seitenaufruf
            'procedure' => 'install' // die im Installationsfall aufzurufende Funktion
        )
    );

    /**
     * initialisiert das Segment
     * @param type $console
     * @param string[][] $data die Serverdaten
     * @param bool $fail wenn ein Fehler auftritt, dann auf true setzen
     * @param string $errno im Fehlerfall kann hier eine Fehlernummer angegeben werden
     * @param string $error ein Fehlertext für den Fehlerfall
     */
    public static function init($console, &$data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        echo '';
        self::$initialized = true;
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function show($console, $result, $data) {
        // das Segment soll nur gezeichnet werden, wenn der Nutzer eingeloggt ist
        if (!Einstellungen::$accessAllowed)
            return;

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__) . '/');
        Installation::log(array('text' => Installation::Get('main', 'languageInstantiated')));
        echo '';
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function install($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

}

#endregion BEISPIEL