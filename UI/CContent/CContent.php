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

        $component = new Model('', dirname(__FILE__), $this, false, true, array('getContent'=>false));
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
        
        $cacheFolder = dirname(__FILE__).'/content/cache/'.implode('/',$params['path']);
        
        $realExtension = (isset($path_parts['extension']) ? ('.'.strtolower($path_parts['extension'])) : '');
        $params['path'][] = $path_parts['filename'].$realExtension;
        $contentPath = implode('/',$params['path']);
        $cacheExtension = $realExtension;
        
        $cachePath = 'cache/'.$contentPath;

        // überprüft, ob die Daten schon im Cache existieren und maximal 1 Tag (86400) alt sind.
        if ((!isset($this->config['SETTINGS']['developmentMode']) || $this->config['SETTINGS']['developmentMode'] !== '1') && file_exists(dirname(__FILE__).'/content/'.$cachePath) && filemtime(dirname(__FILE__).'/content/'.$cachePath) >= time() - 86400){ // temporär abgeschalten
            $preparedPath = $this->prepareFileForResponse($cachePath, $contentPath);
            //Model::header('Location',$this->config['MAIN']['externalUrl'].'/UI/CContent/content/'.$preparedPath);
            
            Model::header('Content-Length',filesize(dirname(__FILE__).'/content/'.$preparedPath));
            $mime = MimeReader::get_mime(dirname(__FILE__).'/content/'.$preparedPath, true);
            Model::header('Content-Type',$mime);
            return Model::isOk(file_get_contents(dirname(__FILE__).'/content/'.$preparedPath));
        }
        
        // jetzt soll geprüft werden, ob die Datei zu CContent gehört und sich im /content Ordner befindet
        $localPath = $contentPath;
        if (file_exists(dirname(__FILE__).'/content/'.$localPath)){
            //self::generatepath( dirname(dirname(__FILE__).'/content/'.$cachePath) );
            //file_put_contents(dirname(__FILE__).'/content/'.$cachePath, file_get_contents(dirname(__FILE__).'/content/'.$localPath));
            $preparedPath = $this->prepareFileForResponse($localPath, $contentPath);
            //Model::header('Location',$this->config['MAIN']['externalUrl'].'/UI/CContent/content/'.$preparedPath);
            
            Model::header('Content-Length',filesize(dirname(__FILE__).'/content/'.$preparedPath));
            $mime = MimeReader::get_mime(dirname(__FILE__).'/content/'.$preparedPath, true);
            Model::header('Content-Type',$mime);
            return Model::isOk(file_get_contents(dirname(__FILE__).'/content/'.$preparedPath));
        }
        
        $order = '/content/'.$contentPath;     
        
        $positive = function($input, $cachePath, $contentPath, $realExtension, $negativeMethod, $cacheFilename) {
            if (empty($input)){
                // wenn die zurückgegebene Datei leer ist, wird nicht gecached und die negative Methode aufgerufen
                return call_user_func_array($negativeMethod, array());
            }            
            
            // die Datei wird lokal gespeichert
            self::generatepath( dirname(dirname(__FILE__).'/content/'.$cachePath) );
            @file_put_contents(dirname(__FILE__).'/content/'.$cachePath,$input);
            
            $preparedPath = $this->prepareFileForResponse($cachePath, $contentPath);

            //Model::header('Location',$this->config['MAIN']['externalUrl'].'/UI/CContent/content/'.$preparedPath);
            if ($cachePath !== $preparedPath){
                $input = file_get_contents(dirname(__FILE__).'/content/'.$preparedPath);
            }
            
            Model::header('Content-Length',strlen($input));
            $mime = MimeReader::get_mime(dirname(__FILE__).'/content/'.$cachePath, true);
            Model::header('Content-Type',$mime);
            return Model::isOk($input);
        };
        
        $negative = function() {
            $input = '';
            Model::header('Content-Length',strlen($input));
            return Model::isProblem($input);
        };

        return $this->_component->callByURI('getContent', $order, array(), '', 200, $positive, array('cachePath'=>$cachePath, 'contentPath'=>$contentPath,'realExtension'=>$realExtension, 'negativeMethod'=>$negative, 'cacheFilename'=>$path_parts['filename'].$cacheExtension), $negative, array());
    }

    /**
     * prepares a local existing file.
     * for that the file extension is used to decide if a compression is required or not
     */
    private function prepareFileForResponse($localFilePath, $order){        
        $realLocalPath = dirname(__FILE__).'/content/'.$localFilePath;
        $path_parts = pathinfo($realLocalPath);
        $extension = (isset($path_parts['extension']) ? ('.'.strtolower($path_parts['extension'])) : '');
        
        $cacheFolder = dirname(__FILE__).'/content/cache/minified';
        $minifiedPath = $cacheFolder.'/'.$order;
        
        // wenn die Datei bereits lokal gecached wurde, dann müssen wir sie nicht nochmal verkleinern
        if (file_exists($minifiedPath) && filemtime($minifiedPath) >= time() - 86400){ // 1 Tag
            if (!isset($this->config['SETTINGS']['developmentMode']) || $this->config['SETTINGS']['developmentMode'] !== '1'){
                return 'cache/minified/'.$order;
            }
        }

        if ($extension == '.php'){
            ob_start();
            include($realLocalPath);
            $result = ob_get_clean();
            
            $newOrder = 'cached_'.substr($order, 0, -4);
            
            self::generatepath( dirname(dirname(__FILE__).'/content/cache/'.$newOrder) );
            file_put_contents(dirname(__FILE__).'/content/cache/'.$newOrder, $result);
            return 'cache/'.$newOrder;
        } elseif ($extension === '.js'){
            if (isset($this->config['SETTINGS']['developmentMode']) && $this->config['SETTINGS']['developmentMode'] === '1'){
                // die javascript-Datei soll nicht verkleinert werden
                return $localFilePath;
            }
        
            //return $localFilePath; // derzeit wird der Inhalt nicht verkleinert
            $minifiedContent = \PHPWee\Minify::js(file_get_contents($realLocalPath));
            if ($minifiedContent === ''){
                // bei der Umwandlung gab es einen Fehler
                return $localFilePath;
            }
            
            self::generatepath( dirname($minifiedPath) );
            file_put_contents($minifiedPath, $minifiedContent);
            return 'cache/minified/'.$order;
        } elseif ($extension === '.css'){
            if (isset($this->config['SETTINGS']['developmentMode']) && $this->config['SETTINGS']['developmentMode'] === '1'){
                // die css-Datei soll nicht verkleinert werden
                return $localFilePath;
            }
        
            //return $localFilePath; // derzeit wird der Inhalt nicht verkleinert
            $minifiedContent = \PHPWee\Minify::css(file_get_contents($realLocalPath));
            if ($minifiedContent === ''){
                // bei der Umwandlung gab es einen Fehler
                return $localFilePath;
            }
                       
            self::generatepath( dirname($minifiedPath) );
            file_put_contents($minifiedPath, $minifiedContent);
            return 'cache/minified/'.$order;
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
        self::deleteDir( dirname(__FILE__).'/content/cache' );
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
        self::deleteDir( dirname(__FILE__).'/content/cache' );
        
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
        
        if (isset($settings->developmentMode)){
            $text .= "[SETTINGS]\n";
            $text .= "developmentMode = \"".str_replace(array("\\","\""),array("\\\\","\\\""),str_replace("\\","/",$settings->developmentMode))."\"\n";
        }
                
        if (!@file_put_contents($file,$text)){
            Logger::Log( 
                        'POST AddPlatform failed, config.ini no access',
                        LogLevel::ERROR
                        );

            return Model::isProblem();
        }   

        
        self::generatepath( dirname(__FILE__).'/content/cache' );
        
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
