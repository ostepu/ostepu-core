<?php 


/**
 * @file FSFile.php contains the FSFile class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example FS/FSFile/FileSample.json
 * @date 2013-2014
 */ 

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * The class for storing and hashing files.
 */
class FSFile
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
        return FSFile::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */
    public static function setBaseDir( $value )
    {
        FSFile::$_baseDir = $value;
    }

    /**
     * @var Slim $_app the slim object
     */
    private $_app;

    /**
     * @var Component $_conf the component data object
     */
    private $_conf;
    private $config = array();

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct( )
    {
        // runs the CConfig
        $com = new CConfig( FSFile::getBaseDir( ), dirname(__FILE__) );

        // runs the FSFile
        if ( $com->used( ) ) return;
            $_conf = $com->loadConfig( );
            
        $this->config = parse_ini_file( 
                                       dirname(__FILE__).'/config.ini',
                                       TRUE
                                       ); 
                                       
        $this->_app = new \Slim\Slim( array( 'debug' => true ) );

        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST AddPlatform
        $this->_app->post( 
                         '/platform',
                         array( 
                               $this,
                               'addPlatform'
                               )
                         );
                         
        // DELETE DeletePlatform
        $this->_app->delete( 
                         '/platform',
                         array( 
                               $this,
                               'deletePlatform'
                               )
                         );
                         
        // GET GetExistsPlatform
        $this->_app->get( 
                         '/link/exists/platform',
                         array( 
                               $this,
                               'getExistsPlatform'
                               )
                         );
                         
        // POST File
        $this->_app->post( 
                          '/'.FSFile::getBaseDir( ).'(/)',
                          array( 
                                $this,
                                'postFile'
                                )
                          );

        // GET Filedata
        $this->_app->map( 
                         '/'.FSFile::getBaseDir( ).'/:a/:b/:c/:file(/)',
                         array( 
                               $this,
                               'getFileData'
                               )
                         )->via( 
                                'GET',
                                'INFO'
                                );;

        // GET GetFileDocument
        $this->_app->get( 
                         '/'.FSFile::getBaseDir( ).'/:a/:b/:c/:file/:filename(/)',
                         array( 
                               $this,
                               'getFileDocument'
                               )
                         );

        // DELETE File
        $this->_app->delete( 
                            '/'.FSFile::getBaseDir( ).'/:a/:b/:c/:file(/)',
                            array( 
                                  $this,
                                  'deleteFile'
                                  )
                            );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Prepares the saving process by generating the hash and the place where the file is stored.
     *
     * Called when this component receives an HTTP POST request to
     * /$a/$b/$c/$file.
     * The request body should contain a JSON object representing the file's
     * attributes.
     */
    public function postFile( )
    {
        $body = $this->_app->request->getBody( );
        $fileObjects = File::decodeFile( $body );

        // always been an array
        $arr = true;
        if ( !is_array( $fileObjects ) ){
            $fileObjects = array( $fileObjects );
            $arr = false;
        }

        $res = array( );

        foreach ( $fileObjects as $fileObject ){

            $fileObject->setHash( sha1( base64_decode( $fileObject->getBody( ) ) ) );
            $filePath = FSFile::generateFilePath( 
                                                 FSFile::getBaseDir( ),
                                                 $fileObject->getHash( )
                                                 );
            $fileObject->setAddress( FSFile::getBaseDir( ) . '/' . $fileObject->getHash( ) );

            if ( !file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){
                FSFile::generatepath( $this->config['DIR']['files'].'/'.dirname( $filePath ) );

                // writes the file to filesystem
                $file = fopen(
                              $this->config['DIR']['files'].'/'.$filePath,
                              'w'
                              );
                if ($file){
                    fwrite( 
                           $file,
                           base64_decode( $fileObject->getBody( ) )
                           );
                    fclose( $file );
                    $fileObject->setStatus(201);
                 
                }else{
                    $fileObject->addMessage("Datei konnte nicht im Dateisystem angelegt werden.");
                    $fileObject->setStatus(409);
                    Logger::Log( 
                            'POST postFile failed',
                            LogLevel::ERROR
                            );

                    $this->_app->response->setBody( File::encodeFile( $fileObject ) );
                }
            }

            // resets the file content
            $fileObject->setBody( null );

            // generate new file address, file size and file hash
            $fileObject->setAddress( $filePath );
            $fileObject->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
            $fileObject->setHash( sha1_file( $this->config['DIR']['files'].'/'.$filePath ) );

            $res[] = $fileObject;

        }

        if ( !$arr && 
             count( $res ) == 1 )
            $res = $res[0];

        $this->_app->response->setStatus( 201 );
        $this->_app->response->setBody( File::encodeFile( $res ) );
    }

    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$a/$b/$c/$file/$filename.
     *
     * @param string $hash The hash of the file which should be returned.
     * @param string $filename A freely chosen filename of the returned file.
     */
    public function getFileDocument( 
                                    $a, $b, $c, $file,
                                    $filename
                                    )
    {

        $path = array(FSFile::getBaseDir( ),$a,$b,$c,$file);

        $filePath = implode( 
                            '/',
                            array_slice( 
                                        $path,
                                        0
                                        )
                            );

        if ( strlen( $this->config['DIR']['files'].'/'.$filePath ) > 1 && 
             file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){

            // the file was found
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                'application/octet-stream'
                                                );
            $this->_app->response->headers->set( 
                                    'Content-Disposition',
                                    "attachment; filename=\"$filename\""
                                    );
                                            
            $this->_app->response->setStatus( 200 );
            readfile( $this->config['DIR']['files'].'/'.$filePath );
            $this->_app->stop( );
            
        } else {
            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
        }

        $this->_app->stop( );
    }

    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getFileData( $a, $b, $c, $file )
    {
        $path = array(FSFile::getBaseDir( ),$a,$b,$c,$file);

        $filePath = implode( 
                            '/',
                            array_slice( 
                                        $path,
                                        0
                                        )
                            );

        if ( strlen( $this->config['DIR']['files'].'/'.$filePath ) > 0 && 
             file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){

            // the file was found
            $file = new File( );
            $file->setAddress( $filePath );
            $file->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
            $file->setHash( sha1_file( $this->config['DIR']['files'].'/'.$filePath ) );
            $this->_app->response->setBody( File::encodeFile( $file ) );
            $this->_app->response->setStatus( 200 );
            $this->_app->stop( );
            
        } else {
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
        }
    }

    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deleteFile( $a, $b, $c, $file )
    {
        $path = array(FSFile::getBaseDir( ),$a,$b,$c,$file);

        $filePath = implode( 
                            '/',
                            array_slice( 
                                        $path,
                                        0
                                        )
                            );

        if ( strlen( $filePath ) > 0 && 
             file_exists( $this->config['DIR']['files'] . '/' . $filePath ) ){

            // after the successful deletion, we want to return the file data
            $file = new File( );
            $file->setAddress( $filePath );
            $file->setFileSize( filesize( $this->config['DIR']['files'] . '/' . $filePath ) );
            $file->setHash( sha1_file( $this->config['DIR']['files'] . '/' . $filePath ) );

            // removes the file
            unlink( $this->config['DIR']['files'] . '/' . $filePath );

            // the removing/unlink process failed, if the file still exists.
            if ( file_exists( $this->config['DIR']['files'] . '/' . $filePath ) ){
                $this->_app->response->setStatus( 409 );
                $this->_app->response->setBody( File::encodeFile( new File( ) ) );
                $this->_app->stop( );
            }

            // the file is removed
            $this->_app->response->setBody( File::encodeFile( $file ) );
            $this->_app->response->setStatus( 201 );
            $this->_app->stop( );
            
        } else {

            // file does not exist
            $this->_app->response->setStatus( 409 );
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->stop( );
        }
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( )
    {
        Logger::Log( 
                    'starts GET GetExistsPlatform',
                    LogLevel::DEBUG
                    );
                    
        if (!file_exists('config.ini')){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop();
        }
       
        $this->_app->response->setStatus( 200 );
        $this->_app->response->setBody( '' );  
    }
    
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( )
    {
        Logger::Log( 
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );
        if (file_exists('config.ini') && !unlink('config.ini')){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop();
        }
        
        $this->_app->response->setStatus( 201 );
        $this->_app->response->setBody( '' );
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( )
    {
        Logger::Log( 
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );

        // decode the received course data, as an object
        $insert = Platform::decodePlatform( $this->_app->request->getBody( ) );

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){
        
            $file = 'config.ini';
            $text = "[DIR]\n".
                    "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$in->getTempDirectory()))."\"\n".
                    "files = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$in->getFilesDirectory()))."\"\n";
                    
            if (!@file_put_contents($file,$text)){
                Logger::Log( 
                            'POST AddPlatform failed, config.ini no access',
                            LogLevel::ERROR
                            );

                $this->_app->response->setStatus( 409 );
                $this->_app->stop();
            }   

            $platform = new Platform();
            $platform->setStatus(201);
            $res[] = $platform;
            $this->_app->response->setStatus( 201 );
        }

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->_app->response->setBody( Platform::encodePlatform( $res[0] ) );
            
        } else 
            $this->_app->response->setBody( Platform::encodePlatform( $res ) );
    }
    
    /**
     * Creates a file path by splitting the hash.
     *
     * @param string $type The prefix of the file path.
     * @param string $hash The hash of the file.
     */
    public static function generateFilePath( 
                                            $type,
                                            $hash
                                            )
    {
        if ( strlen( $hash ) >= 4 ){
            return $type . '/' . $hash[0] . '/' . $hash[1] . '/' . $hash[2] . '/' . substr( 
                                                                                           $hash,
                                                                                           3
                                                                                           );
            
        } else 
            return'';
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