<?php

/**
 * @file GrundeinstellungenAusgeben.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */
#region GrundeinstellungenAusgeben
class GrundeinstellungenAusgeben {

    private static $initialized = false;
    public static $name = 'installInit';
    public static $installed = false;
    public static $page = 2;
    public static $rank = 75;
    public static $enabledShow = true;
    private static $langTemplate = 'GrundeinstellungenAusgeben';
    public static $onEvents = array('install' => array('name' => 'installInit', 'event' => array('actionInstallInit', 'install', 'update')));

    public static function getDefaults() {
        return array(
            'db_ignore' => array('data[DB][db_ignore]', null),
            'db_override' => array('data[DB][db_override]', null),
            'pl_main_details' => array('data[PL][pl_main_details]', null)
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
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_ignore'], 'data[DB][db_ignore]', $def['db_ignore'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_override'], 'data[DB][db_override]', $def['db_override'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', $def['pl_main_details'][1], true);
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
        if (!$console) {
            $text .= Design::erstelleBeschreibung($console, Installation::Get('general_settings', 'description', self::$langTemplate));

            $text .= Design::erstelleZeile($console, Installation::Get('general_settings', 'init', self::$langTemplate), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallInit'), 'h');
            $text .= Design::erstelleZeile($console, Installation::Get('database', 'db_override', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['DB']['db_override'], 'data[DB][db_override]', 'override', null, true), 'v_c');
            $text .= Design::erstelleZeile($console, Installation::Get('database', 'db_ignore', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['DB']['db_ignore'], 'data[DB][db_ignore]', 'ignore', null, true), 'v_c');

            $text .= Design::erstelleZeile($console, Installation::Get('general_settings', 'details', self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', 'details', null, true), 'v_c');
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
            if ($data['PL']['pl_main_details'] == 'details') {
                foreach ($content as $component => $dat) {
                    if (!$console) {
                        $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>" . ((isset($dat['status']) && $dat['status'] === 201) ? Installation::Get('main', 'ok') : "<font color='red'>" . Installation::Get('main', 'fail') . " ({$dat['status']})</font>") . "</align></td></tr>";
                    } else {
                        $text .= "{$component}: " . ((isset($dat['status']) && $dat['status'] === 201) ? Installation::Get('main', 'ok') . "\n" : Installation::Get('main', 'fail') . " ({$dat['status']})\n");
                    }
                }
            }

            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Installation::Get('general_settings', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function install($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // Datenbank einrichten
        if (!isset($data['action']) || $data['action'] != 'update') {
            if (!$fail && (isset($data['DB']['db_override']) && $data['DB']['db_override'] === 'override')) {
                $sql = 'DROP SCHEMA IF EXISTS `' . $data['DB']['db_name'] . '`;';
                Installation::log(array('text' => Installation::Get('database', 'dropSchemaSql', self::$langTemplate, array('sql' => $sql))));
                $oldName = $data['DB']['db_name'];
                $data['DB']['db_name'] = null;
                $result = DBRequest::request2($sql, false, $data);
                $result=$result[0];
                Installation::log(array('text' => Installation::Get('database', 'dropSchemaResult', self::$langTemplate, array('res' => json_encode($result)))));
                if ($result['errno'] !== 0) {
                    $fail = true;
                    $errno = $result['errno'];
                    $error = isset($result["error"]) ? $result["error"] : '';
                }
                $data['DB']['db_name'] = $oldName;
            }
        }

        if (!$fail) {
            $add = (((isset($data['DB']['db_ignore']) && $data['DB']['db_ignore'] === 'ignore') || (isset($data['action']) && $data['action'] == 'update')) ? 'IF NOT EXISTS ' : '');
            $sql = "CREATE SCHEMA {$add}`" . $data['DB']['db_name'] . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
            Installation::log(array('text' => Installation::Get('database', 'createSchemaSql', self::$langTemplate, array('sql' => $sql))));
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $result = DBRequest::request2($sql, false, $data);
            $result=$result[0];
            Installation::log(array('text' => Installation::Get('database', 'dropSchemaResult', self::$langTemplate, array('res' => json_encode($result)))));
            if ($result["errno"] !== 0) {
                $fail = true;
                $errno = $result["errno"];
                $error = isset($result["error"]) ? $result["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        }


        // CControl+DBQuery2 einrichten
        $res = array();
        if (!$fail) {
            $list = array('DB/CControl', 'DB/DBQuery2');
            $platform = Installation::PlattformZusammenstellen($data);

            for ($i = 0; $i < count($list); $i++) {
                $url = $list[$i]; //$data['PL']['init'];
                // inits all components
                Installation::log(array('text' => Installation::Get('database', 'createQueryInitComponents', self::$langTemplate, array('url' => 'POST ' . $data['PL']['url'] . '/' . $url . '/platform', 'content' => Platform::encodePlatform($platform)))));
                $result = Request::post($data['PL']['url'] . '/' . $url . '/platform', array(), Platform::encodePlatform($platform));
                Installation::log(array('text' => Installation::Get('database', 'initComponentsResult', self::$langTemplate, array('res' => json_encode($result)))));

                $res[$url] = array();
                if (isset($result['content']) && isset($result['status']) && $result['status'] === 201) {
                    $res[$url]['status'] = 201;
                } else {
                    $res[$url]['status'] = 409;
                    $fail = true;
                    if (isset($result['status'])) {
                        $errno = $result['status'];
                        $res[$url]['status'] = $result['status'];
                    }
                    ///if (isset($result['content'])) echo $result['content'];
                    Installation::log(array('text' => Installation::Get('database', 'failureInitComponents', self::$langTemplate, array('status' => $res[$url]['status'])), 'logLevel' => LogLevel::ERROR));
                }
            }
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $res;
    }

}

#endregion Grundeinstellungen_ausgeben