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
include_once (dirname(__FILE__) . '/QEPGenerator/structures/DataObject.php');

class QEPGenerator
{
    public static $tree = null;
    private static $activeTree = false;
    private static $changedTree = false;
    private static $rootNode = null;
    private static $cachedData = array();
    
    /*
     * enthält die Konfiguration des QEPGenerators (wird von loadConfig geladen)
     * Der Zugriff erfolgt über getConf($field)
     */
    private static $conf = null;
    
    /*
     * lädt die die Konfiguration des QEPGenerators aus der config.json,
     * falls sie noch nicht geladen wurde
     */
    private static function loadConfig()
    {
        if (self::$conf !== null){
            return;
        }
        self::$conf = self::getDefaultConf();
        
        $confFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'QEPGenerator' . DIRECTORY_SEPARATOR . 'config.json';
        if (file_exists($confFile)){
            self::$conf = array_merge(self::$conf, json_decode(file_get_contents($confFile),true));
        }
    }
    
    /*
     * liefert die Sandardkonfiguration
     * 
     * @return String[] die Konfiguration
     */
    public static function getDefaultConf()
    {
        return array('enabled'=>false,'makeTree'=>false, 'treePath'=>dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'path');
    }
    
    /*
     * liefert einen Wert aus der Konfiguration
     * 
     * @param $field das Feld, dessen Wert verlangt wird
     * @return der Wert oder null (wenn er nicht existiert)
     */
    public static function getConf($field)
    {
        if (isset(self::$conf[$field])){
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
    public static function setConf($field, $value)
    {
        self::$conf[$field] = $value;
    }
    
    /**
     * Liefert den Index des ersten Elements einer Liste
     *
     * @param mixed[]
     * @return int Der Index des ersten Elements
     */
    public static function getFirstIndex($list)
    {
        reset($list);
        return key($list);
    }

    /**
     * Liefert das erste Elements einer Liste
     *
     * @param mixed[]
     * @return mixed Das erste Element, null wenn die Liste leer ist
     */
    public static function getFirstElement($list)
    {
        reset($list);
        return current($list);
    }

    /**
     * Aktiviert das Erstellen des Debug-Baums
     */
    public static function enableMakeTree()
    {
        self::loadConfig();
        self::setConf('makeTree',true);
    }

    /**
     * Deaktiviert das Erstellen des Debug-Baums
     */
    public static function disableMakeTree()
    {
        self::loadConfig();
        self::setConf('makeTree',false);
    }

    /**
     * Liefert die nächste freie SID
     *
     * @return int Die neue SID
     */
    public static function getNextSid()
    {
        return SID::getNextSid();
    }


    /**
     * Aktiviert den QEPGenerator
     */
    public static function enable()
    {
        self::loadConfig();
        self::setConf('enabled',true);
    }

    /**
     * Deaktiviert den QEPGenerator
     */
    public static function disable()
    {
        self::loadConfig();
        self::setConf('enabled',false);
    }

    /**
     * Setzt alle Daten des Managers auf den Standartwert zurück.
     */
    public static function reset()
    {
        self::$tree = null;
        self::$activeTree = false;
        self::$changedTree = false;
        //SID::reset();
    }

    public static function init()
    {
        self::$tree = new cacheTree();
        // initialisiere Wurzel
        $graphName = $_SERVER['SCRIPT_NAME'];
        $graphName = basename(explode('?',$graphName)[0]);
        $newNode = new node(array('id'=>SID::getRoot(),
                                  'name'=>$graphName,
                                  'beginTime'=>microtime(true)));
        ////Logger::Log('currentRoot: '.SID::getRoot(), LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', true, LogLevel::DEBUG);
        self::$tree->addNode($newNode);
        self::$activeTree = true;
    }

    /**
     * Liefert die aufgerufene URL des PHP-Skriptes.
     *
     * @return string Die URL
     */
    public static function generateURL()
    {
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }

    /**
     * Prüft ob zu dem Aufruf bereits Daten vorgehalten werden
     *
     * @param string $URL
     * @param string $method
     * @return string Die zum Aufruf gehörenden Daten oder null, wenn keine vorhanden sind
     */
    public static function getCachedDataByURL($URL, $method)
    {
        self::loadConfig();
        
        if (!self::getConf('enabled')) {
            return null;
        }

        $uTag = self::generateUTag($URL, $method);
        
        // wenn wir den Datensatz bereits im Arbeitsseicher haben, dann nehmen
        // wir gleich diesen Datensatz
        if (isset(self::$cachedData[$uTag])) {
            return self::$cachedData[$uTag];
        }
        
        // ansonsten fragen wir den Cacheserver, ob er den Datensatz besitzt
        $res = cacheAccess::loadData($uTag);
        if ($res !== null){
            // es wurde eine Datensatz gefunden
            return json_decode($res);
        } else {
            // der Cacheserver besitzt den Datensatz nicht
        }

        return null;
    }

    public static function createNode($sid, $name, $method, $URI, $input)
    {        
        self::loadConfig();

        if (self::getConf('enabled') !== true && self::getConf('makeTree') !== true) {
            return;
        }
        if (self::$tree === null) {
            return;
        }

        if (self::$tree->getElementById($sid) === null){

            $newNode = new node(array('id'=>$sid,
                                      'name'=>$name,
                                      'method'=>$method,
                                      'URI'=>$URI,
                                      'input'=>$input,
                                      'beginTime'=>microtime(true)));
            self::$tree->addNode($newNode);
        }
    }

    public static function releaseNode($targetSid, $targetContent, $targetStatus, $path, $mimeType)
    {
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

        if (SID::isRoot()){
            // Die Wurzel der Anfrage wurde bearbeitet
            $elem = self::$tree->getElementById(self::$tree->findRoot());
            if ($elem !== null) {
                $elem->endTime = microtime(true);
            }
            self::$tree->computeExecutionTime();
            foreach(self::$tree->getElements() as $elem){
                self::saveNode($elem);
            }
            self::saveTree(self::$tree);
            self::reset();
        }
    }

    public static function saveTree($tree)
    {
        self::loadConfig();
        
        if (self::getConf('makeTree') === true) {
            $root = self::$tree->getElementById(self::$tree->findRoot());
            if (!is_dir(self::getConf('treePath'). DIRECTORY_SEPARATOR .$root->name)) {
                mkdir(self::getConf('treePath'). DIRECTORY_SEPARATOR .$root->name, 0755); 
            }
            
            file_put_contents(self::getConf('treePath'). DIRECTORY_SEPARATOR .$root->name. DIRECTORY_SEPARATOR .$root->name.'_'.$root->id.'_tree.json',json_encode($tree));
        }

        if (self::getConf('enabled') === true){
            $root = self::$tree->getElementById(self::$tree->findRoot());
            if ($root !== null){
                foreach ($root->childs as $childID){
                    $subTree = $tree->extractSubtree($childID);
                    $subTree->cleanTree();
                    $subRootId = $subTree->findRoot();
                    
                    if ($subRootId === null){
                        // es ist ein Problem aufgetreten, die Wurzel konnte
                        // nicht ermittelt werden
                        continue;
                    }
                    
                    $subRoot = $subTree->getElementById($subRootId);
                    $uTag = self::generateUTag($subRoot->URI, $subRoot->method);
                    cacheAccess::storeData('tree_'.$uTag, json_encode($subTree));
                }
            }
        }
    }

    public static function saveNode($elem, $path = null)
    {
        self::loadConfig();
        
        if (self::getConf('makeTree') === true) {
            // speichere Knotendaten
            $Name = $elem->name;
            $dir = self::$tree->getElementById(self::$tree->findRoot())->name;
            $dir = substr($dir,0,strlen($dir));

            // es gab einen Fehler
            if (trim($dir)=='') return;

            if (!is_dir(self::getConf('treePath'))) {
                mkdir(self::getConf('treePath'), 0755);
            }

            if (!is_dir(self::getConf('treePath'). DIRECTORY_SEPARATOR .$dir)) {
                mkdir(self::getConf('treePath'). DIRECTORY_SEPARATOR .$dir, 0755);
            }

            if (trim($dir)!='') {
                file_put_contents(self::getConf('treePath') . DIRECTORY_SEPARATOR .$dir. DIRECTORY_SEPARATOR .$Name.'_'.$elem->id.'.json',node::encodeNode($elem));
            }
            
            $elem->result = null;
        }
    }

    /**
     * Setzt das aktuelle Skript als Wurzelknoten
     */
    public static function setRoot()
    {
        /*$graphName = $_SERVER['SCRIPT_NAME'];
        $graphName = basename(explode('?',$graphName)[0]);
        self::$rootNode = new PathObject(null, null, $graphName, null ,null, null);*/
    }

    public static function cacheData($sid, $content, $status)
    {
        self::loadConfig();
        
        if (!self::getConf('enabled')) return;
        if ($sid === null) return;
        if (self::$tree === null) return;
        
        $elem = self::$tree->getElementById($sid);
        if ($elem !== null){
            $eTag = self::generateETag($content);
            cacheAccess::storeData('data_'.$eTag,json_encode(new DataObject($content,$status)));
        }
    }

    public static function cacheDataSimple($sid, $Name, $URL, $content, $status, $method)
    {
        /*if (self::getConf('enabled') && strpos($URL,'/UI/')===false && strtoupper($method)=='GET') { // ??????
            $uTag = md5($URL);

            if (!isset(self::$cachedData[$uTag])) {
                self::$cachedData[$uTag] = new DataObject($content,$status);
                $componentTag = $Name;
                $eTag = self::generateETag($content);
            }
        }

        if ((self::getConf('enabled') || self::$makeTree) && $sid===SID::$currentBaseSID) {
            self::finishRequest($sid, null, 'BEGIN', null, $content, $status, null, null);
            self::savePath($URL,$method);
        }*/
    }

    /**
     * Ermittelt einen Hash zu $data
     *
     * @param mixed Die Eingabedaten
     * @return string Der MD5 Hash (32 Zeichen)
     */
    public static function generateETag(&$data)
    {
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
    public static function generateUTag($URL, $method){
        return md5($method.'_'.$URL);
    }

    /**
     * Ermittelt den Hash von $data und weist ihn dem ETag-Header zu
     *
     * @param mixed Eingabedaten
     */
    public static function setETag(&$data)
    {
        $eTag=self::generateETag($data);
        header('ETag: ' . $eTag . '');
    }

    /**
     * Setzt den Cachesid-Header auf $sid
     *
     * @param int Eine SID
     */
    public static function setCacheSid($sid)
    {
        header('Cachesid: ' . $sid . '');
    }

    /**
     * ???
     *
     * @param ???
     * @return ???
     */
    public static function getTree($URL, $method)
    {
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
        
        if (strtoupper($method)!='GET' && self::getConf('makeTree') !== true) {
            // die Anfrage ist keine GET und die Erstellung des Anfragegraphen
            // ist nicht erwünscht
            return;
        }

        $uTag = self::generateUTag($URL, $method);
        self::$activeTree=true;
        self::$changedTree=true;

        self::$tree = cacheAccess::loadData('tree_'.$uTag);

        if (self::$tree===null) {
            // es wurde keine gespeicherte Baumdefinition gefunden, sodass wir
            // die aktuelle Anfrage aufzeichnen müssen

            self::$changedTree=false;
            self::reset();
            self::init();

            return;
        } else {
            // es wurde eine gespeicherte Baumdefinition gefunden
        }

        self::$tree = cacheTree::decodeCacheTree(self::$tree);

        // jetzt können wir diesen Baum nutzen, um unsere Anfragen abzuarbeiten
        
        self::$tree->computeDependencies();
        $nextElements = self::$tree->extractMinComputable();
            
        while(count($nextElements)>0){
            foreach($nextElements as $elemId){
                $elem = self::$tree->elements[$elemId];
                
                /// !!! an dieser Stelle wird der tree gekillt
                $answ = Request::custom($elem->method, $elem->URI, array(),  '', true);

                if (!isset($answ['headers']['Etag']) || $answ['headers']['Etag']!=$elem->resultHash || $answ['status'] != $elem->status) {
                    // der Zustand hat sich verändert oder die generierung des ETag funktioniert nicht
                    self::$tree->setChanged($elemId, 1);
                } else {
                    // der Zustand hat sich nicht verändert
                    self::$tree->setChanged($elemId, 0);
                }
            }
             
            self::$tree->computeProgress();
            
            self::$tree->computeDependencies();
            $nextElements = self::$tree->extractMinComputable();
        }
        echo "OK";
       exit(0);
        
        /*$result = cacheAccess::loadData('data_'.$list[self::getFirstIndex($list)]['eTag']);
        if ($result===null) {
            self::$changedTree=false;
            ///Logger::Log('no result', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            return;
        }

        $sources = array();
        $allOK = true;

        foreach ($list as $key=>$elem) {
            if ($elem['toSid']===null) {
                $sources[$key] = $elem;
            }
        }

        // call sources
        if ($allOK) {
            foreach ($sources as $key => $source) {
                $answ = Request::get($source['toURL'], array(),  '', true);
                if (!isset($answ['headers']['Etag']) || $answ['headers']['Etag']!=$source['eTag']) {
                    $allOK = false;
                    ///Logger::Log('call '.$source['toURL'].(isset($answ['headers']['Etag']) ? ' is not equal, recvHash: '.$answ['headers']['Etag'] : '').' oldHash: '.$source['eTag'] , LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                } else {
                    ///Logger::Log('call '.$source['toURL'].' is equal' , LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                }
            }
        }

        if ($allOK) {
            self::$changedTree=true;
            if (isset($list[self::getFirstIndex($list)])) {
                cacheAccess::cacheDataSimple($sid, $list[self::getFirstIndex($list)]['toName'], $list[self::getFirstIndex($list)]['toURL'], $result, 200, $list[self::getFirstIndex($list)]['toMethod']);
                ///Logger::Log('cache hit', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            } else {
                ///Logger::Log('nonono', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            }
        } else {
            // remove tree
            cacheAccess::removeData('tree_'.$uTag);
        }*/
    }
}