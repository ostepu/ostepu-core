<?php
/**
 * @file CHelp.php Contains the CHelp class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 * @author Max Brauer <ma.brauer@student.uni-halle.de>
 * @date 2016
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/vendor/Markdown/Michelf/MarkdownInterface.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/vendor/Markdown/Michelf/Markdown.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/vendor/Markdown/Michelf/MarkdownExtra.php' );

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
    
    public function getSystemStatus($callName, $input, $params = array())
    {
        if (!isset($this->config['MAIN']['externalUrl'])){
            return Model::isProblem();
        }
        
        $positive = function($input) {
            $input = '<html><head></head><body><span style="color:green">Anfrage erfolgreich</span></body></html>';
            Model::header('Content-Length',strlen($input));
            return Model::isOk($input);
        };
        
        $negative = function() {
            $input = '<html><head></head><body><span style="color:red">Anfrage nicht erfolgreich</span></body></html>';
            Model::header('Content-Length',strlen($input));
            return array_merge(Model::isProblem($input),array('statusText'=>'Unable to connect to the database.'));
        };
        
        return $this->_component->call('getAlive', array(), '', 200, $positive, array(), $negative, array());
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
        //Überprüft ob die Daten schon im Cache existieren und maximal 1 Woche (604800 Sekunden) alt sind.
        if ((!isset($this->config['SETTINGS']['developmentMode']) || $this->config['SETTINGS']['developmentMode'] !== '1') && file_exists($cachePath) && filemtime($cachePath) >= time() - 604800){
            Model::header('Content-Length',filesize($cachePath));
            return Model::isOk(file_get_contents($cachePath));
        }
        
        $order = implode('/',$params['path']);
        $order = '/help/'.$order;      
        
        $positive = function($input, $cachePath, $realExtension, $negativeMethod, $cacheFilename) {
            if (empty($input)){
                // wenn die zurückgegebene Datei leer ist, wird nicht gecached und die negative Methode aufgerufen
                return call_user_func_array($negativeMethod, array());
            }
            
            if ($realExtension == '.md'){
                $parser = new \Michelf\MarkdownExtra;
                $input = $this->umlaute($input);
                $my_html = $parser->transform($input);
                $contact = isset($this->config['HELP']['contactUrl']) ? $this->config['HELP']['contactUrl'] : null;
                if (isset($contact) && trim($contact) !== ''){
                    $contact = '<a href="'.$contact.'">Kontakt</a>';
                }
                
                $input = '<html><head></head><body><link rel="stylesheet" href="'.$this->config['MAIN']['externalUrl'].'/UI/CContent/content/common/css/github-markdown.css" type="text/css"><span class="markdown-body">'.$my_html.$contact.'</span></body></html>';
            }
            
            
            Model::header('Content-Length',strlen($input));
            
            // die Hilfedatei wird lokal gespeichert
            @file_put_contents($cachePath,$input);
            chmod($cachePath, 0774);
            return Model::isOk($input);
        };
        
        $negative = function() {
            $input = '#### !!!Kein Inhalt!!!';
            $parser = new \Michelf\MarkdownExtra;
            $input = $this->umlaute($input);
            $my_html = $parser->transform($input);
            $input = '<html><head></head><body><link rel="stylesheet" href="'.$this->config['MAIN']['externalUrl'].'/UI/CContent/content/common/css/github-markdown.css" type="text/css"><span class="markdown-body">'.$my_html.'</span></body></html>';

            Model::header('Content-Length',strlen($input));
            return Model::isOk($input);
        };
        
        return $this->_component->callByURI('request', $order, array('language'=>$params['language']), '', 200, $positive, array('cachePath'=>$cachePath, 'realExtension'=>$realExtension, 'negativeMethod'=>$negative, 'cacheFilename'=>$path_parts['filename'].$cacheExtension), $negative, array());
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

    public function getApiProfiles( $callName, $input, $params = array() )
    {   
        $myName = $this->_component->_conf->getName();
        $profiles = array();
        $profiles['readonly'] = GateProfile::createGateProfile(null,'readonly');
        $profiles['readonly']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /help/:path+',null));
        $profiles['readonly']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /status',null));
        
        $profiles['general'] = GateProfile::createGateProfile(null,'general');
        $profiles['general']->setRules($profiles['readonly']->getRules());
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /platform/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /platform/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /link/exists/platform',null));
        
        $profiles['develop'] = GateProfile::createGateProfile(null,'develop');
        $profiles['develop']->setRules(array_merge($profiles['general']->getRules(), $this->_component->_com->apiRulesDevelop($myName)));

        $profiles['public'] = GateProfile::createGateProfile(null,'public');
        $profiles['public']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /help/:path+',null));
        $profiles['public']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /status',null));
        return Model::isOk(array_values($profiles));
    }
}
