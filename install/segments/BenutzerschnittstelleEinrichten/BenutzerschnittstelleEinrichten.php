<?php

/**
 * @file BenutzerschnittstelleEinrichten.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
#region BenutzerschnittstelleEinrichten
class BenutzerschnittstelleEinrichten {

    private static $initialized = false;
    public static $name = 'UIConf';
    public static $installed = false;
    public static $page = 6;
    public static $rank = 50;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    private static $langTemplate = 'BenutzerschnittstelleEinrichten';
    public static $onEvents = array('install' => array('name' => 'UIConf', 'event' => array('actionInstallUIConf', 'install', 'update')));

    public static function getSettingsBar(&$data) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $defs = self::getDefaults();
        $res = array(
            'siteKey' => array(Installation::Get('userInterface', 'siteKey', self::$langTemplate), $data['UI']['siteKey'], $defs['siteKey'][1])
        );
        Installation::log(array('text' => Installation::Get('userInterface', 'barResult', self::$langTemplate, array('res' => json_encode($res)))));
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $res;
    }

    public static function getDefaults() {
        return array(
            'conf' => array('data[UI][conf]', '../UI/include/Config.php'),
            'siteKey' => array('data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4'),
            'maintenanceMode' => array('data[UI][maintenanceMode]', '0'),
            'maintenanceText' => array('data[UI][maintenanceText]', ''),
            'maintenanceAllowedUsers' => array('data[UI][maintenanceAllowedUsers]', '')
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
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', $def['conf'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', $def['siteKey'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['maintenanceMode'], 'data[UI][maintenanceMode]', $def['maintenanceMode'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['maintenanceText'], 'data[UI][maintenanceText]', $def['maintenanceText'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['UI']['maintenanceAllowedUsers'], 'data[UI][maintenanceAllowedUsers]', $def['maintenanceAllowedUsers'][1], true);
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
        $text .= Design::erstelleBeschreibung($console, Installation::Get('userInterface', 'description', self::$langTemplate));

        if (!$console) {
            $text .= Design::erstelleZeile($console, Installation::Get('userInterface', 'conf', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['UI']['conf'], 'data[UI][conf]', '../UI/include/Config.php', true), 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            if (!file_exists($data['UI']['conf'])) {
                Installation::log(array('text' => 'data[UI][conf]: ' . $data['UI']['conf'] . ' muss installiert werden.', 'logLevel' => LogLevel::WARNING));
                $text .= Design::erstelleZeile($console, '', 'e', $data['UI']['conf'] . ' muss installiert werden.', 'error v');
            }

            $text .= Design::erstelleZeile($console, Installation::Get('userInterface', 'siteKey', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['UI']['siteKey'], 'data[UI][siteKey]', 'b67dc54e7d03a9afcd16915a55edbad2d20a954562c482de3863456f01a0dee4', true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('userInterface', 'maintenanceMode', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['UI']['maintenanceMode'], 'data[UI][maintenanceMode]', '1', null, true), 'v_c');
            $text .= Design::erstelleZeile($console, Installation::Get('userInterface', 'maintenanceText', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['UI']['maintenanceText'], 'data[UI][maintenanceText]', '', true), 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('userInterface', 'maintenanceAllowedUsers', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['UI']['maintenanceAllowedUsers'], 'data[UI][maintenanceAllowedUsers]', '', true), 'v');
        }

        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']] != null) {
            $result = $result[self::$onEvents['install']['name']];
        } else {
            $result = array('content' => null, 'fail' => false, 'errno' => null, 'error' => null);
        }

        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];

        if (self::$installed) {
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Installation::Get('userInterface', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function install($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $fail = false;
        $file = $data['UI']['conf'];

        $text = array("<?php");
        $text[] = '$serverURI' . " = '{$data['PL']['url']}';";
        $text[] = '$databaseURI = $serverURI . "/DB/DBControl";';
        $text[] = '$logicURI = $serverURI . "/logic/LController";';
        $text[] = '$logicFileURI = $serverURI . "/logic/LFile";';
        $text[] = '$filesystemURI = $serverURI . "/FS/FSControl";';
        $text[] = '$getSiteURI = $serverURI . "/logic/LGetSite";';
        $text[] = '$globalSiteKey' . " = '{$data['UI']['siteKey']}';";
        $text[] = '$externalURI' . " = '{$data['PL']['urlExtern']}';";
        $text[] = '$maintenanceMode' . " = '{$data['UI']['maintenanceMode']}';";
        $text[] = '$maintenanceText' . " = '{$data['UI']['maintenanceText']}';";
        $text[] = '$maintenanceAllowedUsers' . " = '{$data['UI']['maintenanceAllowedUsers']}';";

        $text = implode("\n", $text);
        Installation::log(array('text' => Installation::Get('userInterface', 'confContent', self::$langTemplate, array('content' => json_encode($text)))));
        $resFile = $data['PL']['localPath'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . $file;
        Installation::log(array('text' => Installation::Get('userInterface', 'contFile', self::$langTemplate, array('file' => $resFile))));

        if (!@file_put_contents($resFile, $text)) {
            $fail = true;
            $error = 'UI-Konfigurationsdatei, kein Schreiben möglich!';
            Installation::log(array('text' => Installation::Get('userInterface', 'failureAccessConfFile', self::$langTemplate, array('message' => $error)), 'logLevel' => LogLevel::ERROR));
            return null;
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

}

#endregion BenutzerschnittstelleEinrichten