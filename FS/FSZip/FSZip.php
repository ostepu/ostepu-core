<?php 


/**
 * @file FSZip.php contains the FSZip class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );

\Slim\Slim::registerAutoloader( );

Logger::Log( 
            'begin FSZip',
            LogLevel::DEBUG
            );

// runs the CConfig
$com = new CConfig( FSZip::getBaseDir( ) );

// runs the FSZip
if ( !$com->used( ) )
    new FSZip( $com->loadConfig( ) );

Logger::Log( 
            'end FSZip',
            LogLevel::DEBUG
            );

/**
 * A class for creating and loading ZIP archives from the file system
 */
class FSZip
{

    /**
     * @var string $_baseDir the name of the folder where the zip files should be
     * stored in the file system
     */
    private static $_baseDir = 'zip';

    /**
     * the string $_baseDir getter
     *
     * @return the value of $_baseDir
     */
    public static function getBaseDir( )
    {
        return FSZip::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */
    public static function setBaseDir( $value )
    {
        FSZip::$_baseDir = $value;
    }

    /**
     * @var Slim $_app the slim object
     */
    private $_app = null;
    private $config = array();
    
    /**
     * @var Component $_conf the component data object
     */
    private $_conf = null;

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct( $conf )
    {
        $this->config = parse_ini_file( 
                                       dirname(__FILE__).'/config.ini',
                                       TRUE
                                       ); 
                                       
        // initialize component
        $this->_conf = $conf;

        // initialize slim
        $this->_app = new \Slim\Slim( );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            '_application/json'
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
                         
        // POST PostZipTemporary
        $this->_app->post( 
                          '/' . FSZip::$_baseDir . '/:filename(/)',
                          array( 
                                $this,
                                'postZip'
                                )
                          );

        // POST PostZip
        $this->_app->post( 
                          '/' . FSZip::$_baseDir . '(/)',
                          array( 
                                $this,
                                'postZip'
                                )
                          );

        // GET GetZipData
        $this->_app->get( 
                         '/' . FSZip::$_baseDir . '/:a/:b/:c/:file(/)',
                         array( 
                               $this,
                               'getZipData'
                               )
                         );

        // GET GetZipDocument
        $this->_app->get( 
                         '/' . FSZip::$_baseDir . '/:a/:b/:c/:file/:filename(/)',
                         array( 
                               $this,
                               'getZipDocument'
                               )
                         );

        // DELETE DeleteZip
        $this->_app->delete( 
                            '/' . FSZip::$_baseDir . '/:a/:b/:c/:file(/)',
                            array( 
                                  $this,
                                  'deleteZip'
                                  )
                            );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Creates a ZIP file consisting of the request body and permanently
     * stores it in the file system.
     *
     * Called when this component receives an HTTP POST request to /zip.
     * The request body should contain an array of JSON objects representing the files
     * which should be zipped and stored.
     */
    public function postZip( $filename = null )
    {
        $fileObject = File::decodeFile( $this->_app->request->getBody( ) );
        if ( !is_array( $fileObject ) )
            $fileObject = array( $fileObject );

        // generate sha1 hash for the zip, we have to create
        // (the name and the zip-hash are not the same)
        $hashArray = array( );
        foreach ( $fileObject as $part ){
            if ( $part->getBody( ) !== null ){
                $hashArray[] = $part->getBody( );
                
            } else 
                $hashArray[] = $part->getAddress( ) . $part->getDisplayName( );
        }
        
        $hash = sha1( implode( 
                              "\n",
                              $hashArray
                              ) );
       // unset($hashArray);

        // generate zip
        $filePath = FSZip::generateFilePath( 
                                            FSZip::getBaseDir( ),
                                            $hash
                                            );

        if (!file_exists($this->config['DIR']['files'].'/'.$filePath)){
            $zip = new ZipArchive( );
            // if the directory doesn't exist, create it
            FSZip::generatepath( $this->config['DIR']['files'].'/'.dirname( $filePath ) );

            if ( $zip->open( 
                            $this->config['DIR']['files'].'/'.$filePath,
                            ZIPARCHIVE::CREATE
                            ) === TRUE ){
                foreach ( $fileObject as &$part ){
                    if ( $part->getBody( ) !== null ){
                        $zip->addFromString( 
                                            $part->getDisplayName( ),
                                            base64_decode( $part->getBody( ) )
                                            );
                        
                    } else {
                        
                        $file = $this->config['DIR']['files']. '/' . $part->getAddress( );
                        if (file_exists($file)){
                            $zip->addFromString( 
                                                $part->getDisplayName( ),
                                                file_get_contents($file)
                                                );
                        } else {
                            $this->_app->response->setStatus( 409 );
                            $this->_app->response->setBody( File::encodeFile( new File() ) );
                            $zip->close( );
                            unlink( $filePath );
                            $this->_app->stop( );
                        }
                    }
                    //unset($part);
                }
                $zip->close( );
            } else {
                $this->_app->response->setStatus( 409 );
                $this->_app->response->setBody( File::encodeFile( new File() ) );
                $zip->close( );
                unlink( $filePath );
                $this->_app->stop( );
            }
        }

        if ($filename!=null){
            readfile( $this->config['DIR']['files'].'/'.$filePath );
            
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                'application/octet-stream'
                                                );
            $this->_app->response->headers->set( 
                                                'Content-Disposition',
                                                "attachment; filename=\"$filename\""
                                                );
        } else {
            $zipFile = new File( );
            $zipFile->setHash( $hash );
            $zipFile->setAddress( $filePath );
            
            if (file_exists($this->config['DIR']['files'].'/'.$filePath))
                $zipFile->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
            $this->_app->response->setBody( File::encodeFile($zipFile) );
        }
        $this->_app->response->setStatus( 201 );
    }
    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /zip/$a/$b/$c/$file/$filename.
     *
     * @param string $hash The hash of the file which should be returned.
     * @param string $filename A freely chosen filename of the returned file.
     */
    public function getZipDocument( 
                                    $a, $b, $c, $file,
                                    $filename
                                    )
    {

        $path = array(FSZip::getBaseDir( ),$a,$b,$c,$file);

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
     * /zip/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getZipData( $a, $b, $c, $file )
    {
        $path = array(FSZip::getBaseDir( ),$a,$b,$c,$file);

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
     * /zip/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deleteZip( $a, $b, $c, $file )
    {
        $path = array(FSZip::getBaseDir( ),$a,$b,$c,$file);

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
                                            $file
                                            )
    {
        if ( strlen( $file ) >= 4 ){
            return $type . '/' . $file[0] . '/' . $file[1] . '/' . $file[2] . '/' . substr( 
                                                                                           $file,
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

    /**
     * Selects the components which are responsible for handling the file with
     * the given hash.
     *
     * @param link[] $linkedComponents An array of links to components which could
     * possibly handle the file.
     * @param string $hash The hash of the file.
     */
    public static function filterRelevantLinks( 
                                               $linkedComponents,
                                               $hash
                                               )
    {
        $result = array( );
        foreach ( $linkedComponents as $link ){
            $in = explode( 
                          '-',
                          $link->getRelevanz( )
                          );
            if ( count( $in ) < 2 ){
                $result[] = $link;
                
            }elseif ( FSZip::isRelevant( 
                                        $hash,
                                        $in[0],
                                        $in[1]
                                        ) ){
                $result[] = $link;
            }
        }
        return $result;
    }

    /**
     * Decides if the given component is responsible for the specific hash.
     *
     * @param string $hash The hash of the file.
     * @param string $_relevantBegin The minimum hash the component is responsible for.
     * @param string $_relevantEnd The maximum hash the component is responsible for.
     */
    public static function isRelevant( 
                                      $hash,
                                      $relevant_begin,
                                      $relevant_end
                                      )
    {

        // to compare the begin and the end, we need an other form
        $begin = hexdec( substr( 
                                $relevant_begin,
                                0,
                                strlen( $relevant_begin )
                                ) );
        $end = hexdec( substr( 
                              $relevant_end,
                              0,
                              strlen( $relevant_end )
                              ) );

        // the numeric form of the test hash
        $current = hexdec( substr( 
                                  $hash,
                                  0,
                                  strlen( $relevant_end )
                                  ) );

        if ( $current >= $begin && 
             $current <= $end ){
            return true;
            
        } else 
            return false;
    }
}

 
?>