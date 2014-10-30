<?php 


/**
 * @file FSBinder.php contains the FSBinder class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/Platform.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures/File.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/MimeReader.php' );

\Slim\Slim::registerAutoloader( );

/**
 * The class for storing files.
 */
class FSBinder
{

    /**
     * @var Slim $_app the slim object
     */
    private $_app;
    private $config = array();

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct( )
    {
        if (file_exists(dirname(__FILE__).'/config.ini'))
            $this->config = parse_ini_file( 
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           ); 
                                       
        // runs the CConfig
        $com = new CConfig( '', dirname(__FILE__) );

        // runs the FSBinder
        if ( $com->used( ) ) return;
            ///$_conf = $com->loadConfig( );
            
        // initialize component
        ///$this->_conf = $_conf;
        
        // initialize slim
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

        // POST file
        $this->_app->post( 
                          '/:folder/:a/:b/:c/:file',
                          array( 
                                $this,
                                'postFile'
                                )
                          );

        // GET file as document
        $this->_app->get( 
                         '/:folder/:a/:b/:c/:file/:filename',
                         array( 
                               $this,
                               'getFile'
                               )
                         );

        // DELETE file
        $this->_app->delete( 
                            '/:folder/:a/:b/:c/:file',
                            array( 
                                  $this,
                                  'deleteFile'
                                  )
                            );

        // GET file
        $this->_app->map( 
                         '/:folder/:a/:b/:c/:file',
                         array( 
                               $this,
                               'infoFile'
                               )
                         )->via( 'INFO', 'GET' );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP POST request to
     * /$folder/$a/$b/$c/$file.
     * The request body should contain a JSON object representing the file's
     * attributes.
     *
     * @param string[] $path The path where the file should be stored.
     */
    public function postFile( $folder, $a, $b, $c, $file )
    {
        $path = array($folder,$a,$b,$c, $file);

        $body = $this->_app->request->getBody( );
        $fileobject = File::decodeFile( $body );

        $filePath = implode( 
                            '/',
                            array_slice( 
                                        $path,
                                        0
                                        )
                            );

        if ( !file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){
            FSBinder::generatepath( $this->config['DIR']['files'].'/'.dirname( $filePath ) );

            // writes the file to filesystem
            $file = fopen(
                          $this->config['DIR']['files'].'/'.$filePath,
                          'w'
                          );
            if ($file){
                fwrite( 
                       $file,
                       base64_decode( $fileobject->getBody( ) )
                       );
                fclose( $file );
                $fileObject->setStatus(201);
                
            }else{
            $fileobject->setBody( null );
            $fileobject->addMessage("Datei konnte nicht im Dateisystem angelegt werden.");
            $fileObject->setStatus(409);
            Logger::Log( 
                    'POST postFile failed',
                    LogLevel::ERROR
                    );
                    
            $this->_app->response->setBody( File::encodeFile( $fileobject ) );
            $this->_app->response->setStatus( 409 );
            $this->_app->stop();
            }
        }

        // resets the file content
        $fileobject->setBody( null );

        // generate new file address, file size and file hash
        $fileobject->setAddress( $filePath );
        $fileobject->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
        $fileobject->setHash( sha1_file( $this->config['DIR']['files'].'/'.$filePath ) );
        $fileobject->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));

        $this->_app->response->setBody( File::encodeFile( $fileobject ) );
        $this->_app->response->setStatus( 201 );
    }

    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /$folder/$a/$b/$c/$file/$filename.
     *
     * @param string[] $path The path where the requested file is stored.
     */
    public function getFile( $folder, $a, $b, $c, $file, $filename )
    {
        $path = array($folder,$a,$b,$c,$file);

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
    }

    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP INFO request to
     * /$folder/$a/$b/$c/$file.
     *
     * @param string[] $path The path where the requested file is stored.
     */
    public function infoFile( $folder, $a, $b, $c, $file )
    {
        $path = array($folder,$a,$b,$c,$file);

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
            $file->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));
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
     * /$folder/$a/$b/$c/$file.
     *
     * @param string[] $path The path where the file which should be deleted is stored.
     */
    public function deleteFile( $folder, $a, $b, $c, $file )
    {

        $path = array($folder,$a,$b,$c,$file);

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
            $file->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));

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
                    
        if (!file_exists(dirname(__FILE__).'/config.ini')){
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
        if (file_exists(dirname(__FILE__).'/config.ini') && !unlink(dirname(__FILE__).'/config.ini')){
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
        
            $file = dirname(__FILE__).'/config.ini';
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