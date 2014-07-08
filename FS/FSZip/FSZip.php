<?php 


/**
 * @file FSZip.php contains the FSZip class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

require ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
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

    /**
     * @var Component $_conf the component data object
     */
    private $_conf = null;

    /**
     * @var Link $getFile a link to a component where we get our files from, e.g. FSControl
     */
    private $getFile = array( );

    /**
     * @var Link[] $_fs links to components which work with files, e.g. FSBinder
     */
    private $_fs = array( );

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

        // initialize component
        $this->_conf = $conf;
        $this->_fs = CConfig::deleteFromArray( 
                                              $this->_conf->getLinks( ),
                                              'getFile'
                                              );
        $this->getFile = array( CConfig::getLink( 
                                                 $conf->getLinks( ),
                                                 'getFile'
                                                 ) );

        // initialize slim
        $this->_app = new \Slim\Slim( );
        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            '_application/json'
                                            );

        // POST PostZipTemporary
        $this->_app->post( 
                          '/' . FSZip::$_baseDir . '/:filename(/)',
                          array( 
                                $this,
                                'postZipTemporary'
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
                         '/' . FSZip::$_baseDir . '/:hash(/)',
                         array( 
                               $this,
                               'getZipData'
                               )
                         );

        // GET GetZipDocument
        $this->_app->get( 
                         '/' . FSZip::$_baseDir . '/:hash/:filename(/)',
                         array( 
                               $this,
                               'getZipDocument'
                               )
                         );

        // DELETE DeleteZip
        $this->_app->delete( 
                            '/' . FSZip::$_baseDir . '/:hash(/)',
                            array( 
                                  $this,
                                  'deleteZip'
                                  )
                            );

        // starts slim only if the right prefix was received
        if ( strpos( 
                    $this->_app->request->getResourceUri( ),
                    '/' . FSZip::$_baseDir
                    ) === 0 ){

            // run Slim
            $this->_app->run( );
        }
    }

    /**
     * Creates a ZIP file consisting of the request body and permanently
     * stores it in the file system.
     *
     * Called when this component receives an HTTP POST request to /zip.
     * The request body should contain an array of JSON objects representing the files
     * which should be zipped and stored.
     */
    public function postZip( )
    {
        $body = $this->_app->request->getBody( );
        $fileObject = File::decodeFile( $body );
        if ( !is_array( $fileObject ) )
            $fileObject = array( $fileObject );

        // generate sha1 hash for the zip, we have to create
        // (the name and the hash are the same)
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

        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'INFO',
                                        '/' . FSZip::generateFilePath( 
                                                                      FSZip::getBaseDir( ),
                                                                      $hash
                                                                      ),
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $tempObject = File::decodeFile( $result['content'] );
            $tempObject->setAddress( FSZip::getBaseDir( ) . '/' . $hash );
            $tempObject->setBody( null );
            $this->_app->response->setStatus( 201 );
            $this->_app->response->setBody( File::encodeFile( $tempObject ) );
            $this->_app->stop( );
        }

        // generate zip
        $zip = new ZipArchive( );
        $savepath = 'temp/' . $hash;

        // if the directory doesn't exist, create it
        FSZip::generatepath( dirname( $savepath ) );

        if ( $zip->open( 
                        $savepath,
                        ZIPARCHIVE::CREATE
                        ) === TRUE ){
            foreach ( $fileObject as $part ){
                if ( $part->getBody( ) !== null ){
                    $zip->addFromString( 
                                        $part->getDisplayName( ),
                                        base64_decode( $part->getBody( ) )
                                        );
                    
                } else {
                    $links = FSZip::filterRelevantLinks( 
                                                        $this->getFile,
                                                        $part->getHash( )
                                                        );
                    $result = Request::routeRequest( 
                                                    'GET',
                                                    '/' . $part->getAddress( ) . '/' . $part->getDisplayName( ),
                                                    $this->_app->request->headers->all( ),
                                                    '',
                                                    $links,
                                                    explode( 
                                                            '/',
                                                            $part->getAddress( )
                                                            )[0],
                                                    'getFile'
                                                    );

                    if ( isset( $result['content'] ) ){
                        $zip->addFromString( 
                                            $part->getDisplayName( ),
                                            $result['content']
                                            );
                        
                    } else {
                        $this->_app->response->setStatus( 409 );
                        $zipFile->setBody( null );
                        $this->_app->response->setBody( File::encodeFile( $zipFile ) );
                        $zip->close( );
                        unlink( $savepath );
                        $this->_app->stop( );
                    }
                }
            }
            $zip->close( );
        }

        // save zip to filesystem
        $zipFile = new File( );
        $zipFile->setHash( $hash );
        $zipFile->setBody( base64_encode( file_get_contents( $savepath ) ) );
        $filePath = FSZip::generateFilePath( 
                                            FSZip::getBaseDir( ),
                                            $zipFile->getHash( )
                                            );
        $zipFile->setAddress( FSZip::getBaseDir( ) . '/' . $zipFile->getHash( ) );

        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $zipFile->getHash( )
                                            );

        $result = Request::routeRequest( 
                                        'POST',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        File::encodeFile( $zipFile ),
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        unlink( $savepath );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $tempObject = File::decodeFile( $result['content'] );
            $zipFile->setHash( $tempObject->getHash( ) );
            $zipFile->setFileSize( $tempObject->getFileSize( ) );
            $zipFile->setBody( null );
            $this->_app->response->setStatus( $result['status'] );
            $this->_app->response->setBody( File::encodeFile( $zipFile ) );
            
        } else {
            $this->_app->response->setStatus( 409 );
            $zipFile->setBody( null );
            $this->_app->response->setBody( File::encodeFile( $zipFile ) );
            $this->_app->stop( );
        }
    }

    /**
     * Creates a ZIP file consisting of the request body and permanently
     * stores it in the file system.
     *
     * Called when this component receives an HTTP POST request to /zip.
     * The request body should contain an array of JSON objects representing the files
     * which should be zipped and stored.
     */
    public function postZipTemporary( $filename )
    {
        $body = $this->_app->request->getBody( );
        $fileObject = File::decodeFile( $body );
        if ( !is_array( $fileObject ) )
            $fileObject = array( $fileObject );

        // generate sha1 hash for the zip, we have to create
        // (the name and the hash are not the same)
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

        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'GET',
                                        '/' . FSZip::generateFilePath( 
                                                                      FSZip::getBaseDir( ),
                                                                      $hash
                                                                      ),
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $this->_app->response->setBody( $result['content'] );
            $this->_app->response->setStatus( 201 );
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                'application/octet-stream'
                                                );
            $this->_app->response->headers->set( 
                                                'Content-Disposition',
                                                "attachment; filename=\"$filename\""
                                                );
            $this->_app->stop( );
        }

        // generate zip
        $zip = new ZipArchive( );
        $savepath = 'temp/' . $hash;

        // if the directory doesn't exist, create it
        FSZip::generatepath( dirname( $savepath ) );

        if ( $zip->open( 
                        $savepath,
                        ZIPARCHIVE::CREATE
                        ) === TRUE ){
            foreach ( $fileObject as $part ){
                if ( $part->getBody( ) !== null ){
                    $zip->addFromString( 
                                        $part->getDisplayName( ),
                                        base64_decode( $part->getBody( ) )
                                        );
                    
                } else {
                    $links = FSZip::filterRelevantLinks( 
                                                        $this->getFile,
                                                        $part->getHash( )
                                                        );
                    $result = Request::routeRequest( 
                                                    'GET',
                                                    '/' . $part->getAddress( ) . '/' . $part->getDisplayName( ),
                                                    $this->_app->request->headers->all( ),
                                                    '',
                                                    $links,
                                                    explode( 
                                                            '/',
                                                            $part->getAddress( )
                                                            )[0],
                                                    'getFile'
                                                    );

                    if ( isset( $result['content'] ) ){
                        $zip->addFromString( 
                                            $part->getDisplayName( ),
                                            $result['content']
                                            );
                        
                    } else {
                        $this->_app->response->setStatus( 409 );
                        $zipFile->setBody( null );
                        $this->_app->response->setBody( File::encodeFile( $zipFile ) );
                        $zip->close( );
                        unlink( $savepath );
                        $this->_app->stop( );
                    }
                }
            }
            $zip->close( );
        }

        // save zip to filesystem
        $zipFile = new File( );
        $zipFile->setHash( $hash );
        $zipFile->setBody( base64_encode( file_get_contents( $savepath ) ) );
        $filePath = FSZip::generateFilePath( 
                                            FSZip::getBaseDir( ),
                                            $zipFile->getHash( )
                                            );
        $zipFile->setAddress( FSZip::getBaseDir( ) . '/' . $zipFile->getHash( ) );

        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $zipFile->getHash( )
                                            );

        $result = Request::routeRequest( 
                                        'POST',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        File::encodeFile( $zipFile ),
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $this->_app->response->setStatus( 201 );

            /*$tempObject = File::decodeFile($result['content']);
             $zipFile->setHash($tempObject->getHash());
             $zipFile->setFileSize($tempObject->getFileSize());
             $zipFile->setBody(null);
             $this->_app->response->setStatus($result['status']);
            $this->_app->response->setBody(File::encodeFile($zipFile)); */
            readfile( $savepath );
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                'application/octet-stream'
                                                );
            $this->_app->response->headers->set( 
                                                'Content-Disposition',
                                                "attachment; filename=\"$filename\""
                                                );
            unlink( $savepath );
            
        } else {
            $this->_app->response->setStatus( 409 );
            $zipFile->setBody( null );
            $this->_app->response->setBody( File::encodeFile( $zipFile ) );
            unlink( $savepath );
            $this->_app->stop( );
        }
    }

    /**
     * Returns a ZIP file.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$hash/$filename.
     *
     * @param string $hash The hash of the ZIP file which should be returned.
     * @param string $filename A freely chosen filename of the returned ZIP file.
     */
    public function getZipDocument( 
                                   $hash,
                                   $filename
                                   )
    {
        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $filePath = FSZip::generateFilePath( 
                                            FSZip::getBaseDir( ),
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'GET',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        if ( isset( $result['status'] ) )
            $this->_app->response->setStatus( $result['status'] );

        if ( isset( $result['content'] ) )
            $this->_app->response->setBody( $result['content'] );

        if ( isset( $result['headers']['Content-Type'] ) )
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                $result['headers']['Content-Type']
                                                );
        $this->_app->response->headers->set( 
                                            'Content-Disposition',
                                            "attachment; filename=\"$filename\""
                                            );
        $this->_app->stop( );
    }

    /**
     * Returns the ZIP file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$hash.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getZipData( $hash )
    {
        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $filePath = FSZip::generateFilePath( 
                                            FSZip::getBaseDir( ),
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'INFO',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        if ( isset( $result['headers']['Content-Type'] ) )
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                $result['headers']['Content-Type']
                                                );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && 
             isset( $result['content'] ) ){
            $tempObject = File::decodeFile( $result['content'] );
            $tempObject->setAddress( FSZip::getBaseDir( ) . '/' . $hash );
            $this->_app->response->setStatus( $result['status'] );
            $this->_app->response->setBody( File::encodeFile( $tempObject ) );
            
        } else {
            $this->_app->response->setStatus( 409 );
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->stop( );
        }

        $this->_app->stop( );
    }

    /**
     * Deletes a ZIP file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /zip/$hash.
     *
     * @param string $hash The hash of the ZIP file which should be deleted.
     */
    public function deleteZip( $hash )
    {
        $links = FSZip::filterRelevantLinks( 
                                            $this->_fs,
                                            $hash
                                            );
        $filePath = FSZip::generateFilePath( 
                                            FSZip::getBaseDir( ),
                                            $hash
                                            );
        $result = Request::routeRequest( 
                                        'DELETE',
                                        '/' . $filePath,
                                        $this->_app->request->headers->all( ),
                                        '',
                                        $links,
                                        FSZip::getBaseDir( )
                                        );

        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && 
             isset( $result['content'] ) ){
            $tempObject = File::decodeFile( $result['content'] );
            $tempObject->setAddress( FSZip::getBaseDir( ) . '/' . $hash );
            $this->_app->response->setStatus( $result['status'] );
            $this->_app->response->setBody( File::encodeFile( $tempObject ) );
            
        } else {
            $this->_app->response->setStatus( 409 );
            $this->_app->response->setBody( File::encodeFile( new File( ) ) );
            $this->_app->stop( );
        }
        $this->_app->stop( );
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
     */
    public static function generatepath( $path )
    {
        $parts = explode( 
                         '/',
                         $path
                         );
        if ( count( $parts ) > 0 ){
            $path = $parts[0];
            for ( $i = 1;$i <= count( $parts );$i++ ){
                if ( !is_dir( $path ) )
                    mkdir( 
                          $path,
                          0755
                          );
                if ( $i < count( $parts ) )
                    $path .= '/' . $parts[$i];
            }
        }
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

