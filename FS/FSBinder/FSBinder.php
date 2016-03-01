<?php 


/**
 * @file FSBinder.php contains the FSBinder class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @date 2013-2014
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/MimeReader.php' );

/**
 * The class for storing files.
 */
class FSBinder
{

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
        
        $component = new Model('', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
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
    public function addFile( $callName, $input, $params = array() )
    {
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);
        $fileobject = $input;

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
                       $fileobject->getBody( true )
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
                    
                return Model::isProblem( $fileobject );
            }
        }

        // resets the file content
        $fileobject->setBody( null );

        // generate new file address, file size and file hash
        $fileobject->setAddress( $filePath );
        $fileobject->setFileSize( filesize( $this->config['DIR']['files'].'/'.$filePath ) );
        $fileobject->setHash( sha1_file( $this->config['DIR']['files'].'/'.$filePath ) );
        $fileobject->setMimeType(MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath));

        return Model::isCreated( $fileobject );
    }

    /**
     * Returns a file.
     *
     * Called when this component receives an HTTP GET request to
     * /$folder/$a/$b/$c/$file/$filename.
     *
     * @param string[] $path The path where the requested file is stored.
     */
    public function getFileDocument( $callName, $input, $params = array() )
    {
    	set_time_limit(600);
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);
        
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
            $mime = MimeReader::get_mime($this->config['DIR']['files'].'/'.$filePath);
            Model::header('Content-Type',$mime);
            Model::header('Content-Disposition',"filename=\"".$params['filename']."\"");
            Model::header('Content-Length',filesize($this->config['DIR']['files'].'/'.$filePath));
            Model::header('Accept-Ranges','none');
            readfile( $this->config['DIR']['files'].'/'.$filePath );
            return Model::isOk();
            
        } else {
            return Model::isProblem();
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
    public function getFiledata( $callName, $input, $params = array() )
    {
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);
        
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
            
        } else {
            return Model::isProblem(new File( ));
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
    public function deleteFile( $callName, $input, $params = array() )
    {
        $path = array($params['folder'],$params['a'],$params['b'],$params['c'], $params['file']);
        
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

 