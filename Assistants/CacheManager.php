<?php
require_once ( dirname(__FILE__) . '/Slim/Route.php' );
require_once ( dirname(__FILE__) . '/Slim/Router.php' );
require_once ( dirname(__FILE__) . '/Slim/Environment.php' );
include_once ( dirname(__FILE__) . '/Structures.php' );
include_once ( dirname(__FILE__) . '/Request.php' );
include_once ( dirname(__FILE__) . '/Logger.php' );
include_once ( dirname(__FILE__) . '/CConfig.php' );
include_once ( dirname(__FILE__) . '/DBRequest.php' );
include_once ( dirname(__FILE__) . '/DBJson.php' );
require_once( dirname(__FILE__) . '/phpfastcache/phpfastcache.php');

class PathObject
{
    public function __construct( $_fromSid, $_toSid,$_toName, $_toURL, $_toMethod)
    {
        $this->fromSid = $_fromSid;
        $this->toSid = $_toSid;
        $this->toName = $_toName;
        $this->toURL = $_toURL;
        $this->toMethod = $_toMethod;
    }
    
    public $fromSid = null;
    public $toSid = null;
    public $toName = null;
    public $toURL = null;
    public $toMethod = null;
    public $eTag = null;
}

class DataObject
{
    public function __construct( $_content, $_status, $_eTag=null)
    {
        $this->content = $_content;
        $this->status = $_status;
        $this->eTag = $_eTag;
    }
    
    public $content = null;
    public $status = null;
    public $eTag = null;
}

class CacheManager
{
    private static $cachedData = array();
    public static $cachedPath = array();
    public static $tree = null;
    private static $begin = null;
    private static $activeTree = false;
    private static $changedTree = false;
    private static $enabled = false;
    private static $maxSid = -1;
    
    public static function getToPathBySid($sid)
    {
        if ($sid===null) return null;
        foreach(self::$cachedPath as $key => $elem){
            if ($elem->toSid===null) continue;
            if ($elem->toSid==$sid)
                return $elem;
        }
        return null;
    }
    
    public static function getNextSid()
    {
        self::$maxSid=self::$maxSid+1;
        return self::$maxSid;
    }
    
    public static function setCacheEnabled($status=null)
    {
        if ($status===null) return self::$enabled;
        self::$enabled=$status;
    }
    
    public static function enable()
    {
        $enabled=true;
    }
    
    public static function disable()
    {
        $enabled=false;
    }
    
    public static function reset()
    {
        self::$cachedData = array();
        self::$cachedPath = array();
        self::$tree = null;
        self::$begin = null;
        self::$activeTree = false;
        self::$changedTree = false;
        self::$enabled = true; 
        self::$maxSid = -1;
    }
    
    public static function savePath()
    {
        if (!self::$enabled) return;
        asort(self::$cachedPath);
        
       /* $text="digraph G {rankdir=TB;edge [splines=\"polyline\"];\n";
        $graphName="graph";
        
        $graphName = $_SERVER['SCRIPT_NAME'];
        $graphName = basename(explode('?',$graphName)[0]);
        
        $beginDone=false;
        foreach (self::$cachedPath as $key => $path){
            $fromName = self::getToPathBySid($path->fromSid);

            if ($fromName===null){
                $fromName = 'BEGIN';
            } else
                $fromName = $fromName->toName;
            $text.="\"".$fromName.'"->"'.$path->toName."\"[ label = \"".$path->toMethod."\" ];\n";
        }
        $text.="\n}";
        file_put_contents(dirname(__FILE__).'/../path/'.$graphName.'.gv',$text);*/
        
        if (self::$changedTree) return;
                
        $tree = array();
        foreach (self::$cachedPath as $key => $path){
            if ($path->toMethod!='GET') continue;
            $uTag = self::generateETag($path->toURL);
            $eTag = isset(self::$cachedData[$uTag]) ? self::$cachedData[$uTag]->content : null;
            if ($eTag!==null) $eTag = self::generateETag($eTag);
            $path->eTag=$eTag;
            $tree[$key] = $path;
        }

        $uTag = self::generateETag(self::$cachedPath[0]->toURL);
        self::storeData('tree_'.$uTag,json_encode($tree));
    }
    
