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

class PathObject
{
    ///private static $maxID = 0;
    public function __construct( $_fromName, $_fromURL, $_fromMethod, $_toName, $_toURL, $_toMethod)
    {
        ///$this->id = self::$maxID;self::$maxID++;
        $this->fromName = $_fromName;
        $this->fromURL = $_fromURL;
        $this->fromMethod = $_fromMethod;
        $this->toName = $_toName;
        $this->toURL = $_toURL;
        $this->toMethod = $_toMethod;
    }
    
    public $fromName = null;
    public $fromURL = null;
    public $fromMethod = null;
    public $toName = null;
    public $toURL = null;
    public $toMethod = null;
    ///public $id = null;
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
    private static $cachedPath = array();
    private static $begin = null;
    
    public static function savePath()
    {
        $text="digraph G {rankdir=TB;edge [splines=\"polyline\"];\n";
        $graphName="graph";
        $groups=array();
        foreach (self::$cachedPath as $path){
            $group='0';
            if (strpos($path->fromURL,'/UI/')!==false)$group='1';
            if (strpos($path->fromURL,'/logic/')!==false)$group='2';
            if (strpos($path->fromURL,'/DB/')!==false)$group='3';
            
            if (!isset($groups[$group])) $groups[$group] = array();
            $groups[$group][] = $path;
        }
        
        foreach ($groups as $key=>$list){
        	$text.="subgraph step{$key}{\n";//cluster_{$key}
		
            foreach ($list as $path){
                if ($path->fromName===null){
                    $text.="\"".$path->fromName."\" [style=\"invis\"];\n";
                    $graphName=$path->toName;
                } else{
                    $text.="\"".$path->fromName."\" [shape=\"box\"];\n";
                }
            }
            
            $text.=" label = \"{$key}\";color=blue;style=dashed;rank = same;}\n";
        }
        
        foreach (self::$cachedPath as $path){
            $text.="\"".$path->fromName.'"->"'.$path->toName."\"[ label = \"".$path->toMethod."".implode("\n/",explode('/',$path->toURL))."\" ];\n";
        }
        $text.="\n}";
        
        file_put_contents(dirname(__FILE__).'/../path/'.$graphName.'.gv',$text);
    }
    
    public static function getCachedDataByURL($Base, $URI, $method)
    {
        /*$URL = $Base. $URI;
        if (self::$begin===null){
            self::$begin = $_SERVER['REQUEST_METHOD'].$_SERVER['REQUEST_URI'];
            self::$cachedPath[] = new PathObject(null,null,null,basename($_SERVER['SCRIPT_NAME']),$_SERVER['SCRIPT_NAME'],$_SERVER['REQUEST_METHOD']);
        }
        
        self::$cachedPath[] = new PathObject(basename($_SERVER['SCRIPT_NAME']),$_SERVER['SCRIPT_NAME'],$_SERVER['REQUEST_METHOD'],$Base, $URI,$method);
        if (strtoupper($method)!='GET') return null;
        $uTag = md5($URL);
        if (isset($cachedData[$uTag]))
            return $cachedData[$uTag];*/
        return null;
    }
    
    public static function cacheData($URL, $content, $status, $method)
    {
        /*if (self::$begin!==null && self::$begin==$_SERVER['REQUEST_METHOD'].$_SERVER['REQUEST_URI']){
            self::savePath();
        }
        
        if (strtoupper($method)!='GET') $content=null;
        $uTag = md5($URL);
        $cachedData[$uTag] = new DataObject($content,$status);*/
    }
    
    public static function generateETag($data)
    {
        if (!is_string($data))
            $data = json_encode($data);
        return md5($data);
    }
    
    public static function setETag($data)
    {
        $eTag=self::generateETag($data);
        header('ETag: "' . $eTag . '"');
    }
    
    /*public static function getKnownETags($componentName, $URL)
    {
        $folder = dirname(__FILE__).'/../../cache/'.md5($componentName.' '.$URL);
        if (is_dir($folder)){
            return scandir($folder);
        } else
           return array();
    }
    
    public static function getCachedData($componentName, $URL, $eTag)
    {
        $file = dirname(__FILE__).'/../../cache/'.md5($componentName.' '.$URL).'/'.$eTag;
        if (file_exists($file)){
            return file_get_contents($file);
        } else
            return null;        
    }
    
    public static function cacheData($componentName, $URL, $data, $eTag=null)
    {
        if ($eTag===null)
            $eTag = self::generateETag($data);
        $file = dirname(__FILE__).'/../../cache/'.md5($componentName.' '.$URL).'/'.$eTag;
        mkdir(dirname($file));
        file_put_contents($file,$data);
    }*/
}