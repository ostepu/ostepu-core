<?php
class DokuGenerierung {

    private static $initialized = false;
    public static $name = 'dokuGenerierung';
    public static $installed = false;
    public static $page = 8;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate = 'DokuGenerierung';
    public static $onEvents = array(
        'generateConfFiles' => array(
            'name' => 'actionDokuGenerierungAusfuehren',
            'event' => array('actionDokuGenerierungAusfuehren'),
            'procedure' => 'generateConfFiles',
            'enabledInstall' => true
        )
    );

    public static function getDefaults($data) {
        $res = array();
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

        self::$initialized = true;
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }

    public static function show($console, $result, $data) {
        if (!Einstellungen::$accessAllowed) {
            return;
        }

        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $text = '';
        $text .= Design::erstelleBeschreibung($console, Installation::Get('main', 'description', self::$langTemplate));

        if (self::$onEvents['generateConfFiles']['enabledInstall']) {
            $text .= Design::erstelleZeile($console, Installation::Get('generateConfFiles', 'desc', self::$langTemplate), 'e', Design::erstelleSubmitButton(self::$onEvents['generateConfFiles']['event'][0], Installation::Get('generateConfFiles', 'exec', self::$langTemplate)), 'h');
        }

        if (isset($result[self::$onEvents['generateConfFiles']['name']])) {
            $result = $result[self::$onEvents['generateConfFiles']['name']];
            $fail = $result['fail'];
            $error = $result['error'];
            $errno = $result['errno'];
            $content = $result['content'];
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);            
        }
        
