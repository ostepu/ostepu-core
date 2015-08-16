<?php
/**
 * @file CHelp.php Contains the CHelp class
 *
 * @author Till Uhlig
 * @date 2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/php-markdown-lib/Michelf/MarkdownInterface.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/php-markdown-lib/Michelf/Markdown.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/php-markdown-lib/Michelf/MarkdownExtra.php' );

/**
 * ???
 */
class CHelp
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

    public function umlaute($text){ 
        $search  = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß');
        $replace = array('&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', '&szlig;');
        return str_replace($search, $replace, $text);;
    }

    public function getHelp($callName, $input, $params = array())
    {
        if (!isset($this->config['MAIN']['externalUrl'])){
            return Model::isProblem();
        }
        
        array_unshift($params['path'],$params['language']);
        $fileName = array_pop($params['path']);
        $path_parts = pathinfo($fileName);
        
        $cacheFolder = dirname(__FILE__).'/cache/'.implode('/',$params['path']);
        self::generatepath( $cacheFolder );
        
        $realExtension = (isset($path_parts['extension']) ? ('.'.strtolower($path_parts['extension'])) : '');
        $params['path'][] = $path_parts['filename'].$realExtension;
        $cacheExtension = $realExtension;
        if ($cacheExtension == '.md'){
            $cacheExtension = '.html';
        }
        
        $cachePath = dirname(__FILE__).'/cache/'.implode('/',$params['path']).$cacheExtension;
        if (file_exists($cachePath)){
            return Model::isOk(file_get_contents($cachePath));
        }
        
        $order = implode('/',$params['path']);
        $order = '/help/'.$order;
        
        
        $positive = function($input, $cachePath, $realExtension) {
            if ($realExtension == '.md'){
                $parser = new \Michelf\MarkdownExtra;
                $input = $this->umlaute($input);
                $my_html = $parser->transform($input);
                $input = '<link rel="stylesheet" href="'.$this->config['MAIN']['externalUrl'].'/UI/css/github-markdown.css" type="text/css"><span class="markdown-body">'.$my_html.'</span>';
            }
            
            @file_put_contents($cachePath,$input);
            return Model::isOk($input);
        };
        return $this->_component->callByURI('request', $order, array('language'=>$params['language']), '', 200, $positive, array('cachePath'=>$cachePath, 'realExtension'=>$realExtension), 'Model::isEmpty', array());
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
