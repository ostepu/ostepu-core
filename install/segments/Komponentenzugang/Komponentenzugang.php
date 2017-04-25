<?php

/**
 * @file Komponentenzugang.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
#region Komponentenzugang
class Komponentenzugang {

    private static $initialized = false;
    public static $name = 'accessComponents';
    public static $installed = false;
    public static $page = 4;
    public static $rank = 75;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    private static $langTemplate = 'Komponentenzugang';
    public static $onEvents = array('addProfile' => array('name' => 'accessComponentsAddProfile',
                                                          'event' => array('accessComponentsAddProfile'),
                                                          'procedure' => 'addProfile'),
                                    'selectProfile' => array('name' => 'accessComponentsSelectProfile',
                                                             'event' => array('accessComponentsSelectProfile'),
                                                             'procedure' => 'selectProfile'),
                                    'addAuth' => array('name' => 'accessComponentsAddAuth',
                                                       'event' => array('accessComponentsAddAuth'),
                                                       'procedure' => 'addAuth'),
                                    'addRule' => array('name' => 'accessComponentsAddRule',
                                                       'event' => array('accessComponentsAddRule'),
                                                       'procedure' => 'addRule'),
                                    'deleteRule' => array('name' => 'accessComponentsDeleteRule',
                                                          'event' => array('accessComponentsDeleteRule'),
                                                          'procedure' => 'deleteRule'),
                                    'deleteAuth' => array('name' => 'accessComponentsDeleteAuth',
                                                          'event' => array('accessComponentsDeleteAuth'),
                                                          'procedure' => 'deleteAuth'),
                                    'deleteProfile' => array('name' => 'accessComponentsDeleteProfile',
                                                             'event' => array('accessComponentsDeleteProfile'),
                                                             'procedure' => 'deleteProfile'),
                                    'saveProfile' => array('name' => 'accessComponentsSaveProfile',
                                                           'event' => array('accessComponentsSaveProfile'),
                                                           'procedure' => 'saveProfile'),
                                    'synchronizeDatabase' => array('name' => 'accessComponentsSynchronizeDatabase',
                                                           'event' => array('accessComponentsSynchronizeDatabase', 'install', 'update'),
                                                           'procedure' => 'synchronizeDatabase'));

    public static function getDefaults() {
        return array(
            'coz_selectedProfile' => array('data[COZ][coz_selectedProfile]', null)
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
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['COZ']['coz_selectedProfile'], 'data[COZ][coz_selectedProfile]', $def['coz_selectedProfile'][1], true);
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
        $isUpdate = (isset($data['action']) && $data['action'] == 'update') ? true : false;

        $text = '';
        if (!$console) {
            $text .= Design::erstelleBeschreibung($console, Installation::Get('accessComponents', 'description', self::$langTemplate));

            $text .= Design::erstelleZeileShort($console, Design::erstelleSubmitButton(self::$onEvents['synchronizeDatabase']['event'][0], Installation::Get('accessComponents', 'synchronizeDatabase', self::$langTemplate)), 'h', Design::erstelleSubmitButton(self::$onEvents['selectProfile']['event'][0], Installation::Get('accessComponents', 'selectProfile', self::$langTemplate)), 'h');

            $profiles = self::getAllProfiles($data);
            
            $profileList = array();
            foreach ($profiles as $key => $profile){
                if ($profile === null){
                    unset($profiles[$key]);
                    continue;
                }
                $profileList[$profile->getId()] = ($profile->getName()!==null ? $profile->getName() : '&lt;noname&gt;');
            }
            
            if (count($profileList)>0){
                $text .= Design::erstelleZeileShort($console, '', '');
                
                // zeichnet die Profilauswahl
                $text .= Design::erstelleZeile($console,
                                               Installation::Get('accessComponents', 'existingProfiles', self::$langTemplate),
                                               'e',
                                               Design::erstelleAuswahlliste($console,
                                                                            $profileList,
                                                                            $data['COZ']['coz_selectedProfile'],
                                                                            'data[COZ][coz_selectedProfile]',
                                                                            array_keys($profileList)[0],
                                                                            true).
                                                Design::erstelleSubmitButton(self::$onEvents['addProfile']['event'][0], Installation::Get('accessComponents', 'addProfile', self::$langTemplate)),
                                               'v_c');
            } else {
                // wir zeichnen hier nur die Schaltfläche zum hinzufügen eines Profils
                 $text .= Design::erstelleZeileShort($console,
                                                     Installation::Get('accessComponents', 'existingProfiles', self::$langTemplate),
                                                     'e',
                                                     Design::erstelleSubmitButton(self::$onEvents['addProfile']['event'][0], Installation::Get('accessComponents', 'addProfile', self::$langTemplate)),
                                                     'v_c');   
            }
            
            
            if (!isset($data['COZ']['coz_selectedProfile']) && count($profiles)>0){
                $data['COZ']['coz_selectedProfile'] = $profiles[0]->getId();
            }
            
            $selProfile = null;
            // zeichne die Profile
            $selectedProfile = $data['COZ']['coz_selectedProfile'];
            if ($selectedProfile !== null){
                foreach ($profiles as $profile){
                    // aber nur, wenn es das ausgewählte Profil ist
                    if ($profile->getId() != $selectedProfile){
                        continue;
                    }
                    
                    $selProfile = $profile;
                    
                    $text .= Design::erstelleZeile($console,
                               Installation::Get('accessComponents', 'profileName', self::$langTemplate),
                               'e',
                               ($profile->getReadonly() ? $profile->getName() :
                                    Design::erstelleEingabezeile($console,
                                                            $profile->getName(),
                                                            'data[COZ][coz_selectedProfileName]',
                                                            '',
                                                            false)),
                               'v',
                               Design::erstelleSubmitButton(self::$onEvents['deleteProfile']['event'][0], Installation::Get('accessComponents', 'deleteProfile', self::$langTemplate)),
                               'h');

                    $auths = $profile->getAuths();
                    $authList = array();
                    foreach ($auths as $key => $auth){
                        if ($auth === null){
                            unset($auths[$key]);
                            continue;
                        }
                        $authList[$auth->getId()] = $auth->getId();
                    }

                    if (count($authList)>0){
                        $text .= Design::erstelleZeileShort($console, '', '');
                        
                        if (!isset($data['COZ']['coz_selectedAuth'])){
                            $data['COZ']['coz_selectedAuth'] = array_keys($authList)[0];
                        }
                
                        // zeichnet die Authentifizierungsauswahl
                        $text .= Design::erstelleZeile($console,
                                                       Installation::Get('accessComponents', 'existingAuths', self::$langTemplate),
                                                       'e',
                                                       Design::erstelleAuswahlliste($console,
                                                                                    $authList,
                                                                                    $data['COZ']['coz_selectedAuth'],
                                                                                    'data[COZ][coz_selectedAuth]',
                                                                                    array_keys($authList)[0],
                                                                                    true).
                                                       ($profile->getReadonly() ? '' :
                                                       Design::erstelleSubmitButton(self::$onEvents['addAuth']['event'][0], Installation::Get('accessComponents', 'addAuth', self::$langTemplate))),
                                                       'v_c');
                                                     
                        $selectedAuth = $data['COZ']['coz_selectedAuth'];
                        foreach($auths as $auth){
                            if ($auth->getId() != $selectedAuth){
                                continue;
                            } 
                            
                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'accessType', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? array('noAuth'=>'Frei', 'httpAuth'=>'HTTPAuth')[$auth->getType()] :
                                                           Design::erstelleAuswahlliste($console,
                                                                                        array('noAuth'=>'Frei', 'httpAuth'=>'HTTPAuth'),
                                                                                        $auth->getType(),
                                                                                        'data[COZ][coz_selectedAuthType]',
                                                                                        'noAuth',
                                                                                        false)),
                                                           'v');

                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'accessLogin', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? $auth->getLogin() :
                                                           Design::erstelleEingabezeile($console,
                                                                                        $auth->getLogin(),
                                                                                        'data[COZ][coz_selectedAuthLogin]',
                                                                                        '',
                                                                                        false)),
                                                           'v');
                                                           
                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'accessPasswd', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? $auth->getPasswd() :
                                                           Design::erstelleEingabezeile($console,
                                                                                        $auth->getPasswd(),
                                                                                        'data[COZ][coz_selectedAuthPasswd]',
                                                                                        '',
                                                                                        false)),
                                                           'v');

                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'accessParams', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? $auth->getParams() :
                                                           Design::erstelleEingabezeile($console,
                                                                                        $auth->getParams(),
                                                                                        'data[COZ][coz_selectedAuthParams]',
                                                                                        '',
                                                                                        false)),
                                                           'v',
                                                           ($profile->getReadonly() ? '' :
                                                           Design::erstelleSubmitButton(self::$onEvents['deleteAuth']['event'][0], Installation::Get('accessComponents', 'deleteAuth', self::$langTemplate))),
                                                           'h' );
                        }
                    } else {
                        // wir zeichnen hier nur die Schaltfläche zum hinzufügen einer Authentifizierung
                         $text .= Design::erstelleZeile($console,
                                                        Installation::Get('accessComponents', 'existingAuths', self::$langTemplate),
                                                        'e',
                                                        ($profile->getReadonly() ? '' :
                                                        Design::erstelleSubmitButton(self::$onEvents['addAuth']['event'][0], Installation::Get('accessComponents', 'addAuth', self::$langTemplate))),
                                                        'v_c');                        
                    }
                    
                    $rules = $profile->getRules();
                    $ruleList = array();
                    foreach ($rules as $key => $rule){
                        if ($rule === null){
                            unset($rules[$key]);
                            continue;
                        }
                        $ruleList[$rule->getId()] = $rule->getId();
                    }
                    
                    if (count($ruleList)>0){
                        $text .= Design::erstelleZeileShort($console, '', '');
                        
                        if (!isset($data['COZ']['coz_selectedRule'])){
                            $data['COZ']['coz_selectedRule'] = array_keys($ruleList)[0];
                        }
                
                        // zeichnet die Regelauswahl
                        $text .= Design::erstelleZeile($console,
                                                       Installation::Get('accessComponents', 'existingRules', self::$langTemplate),
                                                       'e',
                                                       Design::erstelleAuswahlliste($console,
                                                                                    $ruleList,
                                                                                    $data['COZ']['coz_selectedRule'],
                                                                                    'data[COZ][coz_selectedRule]',
                                                                                    array_keys($ruleList)[0],
                                                                                    true).
                                                       ($profile->getReadonly() ? '' :
                                                       Design::erstelleSubmitButton(self::$onEvents['addRule']['event'][0], Installation::Get('accessComponents', 'addRule', self::$langTemplate))),
                                                       'v_c');
                                             
                        $selectedRule = $data['COZ']['coz_selectedRule'];
                        
                        // zeigt, wie der Befehl aufgerufen werden kann
                        if (isset($data['PL']['urlExtern'])){
                            foreach($rules as $rule){
                                // suche zunächst die aktuelle Regel (ausgewählt)
                                if ($rule->getId() != $selectedRule){
                                    continue;
                                }
                                
                                // wir behandeln nur httpCall-Aufrufe
                                if ($rule->getType() == 'httpCall'){
                                    $call = $rule->getContent();
                                    $call = explode(' ',$call);
                                    
                                    // der Aufbau muss sein: METHODE BEFEHL
                                    if (count($call) != 2){
                                        break;
                                    }
                                        
                                    $list = Einstellungen::getLinks('gate', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
                                    
                                    if (count($list)==0){
                                        break;
                                    }
                                    
                                    $list = $list[0];
    
                                    $text .= Design::erstelleZeileShort($console,
                                                                        Installation::Get('accessComponents', 'ruleUrl', self::$langTemplate),
                                                                        'e',
                                                                        $call[0] . ' '. $list->getAddress() . '/interface/' . $profile->getName() . '/' . $rule->getComponent() . $call[1],
                                                                        'v break');
                                }
                                break;
                            }
                        }
                                                     
                        foreach($rules as $rule){
                            if ($rule->getId() != $selectedRule){
                                continue;
                            } 
                            
                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'ruleType', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? array('httpCall'=>'HTTPCall')[$rule->getType()] :
                                                           Design::erstelleAuswahlliste($console,
                                                                                        array('httpCall'=>'HTTPCall'),
                                                                                        $rule->getType(),
                                                                                        'data[COZ][coz_selectedRuleType]',
                                                                                        'httpCall',
                                                                                        false)),
                                                           'v');

                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'ruleComponent', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? $rule->getComponent() :
                                                           Design::erstelleEingabezeile($console,
                                                                                        $rule->getComponent(),
                                                                                        'data[COZ][coz_selectedRuleComponent]',
                                                                                        '',
                                                                                        false)),
                                                           'v');

                            $text .= Design::erstelleZeile($console,
                                                           Installation::Get('accessComponents', 'ruleContent', self::$langTemplate),
                                                           'e',
                                                           ($profile->getReadonly() ? $rule->getContent() :
                                                           Design::erstelleEingabezeile($console,
                                                                                        $rule->getContent(),
                                                                                        'data[COZ][coz_selectedRuleContent]',
                                                                                        '',
                                                                                        false)),
                                                           'v',
                                                           ($profile->getReadonly() ? '' :
                                                           Design::erstelleSubmitButton(self::$onEvents['deleteRule']['event'][0], Installation::Get('accessComponents', 'deleteRule', self::$langTemplate))),
                                                           'h');
                        }
                    } else {
                        // wir zeichnen hier nur die Schaltfläche zum hinzufügen einer Regel
                         $text .= Design::erstelleZeile($console,
                                                        Installation::Get('accessComponents', 'existingRules', self::$langTemplate),
                                                        'e',
                                                        ($profile->getReadonly() ? '' :
                                                        Design::erstelleSubmitButton(self::$onEvents['addRule']['event'][0], Installation::Get('accessComponents', 'addRule', self::$langTemplate))),
                                                        'v_c');
                    }
                    
                    break;
                }
            }
            
            if (isset($data['COZ']['coz_selectedProfile']) && isset($selProfile) && !$selProfile->getReadonly()){
                 $text .= Design::erstelleZeileShort($console,
                                                     Design::erstelleSubmitButton(self::$onEvents['saveProfile']['event'][0], Installation::Get('accessComponents', 'saveProfile', self::$langTemplate)),
                                                     'h_c');
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('accessComponents', 'title', self::$langTemplate), $text);
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
    }
    
    private static $cachedProfiles = null;
    private static $cachedExternalProfiles = null;
    
    public static function getAllExternalProfiles($data, $refreshCachedData = false){
        if (self::$cachedExternalProfiles !== null && !$refreshCachedData){
            return self::$cachedExternalProfiles;
        }

        // sammelt Profildefinitionen von anderen Segmenten ein (diese stehen nicht in der Datenbank)
        self::$cachedExternalProfiles = Installation::collect('getAllExternalProfiles',$data, array(__CLASS__));
        
        // wir müssen noch die Definitionen von den Komponenten einsammeln
        $list = Einstellungen::getLinks('getComponentProfiles', dirname(__FILE__), '/tapiconfiguration_cconfig.json');

        $multiRequestHandle = new Request_MultiRequest();

        for ($i = 0; $i < count($list); $i++) {
            $handler = Request_CreateRequest::createGet($list[$i]->getAddress() . '/api/profiles', array(), '');
            $multiRequestHandle->addRequest($handler);
        }

        $answer = $multiRequestHandle->run();
        for ($i = 0; $i < count($list); $i++) {
            $result = $answer[$i];
            if (isset($result['content']) && isset($result['status']) && $result['status'] === 200) {
                $new = GateProfile::decodeGateProfile($result['content']);
                if (!is_array($new)){
                    $new = array($new);
                }
                self::$cachedExternalProfiles = array_merge(self::$cachedExternalProfiles, $new);
            } else {
                Installation::log(array('text' => Installation::Get('accessComponents', 'errorGetApiProfiles', self::$langTemplate, array('component'=>$list[$i]->getTargetName())), 'logLevel' => LogLevel::ERROR));
            }
        }
        
        self::$cachedExternalProfiles = self::mergeProfiles(self::$cachedExternalProfiles);
        
        // für public soll ein Auth installiert werden
        if (isset(self::$cachedExternalProfiles['public'])){
            self::$cachedExternalProfiles['public']->addAuth(GateAuth::createGateAuth(null,'noAuth',null,null,null,null));
        }

        return self::$cachedExternalProfiles;
    }
    
    // vereint die Menge der Regeln und Auths aller Profile mit dem selben Namen in $profileList
    public static function mergeProfiles($profileList){
        $newProfiles = array();
        foreach($profileList as $profile){
            $name = $profile->getName();
            if ($name === null) continue; // das Profil muss einen Namen haben
            if (!isset($newProfiles[$name])){
                if ($profile->getRules() === null)$profile->setRules(array());
                if ($profile->getAuths() === null)$profile->setAuths(array());
                $newProfiles[$name] = $profile;
            } else {
                if ($profile->getRules() !== null){
                    $rules = $newProfiles[$name]->getRules();
                    $rules = array_merge($rules, $profile->getRules());
                    $newProfiles[$name]->setRules($rules);
                }
                if ($profile->getAuths() !== null){
                    $auths = $newProfiles[$name]->getAuths();
                    $auths = array_merge($auths, $profile->getAuths());
                    $newProfiles[$name]->setAuths($auths);
                }
            }
        }
        return $newProfiles;
    }
    
    public static function getAllProfiles($data, $refreshCachedData = false){
        // wenn die Profile bereits geladen wurden, müssen wir sie nicht nochmal ermitteln
        if (self::$cachedProfiles !== null && !$refreshCachedData){
            return self::$cachedProfiles;
        }

        self::$cachedProfiles = array();
        
        // die Profile von außen benötigen IDs
        // TODO: die externen Profile korrekt in die Datenbank laden

        $list = Einstellungen::getLinks('getAllProfiles', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        // es darf nur eine Verbindung existieren, sonst wissen wir beim Ändern nicht, wo wir anfragen sollen

        $multiRequestHandle = new Request_MultiRequest();

        for ($i = 0; $i < count($list); $i++) {
            $handler = Request_CreateRequest::createGet($list[$i]->getAddress() . '/gateprofile', array(), '');
            $multiRequestHandle->addRequest($handler);
        }

        $answer = $multiRequestHandle->run();
        for ($i = 0; $i < count($list); $i++) {
            $result = $answer[$i];
            if (isset($result['content']) && isset($result['status']) && $result['status'] === 200) {
                $new = GateProfile::decodeGateProfile($result['content']);
                if (!is_array($new)){
                    continue;
                }
                self::$cachedProfiles = array_merge(self::$cachedProfiles, $new);
            }
        }

        return self::$cachedProfiles;
    }
    
    public static function getExternalComponents($data){
        // wir wollen hier Komponenten definieren, welche dann von dem Segment "Komponenten erstellen" in der DB erzeugt werden sollen
        $profiles = self::getAllProfiles($data);
        $components = array();
        
        foreach($profiles as $profile){
            $targets = array();
            
            $rules = $profile->getRules();
            foreach($rules as $rule){
                $targets[$rule->getComponent()] = 1;
            }
            
            $targets = array_keys($targets);
            
            foreach($targets as $target){
                $comName = $profile->getName().'/'.$target;
                if (isset($components[$comName])) continue;
                
                $name = ucfirst($profile->getName()).'Interface'.$target;
                $com = array('name'=>$name,
                             'type'=>'clone',
                             'base'=>'CGate',
                             'baseURI'=>'/interface/'.$comName,
                             'initialization'=>'virtual');
                
                $components[$comName] = $com;
            }
        }
        
        return array_values($components);
    }
    
    public static function getComponentDefinitionExtension($data){
        // wir wollen hier Komponenten erweitern, welche dann von dem Segment "Komponenten erstellen" bearbeiten lassen
        $profiles = self::getAllProfiles($data);
        $components = array();
        
        foreach($profiles as $profile){
            $targets = array();
            
            $rules = $profile->getRules();
            foreach($rules as $rule){
                $targets[$rule->getComponent()] = 1;
            }
            
            $targets = array_keys($targets);
            
            foreach($targets as $target){
                $name = ucfirst($profile->getName()).'Interface'.$target;
                $com = array('name'=>'CGate',
                             'links'=>array(array('name'=>'request','target'=>$target)));
                
                $components[] = $com;
            }
        }
        
        return $components;
    }

    public static function addProfile($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // ein neues Profil soll hinzugefügt werden
    
        $list = Einstellungen::getLinks('addProfile', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        
        $multiRequestHandle = new Request_MultiRequest();

        $newProfile = GateProfile::createGateProfile(null,
                                                     null);
        $newProfile->setStatus(200);
        for ($i = 0; $i < count($list); $i++) {
            $handler = Request_CreateRequest::createPost($list[$i]->getAddress() . '/gateprofile', array(), GateProfile::encodeGateProfile($newProfile));
            $multiRequestHandle->addRequest($handler);
        }

        $answer = $multiRequestHandle->run();      
        // TODO: der Aufruf muss ausgewertet werden    
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }
    
    public static function selectProfile($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // keine Aktion
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return array();
    }

    public static function addAuth($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // eine neue Authentifizierung hinzufügen
        
        if (isset($data['COZ']['coz_selectedProfile'])){
            $profileId = $data['COZ']['coz_selectedProfile'];
            $list = Einstellungen::getLinks('addAuth', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
            
            $multiRequestHandle = new Request_MultiRequest();

            $newAuth = GateAuth::createGateAuth(null,
                                                null,
                                                null,
                                                '',
                                                '',
                                                $profileId);

            for ($i = 0; $i < count($list); $i++) {
                $handler = Request_CreateRequest::createPost($list[$i]->getAddress() . '/gateauth', array(), GateAuth::encodeGateAuth($newAuth));
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();            
            // TODO: der Aufruf muss ausgewertet werden
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function addRule($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // eine Regel hinzufügen

        if (isset($data['COZ']['coz_selectedProfile'])){
            $profileId = $data['COZ']['coz_selectedProfile'];
            $list = Einstellungen::getLinks('addRule', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
            
            $multiRequestHandle = new Request_MultiRequest();

            $newRule = GateRule::createGateRule(null,
                                                'httpCall',
                                                'CSystem',
                                                'GET /timestamp',
                                                $profileId);
            for ($i = 0; $i < count($list); $i++) {
                $handler = Request_CreateRequest::createPost($list[$i]->getAddress() . '/gaterule', array(), GateRule::encodeGateRule($newRule));
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();
            // TODO: der Aufruf muss ausgewertet werden
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function deleteRule($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // eine Regel löschen
        
        if (isset($data['COZ']['coz_selectedRule'])){
            $id = $data['COZ']['coz_selectedRule'];
            $list = Einstellungen::getLinks('deleteRule', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
            
            $multiRequestHandle = new Request_MultiRequest();

            for ($i = 0; $i < count($list); $i++) {
                $handler = Request_CreateRequest::createDelete($list[$i]->getAddress() . '/gaterule/gaterule/'.$id, array(), '');
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();    
            // TODO: der Aufruf muss ausgewertet werden
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function deleteAuth($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // eine Authentifizierung löschen
        
        if (isset($data['COZ']['coz_selectedAuth'])){
            $id = $data['COZ']['coz_selectedAuth'];
            $list = Einstellungen::getLinks('deleteAuth', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
            
            $multiRequestHandle = new Request_MultiRequest();

            for ($i = 0; $i < count($list); $i++) {
                $handler = Request_CreateRequest::createDelete($list[$i]->getAddress() . '/gateauth/gateauth/'.$id, array(), '');
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();    
            // TODO: der Aufruf muss ausgewertet werden
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function deleteProfile($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // ein Profil löschen
        
        if (isset($data['COZ']['coz_selectedProfile'])){
            $id = $data['COZ']['coz_selectedProfile'];
            $list = Einstellungen::getLinks('deleteProfile', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
            
            $multiRequestHandle = new Request_MultiRequest();

            for ($i = 0; $i < count($list); $i++) {
                $handler = Request_CreateRequest::createDelete($list[$i]->getAddress() . '/gateprofile/gateprofile/'.$id, array(), '');
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();    
            // TODO: der Aufruf muss ausgewertet werden
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }
    
    public static function synchronizeDatabase($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        
        $profiles = self::getAllExternalProfiles($data);
        $deleteProfile = Einstellungen::getLinks('deleteProfileByName', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        $addProfile = Einstellungen::getLinks('addProfile', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        $addAuth = Einstellungen::getLinks('addAuth', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        $addRule = Einstellungen::getLinks('addRule', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        
        foreach($profiles as $profile){
            
            // zunächst wollen wir das Profil in der Datenbank löschen
            $multiRequestHandle = new Request_MultiRequest();
            for ($i = 0; $i < count($deleteProfile); $i++) {
                $handler = Request_CreateRequest::createDelete($deleteProfile[$i]->getAddress() . '/gateprofile/name/'.$profile->getName(), array(), '');
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();
            // TODO: der Aufruf muss ausgewertet werden
            
            // nun muss das Profil in der Datenbank angelegt werden
            $multiRequestHandle = new Request_MultiRequest();
            $newProfile = GateProfile::createGateProfile(null,
                                                         $profile->getName(),
                                                         '1');
            $newProfile->setStatus(200);
            for ($i = 0; $i < count($addProfile); $i++) {
                $handler = Request_CreateRequest::createPost($addProfile[$i]->getAddress() . '/gateprofile', array(), GateProfile::encodeGateProfile($newProfile));
                $multiRequestHandle->addRequest($handler);
            }

            $answers = $multiRequestHandle->run();    
            $profileId = null;
            foreach($answers as $answer){
                if ($answer['status'] == 201 && isset($answer['content'])){
                    $pro = GateProfile::decodeGateProfile($answer['content']);

                    if ($pro->getStatus() == 201){
                        $profileId = $pro->getId();
                        break;
                    } else {
                        // ein Fehler beim Erstellen des Profils
                    }
                } else {
                    // ein Fehler beim Aufruf
                }
            }

            if ($profileId === null){
                // es ist ein Fehler aufgetreten
                continue;
            }
            
            // jetzt können die Authentifizierungen und Regeln angelegt werden
            
            foreach($profile->getAuths() as $auth){
                $multiRequestHandle = new Request_MultiRequest();

                $newAuth = GateAuth::createGateAuth(null,
                                                    $auth->getType(),
                                                    $auth->getParams(),
                                                    $auth->getLogin(),
                                                    $auth->getPasswd(),
                                                    $profileId);

                for ($i = 0; $i < count($addAuth); $i++) {
                    $handler = Request_CreateRequest::createPost($addAuth[$i]->getAddress() . '/gateauth', array(), GateAuth::encodeGateAuth($newAuth));
                    $multiRequestHandle->addRequest($handler);
                }

                $answer = $multiRequestHandle->run();      
                // TODO: der Aufruf muss ausgewertet werden 
            }
            
            foreach($profile->getRules() as $rule){
                $multiRequestHandle = new Request_MultiRequest();

                $newRule = GateRule::createGateRule(null,
                                                    $rule->getType(),
                                                    $rule->getComponent(),
                                                    $rule->getContent(),
                                                    $profileId);
                for ($i = 0; $i < count($addRule); $i++) {
                    $handler = Request_CreateRequest::createPost($addRule[$i]->getAddress() . '/gaterule', array(), GateRule::encodeGateRule($newRule));
                    $multiRequestHandle->addRequest($handler);
                }

                $answer = $multiRequestHandle->run();
                // TODO: der Aufruf muss ausgewertet werden
            }
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

    public static function saveProfile($data, &$fail, &$errno, &$error) {
        Installation::log(array('text' => Installation::Get('main', 'functionBegin')));
        // das Profil speichern
        
        if (isset($data['COZ']['coz_selectedProfile'])){
            $profileId = $data['COZ']['coz_selectedProfile'];
            $newProfile = GateProfile::createGateProfile($profileId,
                                                         $data['COZ']['coz_selectedProfileName']);
                                                         
            $list = Einstellungen::getLinks('editProfile', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
        
            $multiRequestHandle = new Request_MultiRequest();
            for ($i = 0; $i < count($list); $i++) {
                $handler = Request_CreateRequest::createPut($list[$i]->getAddress() . '/gateprofile/gateprofile/'.$profileId, array(), GateProfile::encodeGateProfile($newProfile));
                $multiRequestHandle->addRequest($handler);
            }

            $answer = $multiRequestHandle->run();
            // TODO: der Aufruf muss ausgewertet werden
            
            
            if (isset($data['COZ']['coz_selectedAuth'])){
                $authId = $data['COZ']['coz_selectedAuth'];
                $newAuth = GateAuth::createGateAuth($authId,
                                                    $data['COZ']['coz_selectedAuthType'],
                                                    $data['COZ']['coz_selectedAuthParams'],
                                                    $data['COZ']['coz_selectedAuthLogin'],
                                                    $data['COZ']['coz_selectedAuthPasswd'],
                                                    $profileId);
                
                $list = Einstellungen::getLinks('editAuth', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
            
                $multiRequestHandle = new Request_MultiRequest();
                for ($i = 0; $i < count($list); $i++) {
                    $handler = Request_CreateRequest::createPut($list[$i]->getAddress() . '/gateauth/gateauth/'.$authId, array(), GateAuth::encodeGateAuth($newAuth));
                    $multiRequestHandle->addRequest($handler);
                }

                $answer = $multiRequestHandle->run();            
                // TODO: der Aufruf muss ausgewertet werden
            }
            
            if (isset($data['COZ']['coz_selectedRule'])){
                $ruleId = $data['COZ']['coz_selectedRule'];
                $newRule = GateRule::createGateRule($ruleId,
                                                    $data['COZ']['coz_selectedRuleType'],
                                                    $data['COZ']['coz_selectedRuleComponent'],
                                                    $data['COZ']['coz_selectedRuleContent'],
                                                    $profileId);
                                                    
                $list = Einstellungen::getLinks('editRule', dirname(__FILE__), '/tapiconfiguration_cconfig.json');
                
                $multiRequestHandle = new Request_MultiRequest();
                for ($i = 0; $i < count($list); $i++) {
                    $handler = Request_CreateRequest::createPut($list[$i]->getAddress() . '/gaterule/gaterule/'.$ruleId, array(), GateRule::encodeGateRule($newRule));
                    $multiRequestHandle->addRequest($handler);
                }

                $answer = $multiRequestHandle->run();
                // TODO: der Aufruf muss ausgewertet werden
            }
        }
        
        Installation::log(array('text' => Installation::Get('main', 'functionEnd')));
        return null;
    }

}

#endregion Komponentenzugang