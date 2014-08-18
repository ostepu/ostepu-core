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
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

// runs the CConfig
$com = new CConfig( '' );

// runs the FSBinder
if ( !$com->used( ) )
    new FSBinder( );

/**
 * The class for storing files.
 */
class FSBinder
{

    /**
     * @var string $_baseDir the name of the folder where the files would be
     * stored in filesystem
     */
    private static $_baseDir = 'files';

    /**
     * the $_baseDir getter
     *
     * @return the value of $_baseDir
     */
    public static function getBaseDir( )
    {
        return FSBinder::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */
    public static function setBaseDir( $value )
    {
        FSBinder::$_baseDir = $value;
    }

    /**
     * @var Slim $_app the slim object
     */
    private $_app;

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct( )
    {

        // initialize slim
        $this->_app = new \Slim\Slim( array( 'debug' => true ) );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );

        // POST file
        $this->_app->post( 
                          '/:path+',
                          array( 
                                $this,
                                'postFile'
                                )
                          );

        // GET file as document
        $this->_app->get( 
                         '/:path+',
                         array( 
                               $this,
                               'getFile'
                               )
                         );

        // DELETE file
        $this->_app->delete( 
                            '/:path+',
                            array( 
                                  $this,
                                  'deleteFile'
                                  )
                            );

        // INFO file
        $this->_app->map( 
                         '/:path+',
                         array( 
                               $this,
                               'infoFile'
                               )
                         )->via( 'INFO' );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP POST request to
     * /$path.
     * The request body should contain a JSON object representing the file's
     * attributes.
     *
     * @param string[] $path The path where the file should be stored.
     */
    public function postFile( $path )
    {

        // if no path is passed, the request is invalid
        if ( count( $path ) == 0 ){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
            return;
        }

        $body = $this->_app->request->getBody( );
        $fileobject = File::decodeFile( $body );

        $filePath = FSBinder::$_baseDir . '/' . implode( 
                                                        '/',
                                                        array_slice( 
                                                                    $path,
                                                                    0
                                                                    )
                                                        );

        if ( !file_exists( $filePath ) ){
            FSBinder::generatepath( dirname( $filePath ) );

            // writes the file to filesystem
            $file = fopen(
                          $filePath,
                          'w'
                          );
            if ($file){
                fwrite( 
                       $file,
                       base64_decode( $fileobject->getBody( ) )
                       );
                fclose( $file );
                
            }else{
            $fileobject->setBody( null );
            $fileobject->addMessage("Datei konnte nicht im Dateisystem angelegt werden.");
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
        $fileobject->setAddress( FSBinder::$_baseDir . '/' . $filePath );
        $fileobject->setFileSize( filesize( $filePath ) );
        $fileobject->setHash( sha1_file( $filePath ) );

        $this->_app->response->setBody( File::encodeFile( $fileobject ) );
        $this->_app->response->setStatus( 201 );
    }

    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /$path.
     *
     * @param string[] $path The path where the requested file is stored.
     */
    public function getFile( $path )
    {

        // if no path is passed, the request is invalid
        if ( count( $path ) == 0 ){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
            return;
        }

        $filePath = FSBinder::$_baseDir . $this->_app->request->getResourceUri( );
        
        if ( strlen( $filePath ) > 0 && 
             file_exists( $filePath ) ){

            // the file was found
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                'application/octet-stream'
                                                );
            $this->_app->response->setStatus( 200 );
            readfile( $filePath );
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
     * /$path.
     *
     * @param string[] $path The path where the requested file is stored.
     */
    public function infoFile( $path )
    {

        // if no path is passed, the request is invalid
        if ( count( $path ) == 0 ){
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
            return;
        }

        $filePath = FSBinder::$_baseDir . $this->_app->request->getResourceUri( );

        if ( strlen( $filePath ) > 0 && 
             file_exists( $filePath ) ){

            // the file was found
            $file = new File( );
            $file->setAddress( $filePath );
            $file->setFileSize( filesize( $filePath ) );
            $file->setHash( sha1_file( $filePath ) );
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
     * /$path.
     *
     * @param string[] $path The path where the file which should be deleted is stored.
     */
    public function deleteFile( $path )
    {

        // if no path is passed, the request is invalid
        if ( count( $path ) == 0 ){
            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
            return;
        }

        // creates the path of the file in the file system
        $filePath = FSBinder::$_baseDir . '/' . implode( 
                                                        '/',
                                                        array_slice( 
                                                                    $path,
                                                                    0
                                                                    )
                                                        );

        if ( strlen( $filePath ) > 0 && 
             file_exists( $filePath ) ){

            // after the successful deletion, we want to return the file data
            $file = new File( );
            $file->setAddress( FSBinder::$_baseDir . '/' . $filePath );
            $file->setFileSize( filesize( $filePath ) );
            $file->setHash( sha1_file( $filePath ) );

            // removes the file
            unlink( $filePath );

            // the removing/unlink process failed, if the file still exists.
            if ( file_exists( $filePath ) ){
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
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     */
    public static function generatepath( $path )
    {
        if (!is_dir($path))          
            mkdir( $path , 0777, true);
        chmod( $path, 0777);
    }
}

 
?>