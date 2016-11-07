<?php

/**
 * @file Installation.php contains the Installation class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */
require_once dirname(__FILE__) . '/../../UI/include/Authentication.php';
require_once dirname(__FILE__) . '/../../Assistants/Structures.php';
require_once dirname(__FILE__) . '/../../Assistants/Request.php';
require_once dirname(__FILE__) . '/../../Assistants/DBRequest.php';
require_once dirname(__FILE__) . '/../../Assistants/DBJson.php';

class Installation {

    public static $logFile = null;
    public static $enableLogs = true;
    public static $logLevel = LogLevel::NONE;

    /**
     * Orders an array by given keys.
     *
     * This methods accepts multiple arguments. That means you can define more than one key.
     * e.g. orderby($data, 'key1', SORT_DESC, 'key2', SORT_ASC).
     *
     * @param array $data The array which will be sorted.
     * @param string $key The key of $data.
     * @param mixed $sortorder Either SORT_ASC to sort ascendingly or SORT_DESC to sort descendingly.
     *
     * @return array An array ordered by given parameters.
     */
    public static function orderBy() {
        Installation::log(array('text' => 'starte Funktion'));
        $args = func_get_args();
        $data = array_shift($args);
        if ($data === null) {
            $data = array();
        }

        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }

                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        Installation::log(array('text' => 'beende Funktion'));
        return array_pop($args);
    }

    /*
     * @param array $components The array which will be filtered.
     * @param string $type '', 'RegEx'
     * @param string $root '', 'Command', 'ComponentName'
     */

    public static function filterComponents($components, $type, $root) {
        Installation::log(array('text' => 'starte Funktion'));
        Installation::log(array('text' => 'beende Funktion'));
    }

    /**
     * prüft, ob die Segmente eine bestimmte Methode $name besitzen und
     * ruft diese auf, sodass Segmente untereinander Daten austauschen können
     * @param string $name
     * @param string $data
     * @return mixed eine zusammengewürfeltes Ergebnis aller Aufrufe
     */
    public static function collect($name, $data) {
        Installation::log(array('text' => 'starte Funktion'));
        $res = array();
        foreach (Einstellungen::$segments as $segs) {
            if (isset(Installer::$segmentStatus[$segs]) && Installer::$segmentStatus[$segs] != 200) {
                continue;
            }

            if (!is_callable("{$segs}::" . $name)) {
                continue;
            }

            try {
                $tmp = call_user_func("{$segs}::" . $name, $data);
                $res = array_merge($res, $tmp);
            } catch (Exception $e) {
                $message = "Absturz des Aufrufs {$segs}::" . $name;
                self::errorHandler($message, $e);
                Installer::$segmentStatus[$segs] = 500;
            }
        }
        Installation::log(array('text' => 'beende Funktion'));
        return $res;
    }

    /**
     * ruft in allen Segmenten die Methode platformSetting($data) auf und
     * sammelt die Ergebnisse zusammen
     * @param string[][] $data Die Serverdaten
     * @return string[] die Zusammenstellung der Serverdaten 'title'=>data
     */
    public static function collectPlatformSettings($data) {
        Installation::log(array('text' => 'starte Funktion'));
        $settings = array();
        foreach (Einstellungen::$segments as $segs) {
            if (isset(Installer::$segmentStatus[$segs]) && Installer::$segmentStatus[$segs] != 200) {
                continue;
            }

            if (!is_callable("{$segs}::platformSetting")) {
                continue;
            }

            $settings = array_merge($settings, $segs::platformSetting($data));
        }
        Installation::log(array('text' => 'beende Funktion'));
        return $settings;
    }

    /**
     * Extrahiert die relevanten Daten der Plattform und erzeugt
     * daraus ein Platform-Objekt
     *
     * @param string[][] $data Die Serverdaten
     * @return Patform Die Plattformdaten
     */
    public static function PlattformZusammenstellen($data, $dbuserPostfix='')
    {
        Installation::log(array('text'=>'starte Funktion'));
        $settings = self::collectPlatformSettings($data);

        // hier aus den Daten ein Plattform-Objekt zusammenstellen
        $platform = Platform::createPlatform(
                                            $data['PL']['url'],
                                            $data['DB']['db_path'],
                                            $data['DB']['db_name'],
                                            null,
                                            null,
                                            $data['DB']['db_user_operator'].$dbuserPostfix,
                                            $data['DB']['db_passwd_operator'.$dbuserPostfix],
                                            $data['PL']['temp'],
                                            $data['PL']['files'],
                                            $data['PL']['urlExtern'],
                                            $settings
                                            );
        $tempPlatform = clone $platform;
        $tempPlatform->setDatabaseRootPassword('*****');
        $tempPlatform->setDatabaseOperatorPassword('*****');
        Installation::log(array('text' => 'Platform = ' . json_encode($tempPlatform)));
        Installation::log(array('text' => 'beende Funktion'));
        return $platform;
    }

    /**
     * Ermittelt alle vorhandenen Serverkonfigurationsdateien
     * aus dem config Ordners
     *
     * @return string[] Die Dateipfade
     */
    public static function GibServerDateien() {
        Installation::log(array('text' => 'starte Funktion'));
        $serverFiles = array();
        Einstellungen::$path = dirname(__FILE__) . '/../config';
        Einstellungen::generatepath(Einstellungen::$path);
        if (is_dir(Einstellungen::$path)) {
            Installation::log(array('text' => 'lese Konfigurationen'));
            try {
                $handle = opendir(Einstellungen::$path);
            } catch (Exception $e) {
                // der Ordner konnte nicht zugegriffen werden
                Installation::log(array('text' => Einstellungen::$path . ' existiert nicht oder es fehlt die Zugriffsberechtigung.', 'logLevel' => LogLevel::ERROR));
                Installer::$messages[] = array('text' => Einstellungen::$path . ' existiert nicht oder es fehlt die Zugriffsberechtigung.', 'type' => 'error');
                return $serverFiles;
            }

            if ($handle !== false) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }

                    $serverFiles[] = $file;
                }
                closedir($handle);
            }
        } else {
            Installation::log(array('text' => 'der Pfad existiert nicht: ' . Einstellungen::$path, 'logLevel' => LogLevel::ERROR));
            return array();
        }


        Installation::log(array('text' => 'ermittelte Konfigurationen = ' . implode(',', $serverFiles)));
        Installation::log(array('text' => 'beende Funktion'));
        return $serverFiles;
    }

    /**
     * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively.
     * Will return an array of the form
     * array(
     *   'files' => [],
     *   'dirs'  => [],
     * )
     * @author sreekumar
     * @param string $root
     * @result array
     */
    public static function read_all_files($root = '.', $exclude = array()) {

        Installation::log(array('text' => 'starte Funktion'));
        $files = array('files' => array(), 'dirs' => array());
        $directories = array();
        $root = realpath($root);

        foreach ($exclude as &$ex) {
            $ex = realpath($ex);
        }

        $last_letter = $root[strlen($root) - 1];
        $root = ($last_letter == DIRECTORY_SEPARATOR) ? $root : $root . DIRECTORY_SEPARATOR;

        $directories[] = $root;

        while (sizeof($directories)) {
            $dir = array_pop($directories);

            try {
                $handle = opendir($dir);
            } catch (Exception $e) {
                // der Ordner konnte nicht zugegriffen werden
                Installation::log(array('text' => $dir . ' existiert nicht oder es fehlt die Zugriffsberechtigung.', 'logLevel' => LogLevel::ERROR));
                Installer::$messages[] = array('text' => $dir . ' existiert nicht oder es fehlt die Zugriffsberechtigung.', 'type' => 'error');
                return $serverFiles;
            }

            if ($handle !== false) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $file = $dir . $file;
                    if (!in_array($file, $exclude)) {
                        if (is_dir($file)) {
                            $directory_path = $file . DIRECTORY_SEPARATOR;
                            array_push($directories, $directory_path);
                            $files['dirs'][] = $directory_path;
                        } elseif (is_file($file)) {
                            $files['files'][] = $file;
                        }
                    }
                }
                closedir($handle);
            }
        }

        Installation::log(array('text' => 'beende Funktion'));
        return $files;
    }

    /**
     * speichert einen Eintrag in der aktuellen log-Datei oder legt eine an
     * @param type $data
     */
    public static function log($data = array()) {
        if (!isset($data['text'])) {
            $data['text'] = null;
        }

        if (!isset($data['logLevel'])) {
            $data['logLevel'] = LogLevel::INFO;
        }

        // wenn in dieser Instanz noch keine Log-Datei verwendet wurde, wird
        // eine neue erzeugt
        if (!isset(Installation::$logFile)) {
            $path = dirname(__FILE__) . '/../logs';
            Einstellungen::generatepath($path);
            Installation::$logFile = $path . '/install_' . date('Ymd_His') . '.log';
        }


        if (!isset($data['name'])) {
            $info = debug_backtrace();
            $infoString = '';
            if (isset($info[1])) {
                $callerInfo = $info[1];
                if (isset($callerInfo['class'])) {
                    $infoString .= $callerInfo['class'];
                }
                if (isset($callerInfo['type'])) {
                    $infoString .= $callerInfo['type'];
                }
                if (isset($callerInfo['function'])) {
                    $infoString .= $callerInfo['function'];
                }
            } else {
                $callerInfo = $info[0];
                if (isset($callerInfo['file'])) {
                    $infoString .= basename($callerInfo['file']);
                }
            }

            if (isset($info[0]['line'])) {
                $infoString .= ':' . $info[0]['line'] . ')';
            } elseif (isset($info[1]['line'])) {
                $infoString .= ' (' . $info[1]['line'] . ')';
            }
            $data['name'] = $infoString;
        }

        // es wird die zentrale Logger-Klasse (Assistants) verwendet
        Logger::Log($data['text'], $data['logLevel'], false, Installation::$logFile, LogLevel::$names[$data['logLevel']] . ',' . $data['name'], false, Installation::$logLevel);
    }

    /**
     * löst einen Sprachplatzhalter auf
     * @param string $area der Hauptbereich
     * @param string $cell der Name des Platzhalters
     * @param string $name der Templatename (eventuell sind mehrere Sprachdateien offen)
     * @param string[] $params Werte, die eingesetzt werden sollen ('title'=>value)
     * @return string der Text
     */
    public static function Get($area, $cell, $name = 'default',
            $params = array()) {
        $value = Language::Get($area, $cell, $name, $params);
        if ($value === Language::$errorValue) {
            Installation::log(array('text' => Language::Get('main', 'unknownPlaceholder', 'default', array('name' => $area . '::' . $cell)), 'logLevel' => LogLevel::ERROR));
        }
        return $value;
    }

    /**
     * wandelt eine int in eine römische Folge um
     * @see http://www.roemische-ziffern.de/Roemische-Zahlen-PHP-berechnen.html
     * @param type $arabische_zahl
     * @return string die Darstellung in römischen Ziffern
     */
    public static function intToRoman($arabische_zahl) {
        $ar_r = array("M", "CM", "D", "CD", "C", "XC", "L", "XL", "X", "IX", "V", "IV", "I");
        $ar_a = array(1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1);
        $roemische_zahl = "";

        for ($count = 0; $count < count($ar_a); $count++) {
            while ($arabische_zahl >= $ar_a[$count]) {
                $roemische_zahl .= $ar_r[$count];
                $arabische_zahl -= $ar_a[$count];
            }
        }
        return $roemische_zahl;
    }

    /**
     * nutzt die log-Methode um eine Exception ins Log zu schreiben
     * @param string $text ein zusätzlicher Text
     * @param Exception $e der Exception-Handler
     */
    public static function errorHandler($text, Exception $e) {
        $info = debug_backtrace();
        $infoString = '';
        if (isset($info[1])) {
            $callerInfo = $info[1];
            if (isset($callerInfo['class'])) {
                $infoString .= $callerInfo['class'];
            }

            if (isset($callerInfo['type'])) {
                $infoString .= $callerInfo['type'];
            }

            if (isset($callerInfo['function'])) {
                $infoString .= $callerInfo['function'];
            }
        } else {
            $callerInfo = $info[0];
            if (isset($callerInfo['file'])) {
                $infoString .= basename($callerInfo['file']);
            }
        }

        if (isset($info[0]['line'])) {
            $infoString .= ':' . $info[0]['line'] . ')';
        } elseif (isset($info[1]['line'])) {
            $infoString .= ' (' . $info[1]['line'] . ')';
        }
        $name = $infoString;
        self::log(array('logLevel' => LogLevel::ERROR, 'text' => $text . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString(), 'name' => $name));
    }

    /**
     * führt einen Befehl im Hintergrund aus
     * @param string der Befehl
     */
    public static function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B " . $cmd, "r"));
        } else {
            exec($cmd . " > /dev/null &");
        }
    }

}
