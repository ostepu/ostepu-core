<?php

/**
 * Dieses Segment erzeugt eine Update-Schaltfläche in der infoBar am linken Rand des Fensters.
 * Über diese Schaltfläche kann in den anderen Segmenten das Ereignis "update" ausgelöst werden.
 * @file Aktionsleiste.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.5
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
#region Aktionsleiste
class Aktionsleiste {

    public static $name = 'actionBar';
    public static $enabledShow = true;
    public static $rank = 150;
    private static $langTemplate = 'Aktionsleiste';

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

        // hier wird die Sprachdatei geladen
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__) . '/');
        Installation::log(array('text' => Installation::Get('main', 'languageInstantiated')));
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    /**
     * gibt den HTML-Text für die Info-Leiste (links) aus
     * @param string[][] $data die Serverdaten
     */
    public static function showInfoBar(&$data) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));

        // die Leiste soll nur gezeichnet werden, wenn der Nutzer eingeloggt ist
        if (Einstellungen::$accessAllowed) {
            // Aktionen
            echo "<tr><td class='e'>" . Installation::Get('main', 'actions', self::$langTemplate) . "</td></tr>";

            // update-Button
            echo "<tr><td class='v'>" . Design::erstelleSubmitButtonFlach('update', 'OK', Installation::Get('main', 'simpleUpdate', self::$langTemplate) . ">") . "</td></tr>";
        }
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

}

#endregion Aktionsleiste