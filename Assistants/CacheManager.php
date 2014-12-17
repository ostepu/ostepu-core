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
        $_fromName = basename(explode('?',$_fromName)[0]);
        $_toName = basename(explode('?',$_toName)[0]);
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
    public static $cachedPath = array();
    public static $tree = null;
    private static $begin = null;
    private static $activeTree = false;
    private static $changedTree = false;
    
    public static function savePath()
    {   return;return;return;
        $text="digraph G {rankdir=TB;edge [splines=\"polyline\"];\n";
        $graphName="graph";
        $groups=array();
        
        $graphName = $_SERVER['SCRIPT_NAME'];
        $graphName = basename(explode('?',$graphName)[0]);
        
        foreach (self::$cachedPath as $path){
            $group='0';
            if (strpos($path->fromURL,'/UI/')!==false)$group='1';
            if (strpos($path->fromURL,'/logic/')!==false)$group='2';
            if (strpos($path->fromURL,'/DB/')!==false)$group='3';
            
            if (!isset($groups[$group])) $groups[$group] = array();
            $groups[$group][] = $path;
        }
        
        /*foreach ($groups as $key=>$list){
        	$text.="subgraph step{$key}{\n";//cluster_{$key}
		
            foreach ($list as $path){
                $toName = $path->toName;
                $fromName = $path->fromName;
                $fromName = basename(explode('?',$fromName)[0]);
                $toName = basename(explode('?',$toName)[0]);
                if ($fromName=='')$fromName=null;
                if ($toName=='')$toName=null;
                if ($fromName=='BEGIN')
                    $fromName = $graphName;
                if ($fromName===null){
                    $text.="\"".$fromName."\" [style=\"invis\"];\n";
                } else{
                    $text.="\"".$fromName."\" [shape=\"box\"];\n";
                }
            }

            $text.=" label = \"{$key}\";color=blue;style=dashed;rank = same;}\n";
        }*/
        //file_put_contents('../cachedPath.txt',json_encode(self::$cachedPath));
        
        $beginDone=false;
        foreach (self::$cachedPath as $path){
            $url = $path->toURI;
                         
            $urlList = explode('/',$url);
            $tempName='';$finalList='';
            foreach ($urlList as $part){
                if (strlen($tempName)+strlen($part)+1>10){
                    $finalList.=$tempName.$part."\n/";
                    $tempName='';
                } else {
                    $tempName.=$part.'/';
                }
            }
            if ($tempName!='')$finalList.=$tempName."\n";
            $ll = $finalList;//implode("\n/",);//$url;
            $toName = $path->toName;
            //if ($path->fromName!='BEGIN')
                $ll="";
                
            $fromName = $path->fromName;    
            $fromName = basename(explode('?',$fromName)[0]);
            $toName = basename(explode('?',$toName)[0]);
            if ($fromName=='')$fromName=null;
            if ($toName=='')$toName=null;
            
            if ($fromName=='BEGIN' && !$beginDone){
                $beginDone=true;
                $fromName = $graphName;
                $url = $_SERVER['REQUEST_URI'];
                $urlList = explode('/',$url);
                $tempName='';$finalList='';
                foreach ($urlList as $part){
                    if (strlen($tempName)+strlen($part)+1>10){
                        $finalList.=$tempName.$part."\n/";
                        $tempName='';
                    } else {
                        $tempName.=$part.'/';
                    }
                }
                if ($tempName!='')$finalList.=$tempName."\n";
                $ll2 = $finalList;
                $text.="\"".'BEGIN'.'"->"'.$fromName."\"[ label = \"".$path->toMethod." ".$ll2."\" ];\n";
            }
            
            if ($fromName=='BEGIN'){
                $fromName = $graphName;
               // $url = $_SERVER['REQUEST_URI'];
            }
            $text.="\"".$fromName.'"->"'.$toName."\"[ label = \"".$path->toMethod." ".$ll."\" ];\n";//.$ll
        }
        $text.="\n}";
        
        file_put_contents(dirname(__FILE__).'/../path/'.$graphName.'.gv',$text);
        if (self::$changedTree) return;
        //file_put_contents('../cachedPath.txt',json_encode(self::$cachedPath));
        
        $treePath = '/tmp/cache';
        $componentTag = self::$cachedPath[0]->toName;
        if ($componentTag=='BEGIN')$componentTag = self::$cachedPath[1]->toName;
        
        self::generatepath($treePath.'/tree/'.$componentTag);
        $tree = array();
        foreach (self::$cachedPath as $path){
            if ($path->fromMethod!='GET' && $path->fromName!='BEGIN') continue;
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
        if (strtoupper($method)!='GET') return null;
        $uTag = md5($URL);
        if (isset(self::$cachedData[$uTag]))
            return self::$cachedData[$uTag];

        return null;
    }
    
    public static function begin($Name, $URL, $URI, $method)
    {
        $fromName = basename(dirname($_SERVER['SCRIPT_NAME']));
        if (self::$begin===null){// && !self::$activeTree
            if (file_exists(realpath('../calls.log')))
                unlink(realpath('../calls.log'));
            //self::$begin = $_SERVER['REQUEST_METHOD'].self::generateURL();
            self::$begin = $method.$URL;
           // self::$cachedPath[] = new PathObject('BEGIN',null,null,$fromName,self::generateURL(), $_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);
        }
    }
    
    public static function addPath($Name, $URL, $URI, $method)
    {
        //if (strpos($_SERVER['SCRIPT_NAME'],'/UI/')!==false) {return null;}
        $fromName = basename(dirname($_SERVER['SCRIPT_NAME']));
        //$fromName= $_SERVER['SCRIPT_NAME'];
        //$fromName = basename(explode('?',$fromName)[0]);
        //$fromName = basename($fromName);

        //$fromName = basename($fromName);


        if (strpos($_SERVER['SCRIPT_NAME'],'/UI/')!==false) {
            $fromName='BEGIN';
        }
        ///Logger::Log('SCRIPT_NAME: '.$_SERVER['SCRIPT_NAME'], LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        ///Logger::Log('fromName: '.$fromName, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        ///Logger::Log('NAME: '.$Name, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        self::$cachedPath[] = new PathObject($fromName,self::generateURL(),$_SERVER['REQUEST_METHOD'],$Name, $URL, $URI,$method);
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
        
        //
        if (self::$begin!==null && self::$begin==$method.$URL){//self::$begin==$_SERVER['REQUEST_METHOD'].self::generateURL()
            file_put_contents('../cachedPath.txt',self::$begin.json_encode(self::$cachedPath));
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
        if (self::$tree!=null) return;
        if (strtoupper($method)!='GET') return;
        // search tree
        $treePath = '/tmp/cache';
        $uTag = md5($URL);
        $componentTag = $Name;
        if (file_exists($treePath.'/tree/'.$componentTag.'/'.$uTag)){
            self::$activeTree=true;
            self::$changedTree=true;
            $uriTag = self::generateETag($URL);
            $list = json_decode(file_get_contents($treePath.'/tree/'.$componentTag.'/'.$uTag),true);
            self::$tree = file_get_contents($treePath.'/tree/'.$componentTag.'/'.$uTag);
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
            //file_put_contents('../cachedPath.txt',json_encode($sources));//
            
            // call sources
            $allOK = true;
            //$temp = self::$cachedPath;
            $RequestList=array();
            foreach ($sources as $source){
                //$answ = Request::get($source['url'], array(),  '', true);
                $RequestList[]=$source['url'];
                /*if (!isset($answ['headers']['Etag']) || $answ['headers']['Etag']!=$source['eTag']){
                    $allOK = false;
                }*/
            }
            ///Logger::Log(implode("\n",$RequestList), LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            $answers = Request::post('http://localhost/uebungsplattform/DB/DBQuery2/query/procedure/multi', array(),  implode("\n",$RequestList), true);
            //Logger::Log($answers['content'], LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
            $answers = Query::decodeQuery($answers['content']);
            
            $i=0;
            foreach ($sources as $source){
                //$answ=$answers[$i];
                $gg=array($answers[$i],$answers[$i+1]);
                $eTag = CacheManager::generateETag(Query::encodeQuery($gg));//$answ['headers']['Etag']//!isset($answ['headers']['Etag']) || 
                if ($eTag!=$source['eTag']){
                    $allOK = false;
                }
                $i+=2;
            }
            
            //self::$cachedPath=$temp;
            
            //return null;
            if ($allOK){
                self::$changedTree=true;
                if (isset($list['BEGIN'])){
                    $data = file_get_contents($treePath.'/data/'.$list['BEGIN'][0]['name'].'/'.$list['BEGIN'][0]['eTag']);
                    self::cacheData($list['BEGIN'][0]['name'], $list['BEGIN'][0]['url'], json_decode($data), 200, $list['BEGIN'][0]['method']);
                    ///Logger::Log('caching', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                } else {
                    Logger::Log('nonono', LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
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