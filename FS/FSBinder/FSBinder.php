<?php
/**
 * @file FSBinder.php contains the FSBinder class
 * 
 * @author Till Uhlig
 * @author Felix Schmidt
 */ 

require_once( 'Include/Slim/Slim.php' );
include_once( 'Include/CConfig.php' );
include_once( 'Include/Structures.php' );

\Slim\Slim::registerAutoloader();

$com = new CConfig("");

if (!$com->used())
    new FSBinder();

/**
 * The class for storing files.
 */
class FSBinder
{
    private static $_baseDir = "files";
    public static function getBaseDir()
    {
        return FSBinder::$_baseDir;
    }
    public static function setBaseDir($value)
    {
        FSBinder::$_baseDir = $value;
    }
    
    private $_app;
    private $_conf;


    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        $this->_app = new \Slim\Slim();

        $this->_app->response->headers->set('Content-Type', 'application/json');
        
        // POST file
        $this->_app->post('/:data+', array($this,'postFile'));
        
        // GET file as document
        $this->_app->get('/:data+', array($this,'getFile'));
        
        // DELETE file
        $this->_app->delete('/:data+', array($this,'deleteFile'));
        
        // INFO file
        $this->_app->map('/:data+', array($this,'infoFile'))->via('INFO');
        
        // run Slim
        $this->_app->run();
    }


    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP POST request to
     * /$data.
     * The request body should contain a JSON object representing the file's
     * attributes.
     *
     * @param string[] $data The path where the file should be stored.
     */
    public function postFile($data)
    { 
        $body = $this->_app->request->getBody();
        $fileobject = File::decodeFile($body);
            
        $filePath = FSBinder::$_baseDir."/".implode("/",array_slice ($data,0));
        FSBinder::generatepath(dirname($filePath));
            
        $file = fopen($filePath,"w");
        fwrite($file, base64_decode($fileobject->getBody()));
        fclose($file);

        $fileobject->setBody(null);
        $fileobject->setAddress(FSBinder::$_baseDir."/".$filePath);
        $fileobject->setFileSize(filesize($filePath));
        $fileobject->setHash(sha1_file($filePath));
        $this->_app->response->setBody(File::encodeFile($fileobject));
        $this->_app->response->setStatus(201);
    }


    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /$data.
     *
     * @param string[] $data The path where the requested file is stored.
     */
    public function getFile($data)
    {      
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $filePath = FSBinder::$_baseDir . $this->_app->request->getResourceUri();
        
        if (strlen($filePath)>0 && file_exists($filePath)){
           $this->_app->response->headers->set('Content-Type', 'application/octet-stream');
           $this->_app->response->setStatus(200);
           readfile($filePath);
           $this->_app->stop();
        } else{
           $this->_app->response->setStatus(409);
           $this->_app->stop();
        }
    }


    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP INFO request to
     * /$data.
     *
     * @param string[] $data The path where the requested file is stored.
     */
    public function infoFile($data)
    { 
        if (count($data)==0){
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
     
        $filePath = FSBinder::$_baseDir . $this->_app->request->getResourceUri();
        
        if (strlen($filePath)>0 && file_exists($filePath)){  
            $file = new File();
            $file->setAddress($filePath);
            $file->setFileSize(filesize($filePath));
            $file->setHash(sha1_file($filePath));
            $this->_app->response->setBody(File::encodeFile($file));
            $this->_app->response->setStatus(200);
            $this->_app->stop();
        } else{
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->response->setStatus(409);
            $this->_app->stop();
        }
    }


    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /$data.
     *
     * @param string[] $data The path where the file which should be deleted is stored.
     */
    public function deleteFile($data)
    {
        if (count($data)==0){
            $this->_app->response->setStatus(409);
            $this->_app->stop();
            return;
        }
        
        $filePath = FSBinder::$_baseDir."/".implode("/",array_slice ($data,0));
                    
        if (strlen($filePath)>0 && file_exists($filePath)){ 
            $file = new File();
            $file->setAddress(FSBinder::$_baseDir."/".$filePath);
            $file->setFileSize(filesize($filePath));
            $file->setHash(sha1_file($filePath));
            unlink($filePath);
            if (file_exists($filePath)){
                $this->_app->response->setStatus(452);
                $this->_app->response->setBody(File::encodeFile(new File()));
                $this->_app->stop();
            }
                
            $this->_app->response->setBody(File::encodeFile($file));
            $this->_app->response->setStatus(252);
            $this->_app->stop();
        } else{
            // file does not exist
            $this->_app->response->setStatus(409);
            $this->_app->response->setBody(File::encodeFile(new File()));
            $this->_app->stop();
        }      
    }


    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string[] $path The path which should be created.
     */
    public static function generatepath($path)
    {
        $parts = explode("/", $path);
        if (count($parts)>0){
            $path = $parts[0];
            for($i=1;$i<=count($parts);$i++){
                if (!is_dir($path))
                    mkdir($path,0755);
                if ($i<count($parts))
                    $path = $path.'/'.$parts[$i];
            }
        }
    }
}

?>