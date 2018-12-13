<?php

/**
 * @file Grundinformationen.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */
#region Grundinformationen
class Grundinformationen {

    private static $initialized = false;
    public static $name = 'mainInfo';
    public static $installed = false;
    public static $page = 1;
    public static $rank = 50;
    public static $enabledShow = true;
    private static $langTemplate = 'Grundinformationen';
    public static $onEvents = array();

    public static function getSettingsBar(&$data) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $defs = self::getDefaults();
        $res = array(
            'url' => array(Installation::Get('general_informations', 'url', self::$langTemplate), $data['PL']['url'], $defs['url'][1]),
            'localPath' => array(Installation::Get('general_informations', 'localPath', self::$langTemplate), $data['PL']['localPath'], $defs['localPath'][1]),
            'urlExtern' => array(Installation::Get('general_informations', 'urlExtern', self::$langTemplate), $data['PL']['urlExtern'], $defs['urlExtern'][1]),
            'temp' => array(Installation::Get('general_informations', 'temp', self::$langTemplate), $data['PL']['temp'], $defs['temp'][1]),
            'files' => array(Installation::Get('general_informations', 'files', self::$langTemplate), $data['PL']['files'], $defs['files'][1])
        );
        Installation::log(array('text' => Installation::Get('general_informations', 'barResult', self::$langTemplate, array('res' => json_encode($res)))));
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $res;
    }

    public static function getDefaults() {
        return array(
            'name' => array('data[SV][name]', ''),
            'url' => array('data[PL][url]', 'http://localhost/uebungsplattform'),
            'localPath' => array('data[PL][localPath]', '/var/www/uebungsplattform'),
            'urlExtern' => array('data[PL][urlExtern]', 'http://localhost/uebungsplattform'),
            'temp' => array('data[PL][temp]', '/var/www/temp'),
            'files' => array('data[PL][files]', '/var/www/files'),
            'developmentMode' => array('data[PL][developmentMode]', '0'),
            'logLevel' => array('data[PL][logLevel]', strval(LogLevel::ERROR))
        );
    }

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

        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['SV']['name'], 'data[SV][name]', $def['name'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['url'], 'data[PL][url]', $def['url'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['localPath'], 'data[PL][localPath]', $def['localPath'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', $def['urlExtern'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', $def['temp'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['files'], 'data[PL][files]', $def['files'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['developmentMode'], 'data[PL][developmentMode]', $def['developmentMode'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['logLevel'], 'data[PL][logLevel]', $def['logLevel'][1], true);
        echo $text;
        self::$initialized = true;
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function show($console, $result, $data) {
        // das Segment soll nur gezeichnet werden, wenn der Nutzer eingeloggt ist
        if (!Einstellungen::$accessAllowed) {
            return;
        }

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $text = '';
        $text .= Design::erstelleBeschreibung($console, Installation::Get('general_informations', 'description', self::$langTemplate));

        if (!$console) {
            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'server_name', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['SV']['name'], 'data[SV][name]', $data['SV']['name'], false), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'url', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['PL']['url'], 'data[PL][url]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'localPath', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['PL']['localPath'], 'data[PL][localPath]', '/var/www/uebungsplattform', true), 'v');
            if (!file_exists($data['PL']['localPath'])) {
                Installation::log(array('text' => 'data[PL][localPath]: ' . $data['PL']['localPath'] . ' existiert nicht.', 'logLevel' => LogLevel::WARNING));
                $text .= Design::erstelleZeile($console, '', 'e', $data['PL']['localPath'] . ' existiert nicht.', 'error v');
            }

            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'urlExtern', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['PL']['urlExtern'], 'data[PL][urlExtern]', 'http://localhost/uebungsplattform', true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'temp', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['PL']['temp'], 'data[PL][temp]', '/var/www/temp', true), 'v');
            if (!file_exists($data['PL']['temp'])) {
                Installation::log(array('text' => 'data[PL][temp]: ' . $data['PL']['temp'] . ' existiert nicht.', 'logLevel' => LogLevel::WARNING));
                $text .= Design::erstelleZeile($console, '', 'e', $data['PL']['temp'] . ' existiert nicht.', 'error v');
            }

            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'files', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['PL']['files'], 'data[PL][files]', '/var/www/files', true), 'v');
            if (!file_exists($data['PL']['files'])) {
                Installation::log(array('text' => 'data[PL][files]: ' . $data['PL']['files'] . ' existiert nicht.', 'logLevel' => LogLevel::WARNING));
                $text .= Design::erstelleZeile($console, '', 'e', $data['PL']['files'] . ' existiert nicht.', 'error v');
            }

            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'developmentMode', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['PL']['developmentMode'], 'data[PL][developmentMode]', '1', null, true), 'v_c');
            $text .= Design::erstelleZeile($console, Installation::Get('general_informations', 'logLevel', self::$langTemplate), 'e', Design::erstelleAuswahlliste($console, array(strval(LogLevel::NONE)=>'NONE', strval(LogLevel::DEBUG)=>'DEBUG', LogLevel::INFO=>'INFO', strval(LogLevel::WARNING)=>'WARNING', strval(LogLevel::ERROR)=>'ERROR'), $data['PL']['logLevel'], 'data[PL][logLevel]', strval(LogLevel::ERROR), true), 'v_c');
        }

        echo Design::erstelleBlock($console, Installation::Get('general_informations', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    /*
     * diese Methode wird von Installation::PlattformZusammenstellen aufgerufen (eingesammelt)
     */
    public static function platformSetting($data) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $result = array('developmentMode' => $data['PL']['developmentMode'], 'logLevel' => $data['PL']['logLevel']);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $result;
    }
    
    /*
     * diese Methode wird von BenutzerschnittstelleEinrichten aufgerufen (eingesammelt)
     */
    public static function getUserInterfaceSettings($data) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $result = array('developmentMode' => $data['PL']['developmentMode'], 'logLevel' => $data['PL']['logLevel']);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $result;
    }

}

#endregion Grundinformationen