<?php

/**
 * @file QEPGenerator.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.2.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */
if (file_exists(dirname(__FILE__) . '/vendor/Slim/Slim/Slim.php')) {
    include_once (dirname(__FILE__) . '/vendor/Slim/Slim/Route.php');
    include_once (dirname(__FILE__) . '/vendor/Slim/Slim/Router.php');
    include_once (dirname(__FILE__) . '/vendor/Slim/Slim/Environment.php');
}

include_once ( dirname(__FILE__) . '/Structures.php' );
include_once ( dirname(__FILE__) . '/Request.php' );
include_once ( dirname(__FILE__) . '/Logger.php' );
include_once ( dirname(__FILE__) . '/CConfig.php' );
include_once ( dirname(__FILE__) . '/DBRequest.php' );
include_once ( dirname(__FILE__) . '/DBJson.php' );
include_once ( dirname(__FILE__) . '/../install/include/Einstellungen.php' );

if (file_exists(dirname(__FILE__) . '/vendor/phpfastcache/phpfastcache.php')) {
    include_once(dirname(__FILE__) . '/vendor/phpfastcache/phpfastcache.php');
}

include_once (dirname(__FILE__) . '/QEPGenerator/cacheAccess.php');
include_once (dirname(__FILE__) . '/QEPGenerator/SID.php');
include_once (dirname(__FILE__) . '/QEPGenerator/cacheTree.php');
include_once (dirname(__FILE__) . '/QEPGenerator/cacheLogger.php');
include_once (dirname(__FILE__) . '/QEPGenerator/structures/DataObject.php');

class QEPGenerator {

    /*
     * der aktuelle Anfragebaum
     */
    public static $tree = null;
    
    /*
     * dieser Bezeichner wird in den Logeinträgen dieser Datei verwendet
     */
    private static $logName = 'QEPGenerator';
    
    /*
     * 
     */
    private static $activeTree = false;
    
    /*
     * 
     */
    private static $changedTree = false;

    /*
     * hier werden Anfrageergebnisse Zwischengespeichert
     * KEY = UTag, VALUE = der Anfrageinhalt als Array (Content, Status)
     */
    private static $cachedData = array();

    /*
     * gibt an, ob der Baum auf jeden Fall ohne die Hilfe des Caches aufgezeichnet
     * werden soll (true = ignoriere Cache, false = normale Nutzung)
     */
    private static $ignoreTreeCache = false;


    /*
     * enthält die Konfiguration des QEPGenerators (wird von loadConfig geladen)
     * Der Zugriff erfolgt über getConf($field)
     */
    private static $conf = null;

    /*
     * setzt den aktuellen Baum zurück und gibt ihn über return aus
     * 
     * @return array der Baum, die SID-Daten
     */
    private static function storeTree() {        
        if (self::$ignoreTreeCache){
            return;
        }
                
        $myTree = null;
        if (self::$tree !== null){
            $myTree = clone self::$tree;
        }
        
        $mySID = SID::storeSid();
        
        cacheLogger::Log(__function__.': '.$mySID['sid'], self::$logName);
        
        $restoreData = array('SID' => $mySID);
        $cacheTreeData = array('activeTree' => self::$activeTree, 'changedTree' => self::$changedTree, 'tree' => $myTree);
        self::reset();
        $restoreData['CacheTree'] = $cacheTreeData;
        return $restoreData;
    }

    /*
     * stellt den Baum anhand von $data (zuvor mit storeTree ermittelt) wieder her
     * 
     * @param array $data die Daten des Baums
     */
    private static function restoreTree($data) {
        cacheLogger::Log(__function__.': '.$data['SID']['sid'], self::$logName);
        SID::restoreSid($data['SID']);
        self::$activeTree = $data['CacheTree']['activeTree'];
        self::$changedTree = $data['CacheTree']['changedTree'];
        self::$tree = $data['CacheTree']['tree'];
    }

