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
        /*$text="digraph G {rankdir=TB;edge [splines=\"polyline\"];\n";
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
        
        file_put_contents(dirname(__FILE__).'/../path/'.$graphName.'.gv',$text);*/
        
        $treePath = '/tmp/cache';
        $componentTag = self::$cachedPath[0]->toName;
        $uriTag = self::generateETag(self::$cachedPath[0]->toURL);
        self::generatepath($treePath.'/trees/'.$componentTag);
        $tree = array();
        foreach (self::$cachedPath as $path){
            if ($path->fromName===null) continue;
            if (!isset($tree[$path->fromName])) $tree[$path->fromName]=array();
            $tree[$path->fromName][] = array('name'=>$path->toName,'uri'=>$path->toURL);
            //$text.="\"".$path->fromName.'"->"'.$path->toName."\"[ label = \"".$path->toMethod."".implode("\n/",explode('/',$path->toURL))."\" ];\n";
        }
        file_put_contents($treePath.'/trees/'.$componentTag.'/'.$uriTag,json_encode($tree));
    }
    
    public static function getCachedDataByURL($Base, $URI, $method)
    {
        /*$URL = $Base. $URI;
        $fromName = basename(dirname($_SERVER['SCRIPT_NAME']));
        if (self::$begin===null){
            self::$begin = $_SERVER['REQUEST_METHOD'].$_SERVER['REQUEST_URI'];
            self::$cachedPath[] = new PathObject(null,null,null,$fromName,$_SERVER['SCRIPT_NAME'],$_SERVER['REQUEST_METHOD']);
        }
        
        self::$cachedPath[] = new PathObject($fromName,$_SERVER['SCRIPT_NAME'],$_SERVER['REQUEST_METHOD'],$Base, $URI,$method);
        
        if (strpos($URI,'/UI/')!==false) {return null;}
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

        if (strpos($URL,'/UI/')!==false) {$content=null;}
        //if (strtoupper($method)!='GET') $content=null;
        $uTag = md5($URL);
        $cachedData[$uTag] = new DataObject($content,$status);
        $treePath = '/tmp/cache';
        $componentTag = basename(dirname($_SERVER['SCRIPT_NAME']));
        $eTag = self::generateETag($content);;
        self::generatepath($treePath.'/data/'.$componentTag);
        file_put_contents($treePath.'/data/'.$componentTag.'/'.$eTag,json_encode($content));*/
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
    
    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     * @see http://php.net/manual/de/function.mkdir.php#83265
     */
    public static function generatepath( $path, $mode = 0755 )
    {
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        $e = explode("/", ltrim($path, "/"));
        if(substr($path, 0, 1) == "/") {
            $e[0] = "/".$e[0];
        }
        $c = count($e);
        $cp = $e[0];
        for($i = 1; $i < $c; $i++) {
            if(!is_dir($cp) && !@mkdir($cp, $mode)) {
                return false;
            }
            $cp .= "/".$e[$i];
        }
        return @mkdir($path, $mode);
    }
}
?>