    public static function generateURL()
    {
        if (!self::$enabled) return null;
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
    
    public static function getCachedDataByURL($sid, $URL, $method)
    {
        if (!self::$enabled) return null;
        if (strtoupper($method)!='GET') return null;
        
        $uTag = md5($URL);
        if (isset(self::$cachedData[$uTag]))
            return self::$cachedData[$uTag];

        return null;
    }
    
    public static function addPath($sid, $toSid, $Name, $URL, $method)
    {
        if (!self::$enabled) return null;
        self::$cachedPath[$sid] = new PathObject($sid, $toSid, $Name, $URL ,$method);
    }
    
    public static function cacheData($sid, $Name, $URL, $content, $status, $method)
    {
        if (!self::$enabled) return null;
        if (strpos($URL,'/UI/')===false && strtoupper($method)=='GET'){
            $uTag = md5($URL);

            if (!isset(self::$cachedData[$uTag])){
                self::$cachedData[$uTag] = new DataObject($content,$status);
                $eTag = self::generateETag($content);
                if ($sid==0)
                    self::storeData('data_'.$eTag,$content);
            }
        }

        if ($sid===0){
            self::savePath();
        }
    }
    
    public static function cacheDataSimple($sid, $Name, $URL, $content, $status, $method)
    {
        if (!self::$enabled) return null;
        if (strpos($URL,'/UI/')===false && strtoupper($method)=='GET'){
            $uTag = md5($URL);

            if (!isset(self::$cachedData[$uTag])){
                self::$cachedData[$uTag] = new DataObject($content,$status);
                $componentTag = $Name;
                $eTag = self::generateETag($content);
            }
        }
        
        if ($sid===0){
            self::savePath();
        }
    }
    
    public static function generateETag($data)
    {
        if (!self::$enabled) return null;
        if (!is_string($data))
            $data = json_encode($data);
        return md5($data);
    }
    
    public static function setETag($data)
    {
        if (!self::$enabled) return null;
        $eTag=self::generateETag($data);
        header('ETag: ' . $eTag . '');
    }
    
    public static function setCacheSid($sid)
    {
        if (!self::$enabled) return null;
        header('Cachesid: ' . $sid . '');
    }
    
    public static function getTree($sid, $URL, $method)
    {
        if (!self::$enabled) return;
        if (self::$activeTree) return;
        if (self::$tree!=null) return;
        if (strtoupper($method)!='GET') return;
        
        $uTag = md5($URL);
        self::$activeTree=true;
        self::$changedTree=true;
        $uriTag = self::generateETag($URL);
        $list = self::loadData('tree_'.$uTag);
        if ($list===null) {
            self::$changedTree=false;
            ///Logger::Log('no tree', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            return;
        } else {
            ///Logger::Log('tree found', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        }
        
        $list = json_decode($list,true);
        $result = self::loadData('data_'.$list[0]['eTag']);
        if ($result===null) {
            self::$changedTree=false;
            ///Logger::Log('no result', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            return;
        }
        
        self::$tree = json_decode(json_encode($list));
        $sources = array();
        $allOK = true;
                    
        foreach ($list as $key=>$elem){
            if ($elem['toSid']===null)
                $sources[$key] = $elem;
        }
        
        // call sources            
        if ($allOK){
            foreach ($sources as $key => $source){
                $answ = Request::get($source['toURL'], array(),  '', true);
                if (!isset($answ['headers']['Etag']) || $answ['headers']['Etag']!=$source['eTag']){
                    $allOK = false;
                    ///Logger::Log('call '.$source['toURL'].(isset($answ['headers']['Etag']) ? ' is not equal, recvHash: '.$answ['headers']['Etag'] : '').' oldHash: '.$source['eTag'] , LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                } else {
                    ///Logger::Log('call '.$source['toURL'].' is equal' , LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                }
            }
        }
        
        if ($allOK){
            self::$changedTree=true;
            if (isset($list[0])){
                self::cacheDataSimple($sid, $list[0]['toName'], $list[0]['toURL'], $result, 200, $list[0]['toMethod']);
                ///Logger::Log('cache hit', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            } else {
                ///Logger::Log('nonono', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            }
        } else {
            // remove tree
            self::removeData('tree_'.$uTag);
        }
    }
    
    private static $cache = null;
    public static function storeData($key, $value)
    {
        if (!self::$enabled) return;
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }
        ///Logger::Log('store: '.$key, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');

        return self::$cache->set($key,$value , 43200);
    }
    
    public static function removeData($key)
    {
        if (!self::$enabled) return null;
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }        
        ///Logger::Log('delete: '.$key, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        return self::$cache->delete($key);
    }
    
    public static function loadData($key)
    {
        if (!self::$enabled) return null;
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }
        ///Logger::Log('load: '.$key, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        
        return self::$cache->get($key);
    }
    
    public static function loadDataArray($keys)
    {
        if (!self::$enabled) return null;
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }
        ///Logger::Log('load: '.implode(':',$keys), LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        
        return self::$cache->getMulti($keys);
    }
    
}