        echo Design::erstelleBlock($console, Installation::Get('main', 'title', self::$langTemplate), $text);

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }
    
    private static function sortComponentF($a,$b){ 
        if ($a==$b) return 0;
        $sortComponent = array('name', 'description', 'version', 'baseURI', 'base', 'type', 'classFile', 'className', 'files', 'links', 'connector');
        $posA = array_search($a, $sortComponent);
        $posB = array_search($b, $sortComponent);
        if ($posA===false) return 1;
        if ($posB===false) return 1;
        return ($posA<$posB)?-1:1;
    }  
    
    private static function sortLinksF($a,$b){ 
        if ($a==$b) return 0;
                    $sortLinks = array('name', 'target', 'description','links');
        $posA = array_search($a, $sortLinks);
        $posB = array_search($b, $sortLinks);
        if ($posA===false) return 1;
        if ($posB===false) return 1;
        return ($posA<$posB)?-1:1;
    }    
    
    private static function sortConnectorF($a,$b){ 
        if ($a==$b) return 0;
                    $sortConnector = array('name', 'target', 'description', 'priority', 'links');
        $posA = array_search($a, $sortConnector);
        $posB = array_search($b, $sortConnector);
        if ($posA===false) return 1;
        if ($posB===false) return 1;
        return ($posA<$posB)?-1:1;
    }

    private static function sortCommandsF($a,$b)
    { 
        if ($a==$b) return 0;
        $sortCommands = array('name', 'path', 'method', 'description', 'placeholder', 'inputType', 'outputType', 'callback', 'singleOutput');
        $posA = array_search($a, $sortCommands);
        $posB = array_search($b, $sortCommands);
        if ($posA===false) return 1;
        if ($posB===false) return 1;
        return ($posA<$posB)?-1:1;
    }  

    public static function generateConfFiles($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        $res = array();
        $res['errorMessages'] = array();

        $componentFiles = Paketverwaltung::getComponentFilesFromSelectedPackages($data, $fail, $errno, $error);
        
        if ($componentFiles !== null){
            foreach($componentFiles as $definition){
                if (isset($definition['conf'])){
                    $file = $definition['conf'];  

                    $content = json_decode(file_get_contents($file), true);

                    if (isset( $content['name'])){
                        $myName = $content['name'];
                    } else {
                        $myName = '???';
                    }
                    
                    $commands = dirname($file).'/Commands.json';
                    if (file_exists($commands)){
                        $content = json_decode(file_get_contents($commands), true);

                        foreach ($content as $i => $elem){
                            if (!isset($elem['description'])){
                                $content[$i]['description'] = array();
                            }
                            if (isset($elem['description']) && is_string($content[$i]['description'])){
                                $content[$i]['description'] = array();
                            }
                            
                            if (!isset($elem['description']['de'])){
                                $content[$i]['description']['de'] = "???";
                            }
                                    
                            $commandsMapping = array('getExistsPlatform'=>'pr\u00fcft, ob die Tabelle und die Prozeduren existieren und die Komponente generell vollständig installiert ist',
                                'addPlatform'=>'installiert die zugeh\u00f6rige Tabelle und die Prozeduren f\u00fcr diese Plattform',
                                'postSamples'=>'erzeugt Zufallsdaten (courseAmount = Anzahl der Veranstaltungen, userAmount = Anzahl der Nutzer), anhand der Vorgabe',
                                'getApiProfiles'=>'liefert `GateProfile`-Objekte, welche unsere Befehle in die Standardprofile von CGate einsortieren',
                                'getSamplesInfo'=>'liefert die Bezeichner der betroffenen Tabellen',
                                'deleteCourse'=>'entfernt die Komponente aus der Veranstaltung',
                                'deletePlatform'=>'entfernt die Komponente und ihre installierten Bestandteile aus der Plattform',
                                'addCourse'=>'f\u00fcgt '.$myName.' zur Veranstaltung hinzu');
                                
                            if (isset($elem['description']['de']) && $elem['description']['de'] == '???' && isset($elem['name']) && isset($commandsMapping[$elem['name']])){
                                $content[$i]['description']['de'] = $commandsMapping[$elem['name']];
                            }
                            
                            if (isset($elem['placeholder'])){
                                foreach($elem['placeholder'] as $b => $placeholder){
                                    if (!isset($placeholder['description'])){
                                        $content[$i]['placeholder'][$b]['description'] = array();
                                    }
                                    if (isset($placeholder['description']) && is_string($placeholder['description'])){
                                        $content[$i]['placeholder'][$b]['description'] = array();
                                    }
                                    if (!isset($placeholder['description']['de'])){
                                        $content[$i]['placeholder'][$b]['description']['de'] = '???';
                                    }                        
                                    
                                    if (isset($placeholder['description']['de']) && $placeholder['description']['de'] == '???'){
                                        $mapping = array('userid'=>'die ID eines Nutzers oder ein Nuzername (`User`)',
                                            'courseid'=>'die ID einer Veranstaltung (`Course`)',
                                            'statusid'=>'die ID eines Veranstaltungsstatus (siehe `DBCourseStatus::getStatusDefinition()`)',
                                            'esid'=>'die ID einer \u00dcbungsserie (`ExerciseSheet`)',
                                            'seid'=>'die ID einer Sitzung (`Session`)',
                                            'redid'=>'die ID eines Redirect-Eintrags (`Redirect`)',
                                            'mid' => 'die ID einer Korrektur (`Marking`)',
                                            'eid' => 'die ID einer Aufgabe (`Exercise`)',
                                            'esid' => 'die ID einer Übungsserie (`ExerciseSheet`)',
                                            'suid' => 'die ID einer Einsendung (`Submission`)',
                                            'fileid' => 'die ID einer Datei (`File`)',
                                            'beginStamp' => 'der Anfangsstempel (Unix-Zeitstempel)',
                                            'endStamp' => 'der Endstempel (Unix-Zeitstempel)',
                                            'hash' => 'der Hashwert einer Datei',
                                            'base' => 'die Basis eines MimeType Bsp.: text, application',
                                            'type' => 'der explizite Typ eines MimeType Bsp.: c++, pdf',
                                            'exid' => 'die ID einer externen ID (`ExternalId`)',
                                            'etid' => 'eine Aufgabetyp ID (`ExerciseType`)',
                                            'eftid' => 'die ID eines Dateityps für eine Aufgabe (`ExerciseFileType`)',
                                            'courseAmount' => 'eine Anzahl an Elementen für diese Veranstaltung',
                                            'apid' => 'die ID einer Zulassungsbedingung (`ApprovalCondition`)',
                                            'setname'=>'der Name einer Einstellung `SET_name` aus `Setting`',
                                            'setid' => 'die ID einer Einstellung (`Setting_X`), bestehend aus der ID und der Veranstaltung',
                                            'locname' => 'der Ortsbezeichner einer Weiterleitung, an welchem diese gezeichnet wird (beispielsweise `sheet`, für alle Übungsserien)',
                                            'memberid'=>'die Nutzer-ID des Gruppenmitglieds',
                                            'authType'=>'ein Authentifizierungstyp (`noAuth`, `httpAuth`, `tokenAuth`',
                                            'gaid'=>'die ID einer Authentifizierung (`GateAuth`)',
                                            'gpid'=>'die ID eines Gate-Profile (`GateProfile`)',
                                            'grid'=>'die ID einer Gate-Regel (`GateRule`)',
                                            'gpname'=>'der Name eines Gate-Profils',
                                            );
                                        if (isset($mapping[$placeholder['name']])){
                                            $content[$i]['placeholder'][$b]['description']['de'] = $mapping[$placeholder['name']];
                                        }
                                    }
                                }
                            }
                            uksort($content[$i],"self::sortCommandsF");
                        }
                                
                        $content = array_values($content);
                              
                        $text = json_encode($content, JSON_PRETTY_PRINT );
                        $text = str_replace(array("\\u00f6","\\u00fc","\\u00e4","\\u00d6","\\u00dc","\\u00c4","\\u00df"),array("ö","ü","ä","Ö","Ü","Ä","ß"),$text);
                        $text = str_replace(array("\\ö","\\ü","\\ä","\\Ö","\\Ü","\\Ä","\\ß"),array("ö","ü","ä","Ö","Ü","Ä","ß"),$text);
                        file_put_contents($commands, $text);
                    }

                    $content = json_decode(file_get_contents($file), true);
                    
                    
                    if (!isset($content['description'])){
                        $content['description'] = array();
                    }
                    if (isset($content['description']) && is_string($content['description'])){
                        $content['description'] = array();
                    } 
                    if (!isset($content['description']['de'])){
                        $content['description']['de'] = '???';
                    }
                    unset($content['link']);
                    
                    if (isset($content['links'])){
                        foreach($content['links'] as $i => $link){
                            if (!isset($link['description'])){
                                $content['links'][$i]['description'] = array();
                            }
                            if (isset($link['description']) && is_string($link['description'])){
                                $content['links'][$i]['description'] = array();
                            } 
                            if (!isset($link['description']['de'])){
                                $content['links'][$i]['description']['de'] = "f\u00fcr den Befehl ".$link['name'];
                            }
                            if (isset($link['description']['de']) && $link['description']['de'] == '???'){
                                $content['links'][$i]['description']['de'] = "f\u00fcr den Befehl ".$link['name'];
                            }
                            uksort($content['links'][$i],"self::sortLinksF");                
                        }            
                    }
                    if (isset($content['connector'])){
                        foreach($content['connector'] as $i => $connector){
                            if (!isset($connector['description'])){
                                $content['connector'][$i]['description'] = array();
                            }
                            if (isset($connector['description']) && is_string($connector['description'])){
                                $content['connector'][$i]['description'] = array();
                            } 
                            if (!isset($connector['description']['de'])){
                                $content['connector'][$i]['description']['de'] = "???";
                            }
                              
                            
                            if (isset($connector['description']['de']) && $connector['description']['de'] == '???'){
                                    $mapping = array('requestCLocalObjectRequest'=>array('CLocalObjectRequest', 'damit '.$myName.' als lokales Objekt aufgerufen werden kann'),
                                    'postPlatformCInstall'=>array('CInstall', 'der Installationsassistent soll uns bei der Plattforminstallation aufrufen'),
                                    'postPlatformReadUserCInstall'=>array('CInstall', 'der Installationsassistent soll uns bei der Installation des Read-User aufrufen'),
                                    'postPlatformWriteUserCInstall'=>array('CInstall', 'der Installationsassistent soll uns bei der Installation des Write-User aufrufen'),
                                    'postPlatformSetupUserCInstall'=>array('CInstall', 'der Installationsassistent soll uns bei der Installation des Setup-User aufrufen'),
                                    'postSamplesCInstall'=>array('CInstall', 'wir wollen bei Bedarf Beispieldaten erzeugen'),
                                    'getDescFilesTDocuView'=>array('TDocuView', 'die Entwicklerdokumentation soll unsere Beschreibungsdatei nutzen'),
                                    'getComponentProfilesTApiConfiguration'=>array('TApiConfiguration', 'damit unsere Aufrufe in die Standardprofile der CGate einsortiert werden'),
                                    'postCourseLCourse'=>array('LCourse', 'wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden'),
                                    'deleteCourseLCourse'=>array('LCourse', 'wenn eine Veranstaltung gel\u00f6scht wird, dann m\u00fcssen auch unsere Tabellen entfernt werden'),
                                    'getCleanAmountCInstall'=>array('CInstall', 'ermittelt die Anzahl der zu bereinigenden Tabellenzeilen'),
                                    'deleteCleanCInstall'=>array('CInstall', 'bereinigt unsere Datenbanktabellen und temporären Daten'),
                                    'requestCHelp'=>array('CHelp','hier werden Hilfedateien beim zentralen Hilfesystem angemeldet, sodass sie über ihre globale Adresse abgerufen werden können'),
                                    'postCourseLProcessor'=>array('LProcessor', 'wenn eine neue Veranstaltung angelegt wird, dann wollen wir auch aufgerufen werden')
                                    );
                                    if (isset($mapping[$connector['name'].$connector['target']])){
                                        $myMapping = $mapping[$connector['name'].$connector['target']];
                                        if ($connector['target'] == $myMapping[0]){
                                            $content['connector'][$i]['description']['de'] = $myMapping[1];
                                        }
                                    }
                            }
                            uksort($content['connector'][$i],"self::sortConnectorF");
                        }              
                    }
                    
                    uksort($content,"self::sortComponentF");
                    $text = json_encode($content, JSON_PRETTY_PRINT );
                    $text = str_replace(array("\\u00f6","\\u00fc","\\u00e4","\\u00d6","\\u00dc","\\u00c4","\\u00df"),array("ö","ü","ä","Ö","Ü","Ä","ß"),$text);
                    $text = str_replace(array("\\ö","\\ü","\\ä","\\Ö","\\Ü","\\Ä","\\ß"),array("ö","ü","ä","Ö","Ü","Ä","ß"),$text);
                    file_put_contents($file, $text);
                }
            }
        }

        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return $res;
    }

}