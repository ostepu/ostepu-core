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
    public function __construct( $_fromName, $_fromURL, $_fromMethod, $_toName, $_toURL, $_toURI, $_toMethod)
    {
        ///$this->id = self::$maxID;self::$maxID++;
        $this->fromName = $_fromName;
        $this->fromURL = $_fromURL;
        $this->fromMethod = $_fromMethod;
        $this->toName = $_toName;
        $this->toURL = $_toURL;
        $this->toURI = $_toURI;
        $this->toMethod = $_toMethod;
    }
    
    public $fromName = null;
    public $fromURL = null;
    public $fromMethod = null;
    public $toName = null;
    public $toURL = null;
    public $toURI = null;
    public $toMethod = null;
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
    private static $activeTree = false;
    private static $changedTree = false;
    
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
            $toName = $path->toName;
            $fromName = $path->fromName;
            if ($path->toName=='BEGIN')
                $toName = basename($path->toURL);
            if ($path->fromName=='BEGIN')
                $fromName = basename($path->fromURL);
            $fromName = basename(explode('?',$fromName)[0]);
            $toName = basename(explode('?',$toName)[0]);
            $fromName = basename($fromName);
            $toName = basename($toName);
            if ($fromName=='')$fromName=null;
            if ($toName=='')$toName=null;
                if ($fromName===null){
                    $text.="\"".$fromName."\" [style=\"invis\"];\n";
                    $graphName=$toName;
                } else{
                    $text.="\"".$fromName."\" [shape=\"box\"];\n";
                }
            }
            
            $text.=" label = \"{$key}\";color=blue;style=dashed;rank = same;}\n";
        }
        
        foreach (self::$cachedPath as $path){
            $url = $path->toURI;
            $ll = implode("\n/",explode('/',$url));
            $toName = $path->toName;
            $fromName = $path->fromName;
            if ($path->toName!='BEGIN')
                $ll="";
            if ($path->toName=='BEGIN')
                $toName = basename($path->toURL);
            if ($path->fromName=='BEGIN')
                $fromName = basename($path->fromURL);
            $fromName = basename(explode('?',$fromName)[0]);
            $toName = basename(explode('?',$toName)[0]);
            $fromName = basename($fromName);
            $toName = basename($toName);
            if ($fromName=='')$fromName=null;
            if ($toName=='')$toName=null;
            $text.="\"".$fromName.'"->"'.$toName."\"[ label = \"".$path->toMethod."".$ll."\" ];\n";
        }
        $text.="\n}";
        
        file_put_contents(dirname(__FILE__).'/../path/'.$graphName.'.gv',$text);
        if (self::$changedTree) return;
        
        
        $treePath = '/tmp/cache';
        $componentTag = self::$cachedPath[0]->toName;
        if ($componentTag=='BEGIN')$componentTag = self::$cachedPath[1]->toName;
        
        self::generatepath($treePath.'/tree/'.$componentTag);
        $tree = array();
        foreach (self::$cachedPath as $path){
            if ($path->fromMethod!='GET') continue;
            if ($path->toMethod!='GET') continue;
            if (!isset($tree[$path->fromName])) $tree[$path->fromName]=array();
            $uTag = self::generateETag($path->toURL);
            $eTag = isset(self::$cachedData[$uTag]) ? self::$cachedData[$uTag]->content : null;
            if ($eTag!==null) $eTag = self::generateETag($eTag);
            $tree[$path->fromName][] = array('name'=>$path->toName,'url'=>$path->toURL,'method'=>$path->toMethod,'eTag'=>$eTag);
        }

        $uTag = self::generateETag(self::generateURL());
        if (self::$cachedPath[0]->fromName=='BEGIN')$uTag = self::generateETag(self::$cachedPath[0]->toURL);
        if (self::$cachedPath[0]->fromName=='')$uTag = self::generateETag(self::$cachedPath[1]->toURL);
        file_put_contents($treePath.'/tree/'.$componentTag.'/'.$uTag,json_encode($tree));
    }
    
    public static function generateURL()
    {
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }
    
    public static function getCachedDataByURL($Name, $URL, $URI, $method)
    {
        //if (strpos($_SERVER['SCRIPT_NAME'],'/UI/')!==false) {return null;}
        
        $fromName = basename(dirname($_SERVER['SCRIPT_NAME']));
        if (strpos($_SERVER['SCRIPT_NAME'],'/UI/')!==false) $fromName='BEGIN';
        if ($fromName=='') $fromName='BEGIN';
        if (self::$begin===null){
            self::$begin = $_SERVER['REQUEST_METHOD'].self::generateURL();
            self::$cachedPath[] = new PathObject(null,null,null,$fromName,$_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);
        }
        self::$cachedPath[] = new PathObject($fromName,self::generateURL(),$_SERVER['REQUEST_METHOD'],$Name, $URL, $URI,$method);
        
        if (strtoupper($method)!='GET') return null;
        $uTag = md5($URL);
        if (isset(self::$cachedData[$uTag]))
            return self::$cachedData[$uTag];

        return null;
    }
    
    public static function cacheData($Name, $URL, $content, $status, $method)
    { 
        if (strpos($URL,'/UI/')===false && strtoupper($method)=='GET'){
            $uTag = md5($URL);

            if (!isset(self::$cachedData[$uTag])){
                self::$cachedData[$uTag] = new DataObject($content,$status);
                $treePath = '/tmp/cache';
                $componentTag = $Name;
                $eTag = self::generateETag($content);
                if (!file_exists($treePath.'/data/'.$componentTag.'/'.$eTag)){
                    self::generatepath($treePath.'/data/'.$componentTag);
                    file_put_contents($treePath.'/data/'.$componentTag.'/'.$eTag,json_encode($content));
                }
            }
        }
        
        if (self::$begin!==null && self::$begin==$_SERVER['REQUEST_METHOD'].self::generateURL()){
            self::savePath();
        }
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
        header('ETag: ' . $eTag . '');
    }
    
    public static function getTree($Name, $URL, $method)
    {
        if (self::$activeTree) return;
        if (strtoupper($method)!='GET') return;
        // search tree
        $treePath = '/tmp/cache';
        $uTag = md5($URL);
        $componentTag = $Name;
        if (file_exists($treePath.'/tree/'.$componentTag.'/'.$uTag)){
            self::$activeTree=true;
            $uriTag = self::generateETag($URL);
            $list = json_decode(file_get_contents($treePath.'/tree/'.$componentTag.'/'.$uTag),true);

            $sources = array();
            $pos = array($Name);
            for ($i=0;$i<count($pos);$i++){
                foreach ($list[$pos[$i]] as $call){
                    if (!isset($list[$call['name']])){
                        $sources[] = $call;
                    } else 
                        $pos[] = $call['name'];
                }
            }

            // call sources
            $allOK = true;
            $temp = self::$cachedPath;
            foreach ($sources as $source){
                $answ = Request::get($source['url'], array(),  '', true);
                if (!isset($answ['headers']['Etag']) || $answ['headers']['Etag']!=$source['eTag']){
                    $allOK = false;
                }
            }
            self::$cachedPath=$temp;
            
            if ($allOK){
                self::$changedTree=true;
                if (isset($list['BEGIN'])){
                    $data = file_get_contents($treePath.'/data/'.$list['BEGIN'][0]['name'].'/'.$list['BEGIN'][0]['eTag']);
                    self::cacheData($list['BEGIN'][0]['name'], $list['BEGIN'][0]['url'], json_decode($data), 200, $list['BEGIN'][0]['method']);
                }
            } else {
                // remove tree
                unlink(realpath($treePath.'/tree/'.$componentTag.'/'.$uTag));
            }
        }
    }
    
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