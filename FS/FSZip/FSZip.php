<?php


/**
 * @file FSZip.php contains the FSZip class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

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
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    private $_component = null;
    private $config = array();
    public function __construct( )
    {
        if (file_exists(dirname(__FILE__).'/config.ini')){
            $this->config = parse_ini_file(
                                           dirname(__FILE__).'/config.ini',
                                           TRUE
                                           );
        }
       
        $component = new Model('zip', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Creates a ZIP file consisting of the request body and permanently
     * stores it in the file system.
     *
     * Called when this component receives an HTTP POST request to /zip.
     * The request body should contain an array of JSON objects representing the files
     * which should be zipped and stored.
     */
    public function addZipPermanent( $callName, $input, $params = array() )
    {
        // generate sha1 hash for the zip, we have to create
        // (the name and the zip-hash are not the same)
        $hashArray = array( );
        foreach ( $input as $part ){
            if ( $part->getBody( ) !== null ){
                $hashArray[] = base64_encode($part->getBody( true ));
               
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
                                            self::getBaseDir( ),
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
                foreach ( $input as &$part ){
                    if ( $part->getBody( ) !== null ){
                        $zip->addFromString(
                                            $part->getDisplayName( ),
                                            $part->getBody( true )
                                            );
                       
                    } else {
                       
                        $file = $this->config['DIR']['files']. '/' . $part->getAddress( );
                        if (file_exists($file)){
                            $zip->addFromString(
                                                $part->getDisplayName( ),
                                                file_get_contents($file)
                                                );
                        } else {
                            $zip->close( );
                            unlink( $filePath );
                            return Model::isProblem(new File());
                        }
                    }
                    //unset($part);
                }
                $zip->close( );
            } else {
                unlink( $filePath );
                return Model::isProblem(new File());
            }
        }

        if (isset($params['filename'])){
            Model::header('Content-Type','application/zip');
            Model::header('Content-Disposition',"filename=\"".$params['filename']."\"");
            Model::header('Content-Length',filesize($this->config['DIR']['files'].'/'.$filePath));
            Model::header('Accept-Ranges','none');
            return Model::isCreated(file_get_contents($this->config['DIR']['files'].'/'.$filePath));
        } else {
            $zipFile = new File( );
            $zipFile->setHash( $hash );
            $zipFile->setAddress( $filePath );
            $zipFile->setMimeType("application/zip");
           
            if (file_exists($this->config['DIR']['files'].'/'.$filePath)){
                $zipFile->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
            }
            return Model::isCreated($zipFile);
        }
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
    public function getZipDocument( $callName, $input, $params = array() )
    {

        $path = array(self::getBaseDir( ),$a,$b,$c,$file);

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
            Model::header('Content-Type','application/zip');
            Model::header('Content-Disposition',"filename=\"".$params['filename']."\""); 
            Model::header('Accept-Ranges','none');
            return Model::isOk(file_get_contents($this->config['DIR']['files'].'/'.$filePath));
           
        }
        return Model::isProblem();
    }

    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /zip/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getZipData( $callName, $input, $params = array() )
    {
        $path = array(self::getBaseDir(),$params['a'],$params['b'],$params['c'], $params['file']);

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
            $file->setMimeType("application/zip");
            return Model::isOk($file);
           
        }
        return Model::isProblem(new File( ));
    }

    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /zip/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deleteZip( $callName, $input, $params = array() )
    {
        $path = array(self::getBaseDir(),$params['a'],$params['b'],$params['c'], $params['file']);

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
            $file->setMimeType("application/zip");

            // removes the file
            unlink( $this->config['DIR']['files'] . '/' . $filePath );

            // the removing/unlink process failed, if the file still exists.
            if ( file_exists( $this->config['DIR']['files'] . '/' . $filePath ) ){
            return Model::isProblem(new File( ));
            }

            // the file is removed
            return Model::isCreated($file);
           
        } else {

            // file does not exist
            return Model::isProblem(new File( ));
        }
    }
   
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts GET GetExistsPlatform',
                    LogLevel::DEBUG
                    );
                   
        if (!file_exists(dirname(__FILE__).'/config.ini')){
            return Model::isProblem();
        }
      
        return Model::isOk();
    }
   
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );
        if (file_exists(dirname(__FILE__).'/config.ini') && !unlink(dirname(__FILE__).'/config.ini')){
            return Model::isProblem();
        }
       
        return Model::isCreated();
    }
   
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );
       
        $file = dirname(__FILE__).'/config.ini';
        $text = "[DIR]\n".
                "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getTempDirectory()))."\"\n".
                "files = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getFilesDirectory()))."\"\n";
               
        if (!@file_put_contents($file,$text)){
            Logger::Log(
                        'POST AddPlatform failed, config.ini no access',
                        LogLevel::ERROR
                        );

            return Model::isProblem();
        }  

        $platform = new Platform();
        $platform->setStatus(201);
       
        return Model::isCreated($platform);
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

 