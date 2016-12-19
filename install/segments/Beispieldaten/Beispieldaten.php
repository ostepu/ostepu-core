<?php

/**
 * @file Beispieldaten.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
#region Beispieldaten
class Beispieldaten {

    private static $initialized = false;
    public static $name = 'beispieldaten';
    public static $installed = false;
    public static $page = 8;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate = 'Beispieldaten';
    public static $onEvents = array(
        'installSamples' => array(
            'name' => 'installSamples',
            'event' => array('actionInstallSamples'),
            'procedure' => 'installSamples',
            'enabledInstall' => true
        )
    );

    public static function getDefaults() {
        $res = array(
            'courses' => array('data[SAMPLE][courses]', '100'),
            'user' => array('data[SAMPLE][user]', '1000')
        );
        return $res;
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
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['SAMPLE']['courses'], 'data[SAMPLE][courses]', $def['courses'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['SAMPLE']['user'], 'data[SAMPLE][user]', $def['user'][1], true);
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
        $text .= Design::erstelleBeschreibung($console, Installation::Get('samples', 'description', self::$langTemplate));

        $text .= Design::erstelleZeile($console, Installation::Get('samples', 'courses', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['SAMPLE']['courses'], 'data[SAMPLE][courses]', $data['SAMPLE']['courses'], true), 'v');
        $text .= Design::erstelleZeile($console, Installation::Get('samples', 'user', self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['SAMPLE']['user'], 'data[SAMPLE][user]', $data['SAMPLE']['user'], true), 'v');

        $links = Einstellungen::getLinks('postSamples');
        $text .= Design::erstelleZeile($console, '', '', '', '');
        if (count($links) > 0) {
            $text .= Design::erstelleZeile($console, Installation::Get('samples', 'targetName', self::$langTemplate), 'e_c', Installation::Get('samples', 'priority', self::$langTemplate), 'e_c');
            foreach ($links as $link) {
                $text .= Design::erstelleZeile($console, $link->getTargetName(), 'e', $link->getPriority(), 'v');
            }
        } else {
            $text .= Design::erstelleZeile($console, '', 'e', Installation::Get('samples', 'noLinks', self::$langTemplate), 'v_c error_light');
        }

        if (self::$onEvents['installSamples']['enabledInstall']) {
            $text .= Design::erstelleZeile($console, Installation::Get('samples', 'createSamplesDesc', self::$langTemplate), 'e', Design::erstelleSubmitButton(self::$onEvents['installSamples']['event'][0], Installation::Get('samples', 'createSamples', self::$langTemplate)), 'h');
        }

        $createBackup = false;
        if (isset($result[self::$onEvents['installSamples']['name']])) {
            $content = $result[self::$onEvents['installSamples']['name']]['content'];
            $text .= Design::erstelleZeile($console, Installation::Get('samples', 'targetName', self::$langTemplate), 'e_c', Installation::Get('samples', 'message', self::$langTemplate), 'e_c');
            foreach ($content as $res) {
                if ($res['status'] == 201) {
                    $res['content'] = null;
                }
                $text .= Design::erstelleZeile($console, $res['targetName'], 'e', $res['status'] . ' ' . $res['content'], 'v break');
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('samples', 'title', self::$langTemplate), $text);

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function installSamples($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $res = array();
        $links = Einstellungen::getLinks('postSamples');
        foreach ($links as $link) {
            $result = Request::post($link->getAddress() . '/samples/course/' . $data['SAMPLE']['courses'] . '/user/' . $data['SAMPLE']['user'], array(), '');
            $result['targetName'] = $link->getTargetName();
            $res[] = $result;
            if ($result['status'] != 201) {
                break;
            }
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $res;
    }

}

#endregion BackupSegment