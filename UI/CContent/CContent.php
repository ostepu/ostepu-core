<?php
include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/vendor/Markdown/Michelf/MarkdownInterface.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/vendor/Markdown/Michelf/Markdown.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/vendor/Markdown/Michelf/MarkdownExtra.php' );
include_once ( dirname(__FILE__) . '/phpwee/phpwee.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/MimeReader.php' );

/**
 * ???
 */
class CContent
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
        
        $component = new Model('', dirname(__FILE__), $this, false, true);
        $this->_component=$component;
        $component->run();
    }

    public function getContent($callName, $input, $params = array())
    {
        if (!isset($this->config['MAIN']['externalUrl'])){
            return Model::isProblem();
        }
        
        $fileName = array_pop($params['path']);
        $path_parts = pathinfo($fileName);
        
        $cacheFolder = dirname(__FILE__).'/cache/'.implode('/',$params['path']);
        self::generatepath( $cacheFolder );
        
        $realExtension = (isset($path_parts['extension']) ? ('.'.strtolower($path_parts['extension'])) : '');
        $params['path'][] = $path_parts['filename'].$realExtension;
        $cacheExtension = $realExtension;
        
        $cachePath = dirname(__FILE__).'/cache/'.implode('/',$params['path']);
        //Überprüft ob die Daten schon im Cache existieren und maximal 1 Woche (604800 Sekunden) alt sind.
        if (file_exists($cachePath) && filemtime($cachePath) >= time() - 604800){
            Model::header('Content-Length',filesize($cachePath));
            $mime = MimeReader::get_mime($cachePath);
            Model::header('Content-Type',$mime);
            
            $preparedPath = $this->prepareFileForResponse($cachePath);
            return Model::isOk(file_get_contents($preparedPath));
        }
        
        $localPath = dirname(__FILE__).'/content/'.implode('/',$params['path']);
        if (file_exists($localPath)){
            Model::header('Content-Length',filesize($localPath));
            $mime = MimeReader::get_mime($localPath);
            Model::header('Content-Type',$mime);
            
            $preparedPath = $this->prepareFileForResponse($localPath);
            return Model::isOk(file_get_contents($preparedPath));
        }
        
        $order = implode('/',$params['path']);
        $order = '/content/'.$order;      
        
        $positive = function($input, $cachePath, $realExtension, $negativeMethod, $cacheFilename) {
            if (empty($input)){
                // wenn die zurückgegebene Datei leer ist, wird nicht gecached und die negative Methode aufgerufen
                return call_user_func_array($negativeMethod, array());
            }            
            
            Model::header('Content-Length',strlen($input));
            
            // die Hilfedatei wird lokal gespeichert
            @file_put_contents($cachePath,$input);
            
            $mime = MimeReader::get_mime($cachePath);
            Model::header('Content-Type',$mime);
            return Model::isOk($input);
        };
        
        $negative = function() {
            $input = '';
            Model::header('Content-Length',strlen($input));
            return Model::isProblem($input);
        };
        
        return $this->_component->callByURI('request', $order, array(), '', 200, $positive, array('cachePath'=>$cachePath, 'realExtension'=>$realExtension, 'negativeMethod'=>$negative, 'cacheFilename'=>$path_parts['filename'].$cacheExtension), $negative, array());
    }

    /**
     * prepares a local existing file.
     * for that the file extension is used to decide if a compression is required or not
     */
    private function prepareFileForResponse($localFilePath){
        $path_parts = pathinfo($localFilePath);
        $extension = (isset($path_parts['extension']) ? ('.'.strtolower($path_parts['extension'])) : '');
        
        $hashFilePath = sha1($localFilePath);
        $cacheFolder = dirname(__FILE__).'/cache/minified';
        $minifiedPath = $cacheFolder.'/'.$hashFilePath;
        if (file_exists($minifiedPath) && filemtime($minifiedPath) >= time() - 604800){
            return $minifiedPath;
        }

        if ($extension == '.js'){
            $minifiedContent = \PHPWee\Minify::js(file_get_contents($localFilePath));
            if ($minifiedContent === ''){
                // bei der Umwandlung gab es einen Fehler
                return $localFilePath;
            }
            
            self::generatepath( $cacheFolder );
            file_put_contents($minifiedPath, $minifiedContent);
            return $minifiedPath;
        } elseif ($extension == '.css'){
            $minifiedContent = \PHPWee\Minify::css(file_get_contents($localFilePath));
            if ($minifiedContent === ''){
                // bei der Umwandlung gab es einen Fehler
                return $localFilePath;
            }
                       
            self::generatepath( $cacheFolder );
            file_put_contents($minifiedPath, $minifiedContent);
            return $minifiedPath;
        }
        return $localFilePath;
    }
    
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $callName, $input, $params = array())
    {
        self::deleteDir( dirname(__FILE__).'/cache' );
        return Model::isCreated();
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array())
    {
        self::deleteDir( dirname(__FILE__).'/cache' );
        
        $file = dirname(__FILE__).'/config.ini';
        $text = "[DIR]\n".
                "temp = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getTempDirectory()))."\"\n".
                "files = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getFilesDirectory()))."\"\n".
                "[MAIN]\n".
                "externalUrl = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$input->getExternalUrl()))."\"\n";
                
        $settings = $input->getSettings();
        if (isset($settings->contactUrl)){
            $text .= "[HELP]\n";
            $text .= "contactUrl = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$settings->contactUrl))."\"\n";
        }
                
        if (!@file_put_contents($file,$text)){
            Logger::Log( 
                        'POST AddPlatform failed, config.ini no access',
                        LogLevel::ERROR
                        );

            return Model::isProblem();
        }   

        
        self::generatepath( dirname(__FILE__).'/cache' );
        
        $platform = new Platform();
        $platform->setStatus(201);
        
        return Model::isCreated($platform);
    }
    
    public function getExistsPlatform( $callName, $input, $params = array())
    {
        return Model::isOk(new Platform());
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
    
    public static function deleteDir($path)
    {
        // entfernt einen Ordner und zuvor alle enthaltenen Dateien
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) {
                self::deleteDir(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        }

        // Datei entfernen
        else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
}
