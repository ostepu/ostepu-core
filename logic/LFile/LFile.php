<?php 


/**
 * @file LFile.php contains the LFile class
 *
 * @author Till Uhlig 
 * @date 2014
 */ 

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Request.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/Logger.php' );

include_once ( './LFileHandler.php' );
//var_dump($_SERVER);
\Slim\Slim::registerAutoloader( );

/**
 * The class for storing and hashing files.
 */
class LFile
{

    /**
     * @var string $_baseDir the root directory of this component.
     */
    private static $_baseDir = 'file';

    /**
     * the $_baseDir getter
     *
     * @return the value of $_baseDir
     */
    public static function getBaseDir( )
    {
        return LFile::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */
    public static function setBaseDir( $value )
    {
        LFile::$_baseDir = $value;
    }

    /**
     * @var Slim $_app the slim object
     */
    private $_app;

    /**
     * @var Component $_conf the component data object
     */
    private $_conf;

    /**
     * @var Link[] $_fs link to component which work with files, e.g. FSFile
     */
    private $_fs = array();
    
        /**
     * @var Link[] $_db link to component which work with files, e.g. DBFile
     */
    private $_db = array();

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LFile::$_baseDir );

        // runs the LFile
        if ( $com->used( ) ) return;
        $conf = $com->loadConfig( );
            
        $this->_conf = $conf;
        $this->_fs = CConfig::getLinks($this->_conf->getLinks( ),'file');
        $this->_db = CConfig::getLinks($this->_conf->getLinks( ),'fileDb');

        $this->_app = new \Slim\Slim( array( 'debug' => true ) );
        $this->_app->response->setStatus( 404 );

        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );
                                            
        // POST File
        $this->_app->post( 
                          '/' . LFile::$_baseDir . '(/)',
                          array( 
                                $this,
                                'postFile'
                                )
                          );
                          
        // POST PathFile
        $this->_app->post( 
                          '/' . LFile::$_baseDir . '/:path(/)',
                          array( 
                                $this,
                                'postPathFile'
                                )
                          );
                                                      
        // DELETE File
        $this->_app->delete( 
                            '/' . LFile::$_baseDir. '/:fileid',
                            array( 
                                  $this,
                                  'deleteFile'
                                  )
                            );

        // run Slim
        $this->_app->run( );
    }
   
    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$path(/)
     *
     * @param String the path, where the file should be stored
     */
    public function postPathFile( $path)
    {
        Logger::Log( 
                'starts POST postFile',
                LogLevel::DEBUG
                );
                   
        $this->_app->response->setStatus( 201 );                   
        $body = $this->_app->request->getBody( );
        $fileObjects = File::decodeFile( $body );
        
        if (!is_array($path)) $path = array($path);
        $temp="";
        foreach ($path as $part){
            if ($part!='')
            $temp .= $part.'/';
        }
        $path = $temp;

        // always been an array
        $arr = true;
        if ( !is_array( $fileObjects ) ){
            $fileObjects = array( $fileObjects );
            $arr = false;
        }

        $res = array( );

        foreach ( $fileObjects as $fileObject ){
            if ($fileObject->getBody() == null && $fileObject->getAddress() == null){
                $result = new File();
                $result->setBody(null);
                $result->setStatus(409);
                $res[] = $result;
                continue;
            }
            
            $result = LFileHandler::add($this->_db,$this->_fs,$path, array(),$fileObject);
            
            if ( $result !== null){
                $result->setStatus(201);
                $result->setBody(null);
                $res[] = $result; 
            } else {
                $result = $fileObject;
                $result->setBody(null);
                $result->setStatus(409);
                $result->addMessage("Die Datei konnte nicht gespeichert werden.");
                $res[] = $result;
                
                Logger::Log( 
                            'POST postPathFile failed',
                            LogLevel::ERROR
                            );
            }
        }

        if ( !$arr && 
             count( $res ) == 1 )
            $res = $res[0];

        $this->_app->response->setBody( File::encodeFile( $res ) );
    }
   
    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP POST request to
     * /file(/)
     */
    public function postFile( )
    {
        $this->postPathFile(array(''));
    }

    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file(/)
     */
    public function deleteFile( $fileid )
    {
        Logger::Log( 
                'starts Delete deleteFile',
                LogLevel::DEBUG
                );

        $this->_app->response->setStatus( 201 );

        $fileObject = new File();
        $fileObject->setFileId($fileid);

        $res = null;
        
        if ($fileObject!==null && $fileObject !== array()){
            $result = LFileHandler::delete($this->_db, $this->_fs, array(), $fileObject);    
        } else
            $result = null;
            
        if ( $result !== null){
            $result->setStatus(201);
            $res = $result; 
        } else {
            $result = new File();
            $result->getMessages()[] = ("Die Datei konnte nicht gelöscht werden.");
            $result->setStatus(409);
            $res = $result;
            
            $this->_app->response->setStatus( 409 );
        }

        $this->_app->response->setBody( File::encodeFile( $res ) );                        
    }
}

 
?>