<?php

/**
 * @file MySQLInfo.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
#region MySQLInfo
class MySQLInfo {

    public static $name = 'MySQLInfo';
    public static $page = 0; // die ID der Seite, auf welcher das Segment gezeigt werden soll
    public static $rank = 100; // bestimmt die Reihenfolge im Vergleich zu anderen Segmenten auf der selben Seite
    // niedriger Rank = fruehe Ausfuehrung, hoher Rank = spaetere Ausfuehrung
    public static $enabledShow = true; // ob die show() Funktion aufrufbar ist
    private static $langTemplate = 'MySQLInfo';

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
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__) . '/');
        Installation::log(array('text' => Installation::Get('main', 'languageInstantiated')));
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function show($console, $result, $data) {
        if (!Einstellungen::$accessAllowed)
            return;

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $text = '';
        if (!$console) {
            $text .= Design::erstelleBeschreibung($console, Installation::Get('mySQLInfo', 'description', self::$langTemplate));
        }

        if (!$console) {
            echo Design::erstelleBlock($console, Installation::Get('mySQLInfo', 'title', self::$langTemplate), $text);
        } else {
            if ($text != '') {
                echo Design::erstelleBlock($console, Installation::Get('mySQLInfo', 'title2', self::$langTemplate), $text);
            }
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

}

#endregion MySQLInfo