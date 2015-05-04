<?php 


/**
 * @file FSCsv.php contains the FSCsv class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class for creating, storing and loading CSV files from the file system
 */
class FSCsv
{

    /**
     * @var string $_baseDir the name of the folder where the csv files should be
     * stored in the file system
     */
    private static $_baseDir = 'csv';

    /**
     * the string $_baseDir getter
     *
     * @return the value of $_baseDir
     */
    public static function getBaseDir( )
    {
        return FSCsv::$_baseDir;
    }

    /**
     * the $_baseDir setter
     *
     * @param string $value the new value for $_baseDir
     */
    public static function setBaseDir( $value )
    {
        FSCsv::$_baseDir = $value;
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
        $com = new CConfig( FSCsv::getBaseDir( ), dirname(__FILE__) );

        // runs the FSCsv
        if ( $com->used( ) ) return;
            ///$_conf = $com->loadConfig( );

        if (file_exists(dirname(__FILE__).'/config.ini'))
            $this->config = parse_ini_file( 
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           ); 
                                       
        // initialize component
        ///$this->_conf = $_conf;

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
                         
        // POST PostCsvPermanent
        $this->_app->post( 
                          '/:folder(/)',
                          array( 
                                $this,
                                'postCsv'
                                )
                          );
                          
        // POST PostCsvTemporary
        $this->_app->post( 
                          '/:folder/:filename(/)',
                          array( 
                                $this,
                                'postCsv'
                                )
                          );

        // GET GetCsvData
        $this->_app->get( 
                         '/:folder/:a/:b/:c/:file(/)',
                         array( 
                               $this,
                               'getCsvData'
                               )
                         );

        // GET GetCsvDocument
        $this->_app->get( 
                         '/:folder/:a/:b/:c/:file/:filename(/)',
                         array( 
                               $this,
                               'getCsvDocument'
                               )
                         );

        // DELETE DeleteCsv
        $this->_app->delete( 
                            '/:folder/:a/:b/:c/:file(/)',
                            array( 
                                  $this,
                                  'deleteCsv'
                                  )
                            );

        // run Slim
        $this->_app->run( );
    }

    /**
     * Creates a Csv file consisting of the request body and then returns it.
     *
     * Called when this component receives an HTTP POST request to
     * /Csv/$orientation/temporary/$filename.
     * The request body should contain a JSON object representing the Csv file.
     *
     * @param string $orientation The orientation of the Csv file, e.g. portrait or landscape.
     * @param string $filename A freely chosen filename of the Csv file which should be stored.
     * @see http://wiki.spipu.net/doku.php
     */
    public function postCsv(
                            $folder,
                            $filename = null
                            )
    {
        $body = $this->_app->request->getBody( );
        $data = Csv::decodeCsv($body);
        $name = sha1( $body );
        unset($body);
        
                // generate Csv
        $filePath = FSCsv::generateFilePath( 
                                            $folder,
                                            $name
                                            );
                                           
        if (!file_exists( $this->config['DIR']['files'].'/'.$filePath ) ){
            FSCsv::generatepath( $this->config['DIR']['files'].'/'.dirname( $filePath ) );
            
            $result = $this->createCsv($data);

            // writes the file to filesystem
            $file = fopen(
                          $this->config['DIR']['files'].'/'.$filePath,
                          'w'
                          );

            if ($file){
                fwrite( 
                       $file,
                       $result
                       );
                fclose( $file );
              
            }else{
                $fileObject = new File( );
                $fileObject->addMessage("Datei konnte nicht im Dateisystem angelegt werden.");
                $fileObject->setStatus(409);
                Logger::Log( 
                        'POST postCsv failed',
                        LogLevel::ERROR
                        );
                $this->_app->response->setStatus( 409 );
                $this->_app->response->setBody( File::encodeFile( $fileObject ) );
                $this->_app->stop();
            }
        }        
                               
        $this->_app->response->setStatus( 201 );
        if ($filename!=null){
            if (isset($result)){
                $this->_app->response->setBody($result);
            } else 
                readfile($this->config['DIR']['files'].'/'.$filePath);
            
            $this->_app->response->headers->set( 
                                                'Content-Type',
                                                'application/octet-stream'
                                                );
            $this->_app->response->headers->set( 
                                                'Content-Disposition',
                                                "attachment; filename=\"$filename\""
                                                );
        } else {
            $CsvFile = new File( );
            $CsvFile->setStatus(201);
            $CsvFile->setAddress( $filePath );
            $CsvFile->setMimeType("application/Csv");
            
            if (file_exists($this->config['DIR']['files'].'/'.$filePath)){
                $CsvFile->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
                $hash = sha1(file_get_contents($this->config['DIR']['files'].'/'.$filePath));   
                $CsvFile->setHash( $hash );
            }
            $this->_app->response->setBody( File::encodeFile($CsvFile) );
        }
    }

    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /Csv/$a/$b/$c/$file/$filename.
     *
     * @param string $hash The hash of the file which should be returned.
     * @param string $filename A freely chosen filename of the returned file.
     */
    public function getCsvDocument( $folder,
                                    $a, $b, $c, $file,
                                    $filename
                                    )
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
    public function getCsvData( $folder,$a, $b, $c, $file )
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
            $file->setMimeType("application/Csv");
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
    public function deleteCsv( $folder,$a, $b, $c, $file )
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
            $file->setMimeType("application/Csv");

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
                
            }elseif ( FSCsv::isRelevant( 
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
    
    public function createCsv($data)
    {
        if ($data->getRows()==null) return null;
        
        $tempDir = $this->tempdir($this->config['DIR']['temp'], 'createCSV', $mode=0775);
        $path = $tempDir.'/maxmemory';
        $csv = fopen($path.':'. (5*1024*1024), 'w+');

        foreach($data->getRows() as $row){
            fputcsv($csv, $row, ';','"');
        }
        
        rewind($csv);

        // put it all in a variable
        $output = stream_get_contents($csv);
        fclose($csv);
        unlink($path);
        $this->deleteDir(dirname($path));
        return $output;
    }
    
   /**
    * Delete hole directory inclusiv files and dirs
    *
    * @param string $path
    * @return boolean
    */
    public function deleteDir($path)
    {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                $this->deleteDir(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        }

        // Datei entfernen
        else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
    
    public function tempdir($dir, $prefix='', $mode=0775)
    {
        if (substr($dir, -1) != '/') $dir .= '/';

        do
        {
            $path = $dir.$prefix.mt_rand(0, 9999999);
        } while (!mkdir($path, $mode));

        return $path;
    }
}

 