    /*
     * lädt die die Konfiguration des QEPGenerators aus der config.json,
     * falls sie noch nicht geladen wurde
     */
    private static function loadConfig() {
        cacheLogger::enableLog();
        
        if (self::$conf !== null) {
            return;
        }
        self::$conf = self::getDefaultConf();

        $confFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'QEPGenerator' . DIRECTORY_SEPARATOR . 'config.json';
        if (file_exists($confFile)) {
            self::$conf = array_merge(self::$conf, json_decode(file_get_contents($confFile), true));
        }
    }

    /*
     * liefert die Sandardkonfiguration
     * 
     * @return String[] die Konfiguration
     */
    public static function getDefaultConf() {
        return array('enabled' => false, 'makeTree' => false, 'treePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'path');
    }

    /*
     * liefert einen Wert aus der Konfiguration
     * 
     * @param $field das Feld, dessen Wert verlangt wird
     * @return der Wert oder null (wenn er nicht existiert)
     */
    public static function getConf($field) {
        if (isset(self::$conf[$field])) {
            return self::$conf[$field];
        }
        return null;
    }

    /*
     * setzt einen Wert in der Konfiguration
     * 
     * @param $field das Feld
     * @param $value der neue Wert
     */
    public static function setConf($field, $value) {
        self::$conf[$field] = $value;
    }

    /**
     * Liefert den Index des ersten Elements einer Liste
     *
     * @param mixed[]
     * @return int Der Index des ersten Elements
     */
    public static function getFirstIndex($list) {
        reset($list);
        return key($list);
    }

    /**
     * Liefert das erste Elements einer Liste
     *
     * @param mixed[]
     * @return mixed Das erste Element, null wenn die Liste leer ist
     */
    public static function getFirstElement($list) {
        reset($list);
        return current($list);
    }

    /**
     * Aktiviert das Erstellen des Debug-Baums
     */
    public static function enableMakeTree() {
        self::loadConfig();
        self::setConf('makeTree', true);
    }

    /**
     * Deaktiviert das Erstellen des Debug-Baums
     */
    public static function disableMakeTree() {
        self::loadConfig();
        self::setConf('makeTree', false);
    }

    /**
     * Liefert die nächste freie SID
     *
     * @return int Die neue SID
     */
    public static function getNextSid() {
        return SID::getNextSid();
    }

    /**
     * Aktiviert den QEPGenerator
     */
    public static function enable() {
        self::loadConfig();
        self::setConf('enabled', true);
    }

    /**
     * Deaktiviert den QEPGenerator
     */
    public static function disable() {
        self::loadConfig();
        self::setConf('enabled', false);
    }

    /**
     * Setzt alle Daten des Managers auf den Standartwert zurück.
     */
    public static function reset() {
        self::$tree = null;
        self::$activeTree = false;
        self::$changedTree = false;
        //SID::reset();
    }

    private static function init() {
        self::$tree = new cacheTree();
        // initialisiere Wurzel
        $graphName = $_SERVER['SCRIPT_NAME'];
        $graphName = basename(explode('?', $graphName)[0]);
        $newNode = new node(array('id' => SID::getRoot(),
            'name' => $graphName,
            'beginTime' => microtime(true)));
        cacheLogger::Log(__function__.': currentRoot='.SID::getRoot(), self::$logName);
        self::$tree->addNode($newNode);
        self::$activeTree = true;
    }

    /**
     * Liefert die aufgerufene URL des PHP-Skriptes.
     *
     * @return string Die URL
     */
    public static function generateURL() {
        $requestScheme = 'unknown';
        if (isset($_SERVER['REQUEST_SCHEME'])){
            $requestScheme=$_SERVER['REQUEST_SCHEME'];
        }
        
        $serverName = 'unknown';
        if (isset($_SERVER['SERVER_NAME'])){
            $serverName=$_SERVER['SERVER_NAME'];
        }
        
        $requestUri = '/unknown';
        if (isset($_SERVER['REQUEST_URI'])){
            $requestUri=$_SERVER['REQUEST_URI'];
        }
        
        return $requestScheme . '://' . $serverName . $requestUri;
    }

    /**
     * Prüft ob zu dem Aufruf bereits Daten vorgehalten werden
     *
     * @param string $URL
     * @param string $method
     * @return string Die zum Aufruf gehörenden Daten oder null, wenn keine vorhanden sind
     */
    public static function getCachedDataByURL($URL, $method) {
        self::loadConfig();

        if (!self::getConf('enabled')) {
            return null;
        }

        $uTag = self::generateUTag($URL, $method);
        
        cacheLogger::Log(__function__.': URL='.$URL.' method='.$method.' uTag='.$uTag, self::$logName);

        // wenn wir den Datensatz bereits im Arbeitsseicher haben, dann nehmen
        // wir gleich diesen Datensatz
        if (isset(self::$cachedData['data_'.$uTag])) {
            cacheLogger::Log(__function__.': Datensatz data_'.$uTag.' gefunden', self::$logName);
            return self::$cachedData['data_'.$uTag];
        }

        // ansonsten fragen wir den Cacheserver, ob er den Datensatz besitzt
        $res = cacheAccess::loadData('data_'.$uTag);
        if ($res !== null) {
            // es wurde eine Datensatz gefunden
            return json_decode($res);
        } else {
            // der Cacheserver besitzt den Datensatz nicht
        }

        cacheLogger::Log(__function__.': Datensatz data_'.$uTag.' nicht gefunden', self::$logName);
        return null;
    }

    public static function createNode($sid, $name, $method, $URI, $input) {
        self::loadConfig();

        if (self::getConf('enabled') !== true && self::getConf('makeTree') !== true) {
            return;
        }
        if (self::$tree === null) {
            return;
        }

        if (self::$tree->getElementById($sid) === null) {

            $newNode = new node(array('id' => $sid,
                'name' => $name,
                'method' => $method,
                'URI' => $URI,
                'input' => $input,
                'beginTime' => microtime(true)));
            self::$tree->addNode($newNode);
        } else {
            // der Knoten existiert bereits
        }
    }

    public static function releaseNode($targetSid, $targetContent,
            $targetStatus, $path, $mimeType) {
        self::loadConfig();

        if (self::getConf('enabled') !== true && self::getConf('makeTree') !== true) {
            return;
        }
        if (self::$tree === null) {
            return;
        }

        $elem = self::$tree->getElementById($targetSid);
        if ($elem !== null) {
            $elem->endTime = microtime(true);
            $elem->status = $targetStatus;
            $elem->result = $targetContent;
            $elem->resultHash = $elem->generateETag();
            $elem->mimeType = $mimeType;
            $elem->path = $path;

            $mySID = SID::getSid();
            if ($mySID !== null && $mySID !== $targetSid) {
                ////Logger::Log('addEdge: '.$mySID.'->'.$targetSid, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', false, LogLevel::DEBUG);
                self::$tree->addEdge($mySID, $targetSid);
            }
        }

        if (SID::isRoot()) {
            // Die Wurzel der Anfrage wurde bearbeitet
            self::cacheData($targetSid, $targetContent, $targetStatus);
                
            // wenn sich der Baum verändert hat, dann speichern wir ihn
            if (self::$changedTree){
                $elem = self::$tree->getElementById(self::$tree->findRoot());
                if ($elem !== null) {
                    $elem->endTime = microtime(true);
                }
                self::$tree->computeExecutionTime();
                foreach (self::$tree->getElements() as $elem) {
                    self::saveNode($elem);
                }
                
                self::saveTree(self::$tree);
            }
            
            self::reset();
        }
    }

    public static function saveTree($tree) {
        self::loadConfig();

        if (self::getConf('makeTree') === true) {
            $root = self::$tree->getElementById(self::$tree->findRoot());
            if (!is_dir(self::getConf('treePath') . DIRECTORY_SEPARATOR . $root->name)) {
                mkdir(self::getConf('treePath') . DIRECTORY_SEPARATOR . $root->name, 0755);
            }

            file_put_contents(self::getConf('treePath') . DIRECTORY_SEPARATOR . $root->name . DIRECTORY_SEPARATOR . $root->name . '_' . $root->id . '_tree.json', json_encode($tree));
        }

        if (self::getConf('enabled') === true) {
            cacheLogger::Log(__function__.': der Baum wird gespeichert', self::$logName);
            $root = self::$tree->getElementById(self::$tree->findRoot());
            if ($root !== null) {
            cacheLogger::Log(__function__.' Baum >>>>>>>>>>>>>>', self::$logName);
            cacheLogger::Log(json_encode(self::$tree), self::$logName);
            cacheLogger::Log('<<<<<<<<<<<<<<<<<<<<<<<<<', self::$logName);
                    
                foreach ($root->childs as $childID) {
                    $subTree = $tree->extractSubtree($childID);

                    cacheLogger::Log(__function__." extract $childID >>>>>>>>>>>>>>", self::$logName);
                    cacheLogger::Log(json_encode($subTree), self::$logName);
                    cacheLogger::Log('<<<<<<<<<<<<<<<<<<<<<<<<<', self::$logName);
                    
                    $subTree->cleanTree();
                    $subRootId = $subTree->findRoot();
                        

                    if ($subRootId === null) {
                        // es ist ein Problem aufgetreten, die Wurzel konnte
                        // nicht ermittelt werden
                        cacheLogger::LogError(__FILE__.':'.__function__.':'.__LINE__.' die Wurzel konnte nicht ermittelt werden', self::$logName);
                        continue;
                    }

                    $subRoot = $subTree->getElementById($subRootId);
                    $uTag = self::generateUTag($subRoot->URI, $subRoot->method);
                    cacheAccess::storeData('tree_' . $uTag, json_encode($subTree));
                }
            }
        }
    }

    public static function saveNode($elem, $path = null) {
        self::loadConfig();

        if (self::getConf('makeTree') === true) {
            // speichere Knotendaten
            $Name = $elem->name;
            $dir = self::$tree->getElementById(self::$tree->findRoot())->name;
            $dir = substr($dir, 0, strlen($dir));

            // es gab einen Fehler
            if (trim($dir) == '')
                return;

            if (!is_dir(self::getConf('treePath'))) {
                mkdir(self::getConf('treePath'), 0755);
            }

            if (!is_dir(self::getConf('treePath') . DIRECTORY_SEPARATOR . $dir)) {
                mkdir(self::getConf('treePath') . DIRECTORY_SEPARATOR . $dir, 0755);
            }

            if (trim($dir) != '') {
                file_put_contents(self::getConf('treePath') . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $Name . '_' . $elem->id . '.json', node::encodeNode($elem));
            }

            $elem->result = null;
        }
    }

    /*
     * speichert den Datensatz im Cache
     * 
     * @param $sid die ID des Knotens
     * @param $content der Inhalt
     * $param $status der HTTP-Status
     */
    public static function cacheData($sid, $content, $status) {
        if (!is_string($content)){
            throw new Exception('content muss ein String sein!!!');
        }
        
        self::loadConfig();

        if (!self::getConf('enabled')) {
            return false;
        }
        if ($sid === null) {
            return false;
        }
        if (self::$tree === null) {
            return false;
        }

        cacheLogger::Log(__function__.': sid='.$sid, self::$logName);

        $elem = self::$tree->getElementById($sid);
        if ($elem !== null) {
            $uTag = $elem->generateUTag();
        
            // speichert das Element im Arbeitsspeicher
            if (isset(self::$cachedData['data_'.$uTag])){
                self::$cachedData['data_'.$uTag] = new DataObject($content, $status);
            }
        
            $res = cacheAccess::storeData('data_' . $uTag, json_encode(new DataObject($content, $status)));
            if ($res){
                $elem->storedResult=true;
            }
            return $res;
            
        }
        return false;
    }

    /**
     * Ermittelt einen Hash zu $data
     *
     * @param mixed Die Eingabedaten
     * @return string Der MD5 Hash (32 Zeichen)
     */
    public static function generateETag(&$data) {
        if (!is_string($data)) {
            return md5(json_encode($data));
        }
        return md5($data);
    }

    /*
     * generiert einen TAG anhand der URL und der Methode
     * 
     * @return string Der MD5 Hash (32 Zeichen)
     */

    public static function generateUTag($URL, $method) {
        return md5($method . '_' . $URL);
    }

    /**
     * Ermittelt den Hash von $data und weist ihn dem ETag-Header zu
     *
     * @param mixed Eingabedaten
     */
    public static function setETag(&$data) {
        $eTag = self::generateETag($data);
        header('ETag: ' . $eTag . '');
    }

    /**
     * Setzt den Cachesid-Header auf $sid
     *
     * @param int Eine SID
     */
    public static function setCacheSid($sid) {
        SID::setSid($sid);
    }

    /*
     * Entfernt den Cachesid-Header
     */
    public static function unsetCacheSid() {
        SID::unsetSid();
    }

    /*
     * wenn Knoten abgearbeitet wurden, dann muss geprüft werden,
     * ob Knoten übersprungen werden können
     */
    public static function computeProgress() {
        cacheLogger::Log(__function__, self::$logName);
       
        self::$tree->resetAllLabel();
        $leafs = self::$tree->getLeafs();
        
        $nextElements = $leafs;
        
        // label: 0 = unverändert, 1 = verändert, 2 = muss berechnet werden
        
        for ($i=0;$i<count($nextElements);$i++){
            $elemId = $nextElements[$i];
            $elem = self::$tree->getElementById($elemId);
            $elemState = self::$tree->getChangedState($elemId);
            
            if ($elemState !== null){
                // der Knoten wurde bereits bearbeitet
                continue;
            }
            
            $changedChildFound = false;
            
            if ($elem->hasChilds()){
                $childs = $elem->getChilds();
                
                foreach($childs as $childId){
                    $child = self::$tree->getElementById($childId);
                    $childState = self::$tree->getChangedState($childId);
                            
                    if ($childState !== null && $childState === 1){
                        // wenn sich eines meiner Kinder verändert hat, dann muss
                        // ich neu berechnet werden, dazu werden wir nun versuchen
                        // mein Kinder aus dem Cache/Arbeitsspeicher zu laden
                        
                        foreach($childs as $childId){
                            $child = self::$tree->getElementById($childId);
                            
                            if ($childState !== null && $childState == 1){
                                // wenn dann das Kind erreicht wurde, welches sich
                                // verändert hat, dann müssen die nachfolgenden Kinder
                                // eventuell eh neu berechnet werden
                                $changedChildFound=true;
                            }
                            
                            if ($childState !== null && $childState == 0){
                                // dieses Kind hat sich nicht verändert und
                                // kann aus dem Cache geladen werden
                                $tag = $child->generateUTag();
                                $availableChild = false;
                                
                                // prüfe, ob der Inhalt vielleicht schon im
                                // Arbeitsspeicher liegt
                                if (!$changedChildFound && !$availableChild && isset(self::$cachedData[$tag])){
                                    $availableChild=true;
                                }
                                
                                // ansonsten muss er aus dem cache geladen werden
                                if (!$changedChildFound && !$availableChild && $child->storedResult){
                                    $data = cacheAccess::loadData('data_' . $tag);
                                    if ($data !== null){
                                        $availableChild=true;
                                        self::$cachedData[$tag] = $data;
                                    }
                                }
                                
                                $elementsInChildTree = self::$tree->getElementsInSubtree($childId);
                                if ($availableChild){
                                    // wenn das Kind nun verfügbar ist, dann können
                                    // wir dieses Kind und dessen Kinder als "positiv" bearbeitet markieren
                                    foreach($elementsInChildTree as $key){
                                        if (self::$tree->getChangedState($key) === null){
                                            self::$tree->setChanged($key, 0);
                                        }
                                    }
                                } else {
                                    // wenn wir das Kind soweit nicht laden konnten, dann muss es
                                    // trotzdem gesperrt werden, weil der Vater sowieso berechnet werden muss
                                    foreach($elementsInChildTree as $key){
                                        if (self::$tree->getChangedState($key) === null){
                                            self::$tree->setChanged($key, 1);
                                        }                                     
                                    }
                                }
                           }                        
                        }
                        
                        break;
                    }
                }
                
                if (!$changedChildFound){
                    // alle Kinder sind unverändert
                    self::$tree->setChanged($elemId, 0);
                }
            } else {
                // ein Blatt
                // mit dem Blatt müssen wir nichts weiter machen
            }
            
            if ($elem->hasParent()){
                $nextElements[] = $elem->parent;
            } else {
                // $elemId ist die Wurzel und kann nun aus dem cache geladen werden
                $tag = $elem->generateUTag();
                $availableData = false;

                // prüfe, ob der Inhalt vielleicht schon im
                // Arbeitsspeicher liegt
                if (!$availableData && isset(self::$cachedData[$tag])){
                    $availableData=true;
                }

                // ansonsten muss er aus dem cache geladen werden
                if (!$availableData && $elem->storedResult){
                    $data = cacheAccess::loadData('data_' . $tag);
                    if ($data !== null){
                        $availableData=true;
                        self::$cachedData[$tag] = $data;
                    }
                }
            }
        }
        
        
    }
    
    /**
     * lädt den zur Anfragen gehörenden Baum ins System
     *
     * @param $URL der aufgerufene Befehl
     * @param $method die Aufrufmethode (GET, DELETE, POST, ...)
     */
    public static function getTree($URL, $method) {
        self::loadConfig();

        if (self::getConf('enabled') !== true) {
            // das Cachesystem ist deaktiviert
            return;
        }

        if (!in_array('phpFastCache', get_declared_classes())) {
            return;
        }

        if (self::$activeTree) {
            // es wird bereits ein Baum bearbeitet
            return;
        }

        if (self::$tree !== null) {
            // wir haben schon einen Baum geladen
            return;
        }

        if (strtoupper($method) != 'GET' && self::getConf('makeTree') !== true) {
            // die Anfrage ist keine GET und die Erstellung des Anfragegraphen
            // ist nicht erwünscht
            return;
        }

        $uTag = self::generateUTag($URL, $method);
        self::$activeTree = true;
        self::$changedTree = false;

        // wenn ignoreTreeCache=true, dann soll der Baum wirklich aufgezeichnet werden
        // und nicht über den Cache bearbeitet werden
        if (self::$ignoreTreeCache) {
            self::$tree = null;
        } else {
            self::$tree = cacheAccess::loadData('tree_' . $uTag);
        }

        if (self::$tree === null) {
            // es wurde keine gespeicherte Baumdefinition gefunden, sodass wir
            // die aktuelle Anfrage aufzeichnen müssen
            cacheLogger::Log(__function__.': neuer Baum', self::$logName);

            self::reset();
            self::init();
            self::$changedTree = true;

            return;
        } else {
            // es wurde eine gespeicherte Baumdefinition gefunden
            cacheLogger::Log(__function__.': Baum wurde geladen', self::$logName);
            self::$changedTree = false;
        }

        self::$tree = cacheTree::decodeCacheTree(self::$tree);

        // jetzt können wir diesen Baum nutzen, um unsere Anfragen abzuarbeiten

        self::$tree->computeDependencies();
        $nextElements = self::$tree->extractMinComputable();

        while (count($nextElements) > 0) {
            foreach ($nextElements as $elemId) {
                $elem = self::$tree->elements[$elemId];

                // der Aufruf der nachfolgenden Knoten soll unseren bisher berechneten
                // Baum nicht beeinflussen
                $currentCacheTreeConfiguration = self::storeTree();
                self::$ignoreTreeCache = true;

                $answ = Request::custom($elem->method, $elem->URI, array(), '', true);

                // der Baum der Unteranfrage
                $subTree = self::storeTree();

                // hier wird die Konfiguration unseres Baums wiederhergestellt
                self::restoreTree($currentCacheTreeConfiguration);
                self::$ignoreTreeCache = false;

                if (!isset($answ['headers']['Etag']) || $answ['headers']['Etag'] != $elem->resultHash || $answ['status'] != $elem->status) {
                    // der Zustand hat sich verändert oder die generierung des ETag funktioniert nicht
                    self::$tree->setChanged($elemId, 1);
                } else {
                    // der Zustand hat sich nicht verändert
                    self::$tree->setChanged($elemId, 0);
                }
            }

            self::computeProgress();

            self::$tree->computeDependencies();
            $nextElements = self::$tree->extractMinComputable();
        }
        ///echo "OK";
        ///exit(0);
    }

}
