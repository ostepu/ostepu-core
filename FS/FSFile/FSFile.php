<?php 


/**
 * @file FSFile.php contains the FSFile class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example FS/FSFile/FileSample.json
 * @date 2013-2014
 */ 

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/MimeReader.php' );

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
        
        $component = new Model(self::getBaseDir(), dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Prepares the saving process by generating the hash and the place where the file is stored.
     *
     * Called when this component receives an HTTP POST request to
     * /$a/$b/$c/$file.
     * The request body should contain a JSON object representing the file's
     * attributes.
     */
    public function addFile( $callName, $input, $params = array() )
    {
        $fileObject = $input;
        $fileContent = $fileObject->getContent( );
        $fileObject->setBody( null );        
        
        $fileObject->setHash( sha1( $fileContent ) );
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
                       $fileContent
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

                return Model::isCreated($fileObject);
            }
        }

        // resets the file content
        $fileObject->setBody( null );

        // generate new file address, file size and file hash
        $fileObject->setAddress( $filePath );
        $fileObject->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
        $fileObject->setHash( sha1_file( $this->config['DIR']['files'].'/'.$filePath ) );
        $fileObject->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));
            
        return Model::isCreated($fileObject);
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
    public function getFileDocument( $callName, $input, $params = array() )
    {
        $path = array(self::getBaseDir(),$params['a'],$params['b'],$params['c'], $params['file']);

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
            Model::header('Content-Type','application/octet-stream');
            Model::header('Content-Disposition',"attachment; filename=\"".$params['filename']."\"");                                            
            readfile( $this->config['DIR']['files'].'/'.$filePath );
            return Model::isOk();
            
        }
        return Model::isProblem();
    }

    /**
     * Returns the file infos as a JSON file object.
     *
     * Called when this component receives an HTTP GET request to
     * /file/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the requested file.
     */
    public function getFileData( $callName, $input, $params = array() )
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
            $file->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));
            return Model::isOk($file);
            
        }
        return Model::isProblem(new File( ));
    }

    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$a/$b/$c/$file.
     *
     * @param string $hash The hash of the file which should be deleted.
     */
    public function deleteFile( $callName, $input, $params = array() )
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
            $file->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));

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

 