<?php
/**
 * @file CConfig.php contains the CConfig class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */

include_once ( dirname( __FILE__ ) . '/Structures.php' );

if (file_exists(dirname(__FILE__) . '/vendor/Slim/Slim/Slim.php')){
    include_once ( dirname(__FILE__) . '/vendor/Slim/Slim/Slim.php' );
}

if (in_array("Slim\\Slim", get_declared_classes()))
    \Slim\Slim::registerAutoloader( );

/**
 * this class is used to link components, to save new linkage data and to
 * retrieve linkage data
 */
class CConfig
{

    /**
     * @var $_app the slim object
     */
    private $_app;

    ///public static $possible = true;

    /**
     * @var $CONF_FILE the file where the component configuration would be stored
     */
    private static $CONF_FILE = 'CConfig.json';

    /**
     * @var $_prefix the prefix, the class works with
     */
    private $_prefix = '';

    /**
     * @var $_used to check whether the component configuration
     * has been addressed
     */
    private $_used = false;
    public $pre = null;
    public $confFile = null;
    public $callPath = null;
    public static $onload=false;

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public function getPrefix( )
    {
        return $this->_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */
    public function setPrefix( $value )
    {
        $this->_prefix = $value;
    }

    private $_noInfo = false;
    private $_noHelp = false;
    private $_defaultLanguage = null;

    /**
     * the CConfig constructor
     *
     * @param $prefix the prefix, the component works with
     */
    public function __construct( $prefix, $callPath = null, $noInfo = false, $noHelp = false, $defaultLanguage = 'de' )
    {
        if (!in_array('Slim\Slim', get_declared_classes())){
            return;
        }

        $this->_noInfo = $noInfo;
        $this->_noHelp = $noHelp;
        $this->_defaultLanguage = $defaultLanguage;

        // initialize slim
        $this->setPrefix( $prefix );

        $callPath = str_replace("\\",'/',$callPath);
        $this->callPath = $callPath;

        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = str_replace('?' . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''), '', substr_replace($requestUri, '', 0, strlen((strpos($requestUri, $scriptName) !== false ? $scriptName : str_replace('\\', '', dirname($scriptName))))));

        $pregA = @preg_match("%^(/[a-zA-Z0-9_\x7f-\xff]*)?/control$%", $path);
        $pregB = @preg_match("%^(/[a-zA-Z0-9_\x7f-\xff]*)?/info/commands(/?)$%", $path);
        $pregC = @preg_match("%^(/[a-zA-Z0-9_\x7f-\xff]*)?/info/links(/?)$%", $path);
        $pregD = @preg_match("%^(/[a-zA-Z0-9_\x7f-\xff]*)?/info/([a-zA-Z0-9_\x7f-\xff]*)(/?)$%", $path);
        $pregE = @preg_match("%^(/[a-zA-Z0-9_\x7f-\xff]*)?/help/([a-zA-Z0-9_\x7f-\xff]*)/%", $path);

        if ( $pregA || $pregB || $pregC || (!$noInfo && $pregD )  || (!$noHelp && $pregE ) ) {

            $this->_app = new \Slim\Slim( array('debug' => true) );

            $this->_app->response->headers->set(
                                                'Content-Type',
                                                'application/json'
                                                );

            // GET Commands
            $this->_app->map(
                              '(/:pre)/info/commands(/)',
                              array(
                                    $this,
                                    'commands'
                                    )
                              )->via('GET','OPTIONS');

            // GET Instruction
            $this->_app->get(
                              '(/:pre)/info/links(/)',
                              array(
                                    $this,
                                    'instruction'
                                    )
                              );

            if (!$this->_noInfo){
                // GET Info
                $this->_app->get(
                                  '(/:pre)/info/:language(/)',
                                  array(
                                        $this,
                                        'info'
                                        )
                                  );
            }

            // POST Config
            $this->_app->post(
                              '(/:pre)/control',
                              array(
                                    $this,
                                    'postConfig'
                                    )
                              );

            // GET Config
            $this->_app->get(
                             '(/:pre)/control',
                             array(
                                   $this,
                                   'getConfig'
                                   )
                             );

            if (!$this->_noHelp){
                // GET Help
                $this->_app->get(
                                 '(/:pre)/help/:language/:helpPath+',
                                 array(
                                       $this,
                                       'getHelp'
                                       )
                                 );
            }

        // run Slim
        $this->_used = true;
        $this->_app->run( );
        ///unset($this->_app);
        }
    }

    public function info( $pre = '', $language = 'de')
    {
        $path = ($this->callPath!=null ? $this->callPath.'/' : '');
        $path = str_replace("\\",'/',$path);

        if (file_exists($path.'info/'.$language.'.md')){
            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( file_get_contents($path.'info/'.$language.'.md') );
        }else{
            // wenn die gewollte Datei nicht existiert, suche wenigstens noch "de"
            if (file_exists($path.'info/de.md')){
                $this->_app->response->setStatus( 200 );
                $this->_app->response->setBody( file_get_contents($path.'info/de.md') );                
            } else {
                $this->_app->response->setStatus( 404 );
                $this->_app->response->setBody( '' );
            }
        }
    }

    public function getHelp( $pre = '', $language, $helpPath)
    {
        $path = ($this->callPath!=null ? $this->callPath.'/' : '');
        $path = str_replace("\\",'/',$path);
        $path .= 'help/';

        $fileName = array_pop($helpPath);
        $path_parts = pathinfo($fileName);
        $helpPath[] = $path_parts['filename'];
        $helpPath[] = $language;
        $extension = (isset($path_parts['extension']) ? ('.'.strtolower($path_parts['extension'])) : '');
        $helpPathString = implode('_',$helpPath).$extension;

        if (file_exists($path.$helpPathString)){
            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( file_get_contents($path.$helpPathString) );
        }else{
            array_pop($helpPath);
            $helpPath[] = $this->_defaultLanguage;
            $helpPathString = implode('_',$helpPath).$extension;

            if (file_exists($path.$helpPathString)){
                $this->_app->response->setStatus( 200 );
                $this->_app->response->setBody( file_get_contents($path.$helpPathString) );
            } else {
                $this->_app->response->setStatus( 404 );
                $this->_app->response->setBody( '' );
            }
        }
    }

    public function instruction( $pre ='', $returnData=false)
    {
        if ($pre != ''){
            $pre.='_';
        }

        $path = ($this->callPath!=null ? $this->callPath.'/' : '');
        $path = str_replace("\\",'/',$path);
        if ($this->callPath==null && $path!='') $this->callPath=$path;
        $this->confFile = $path . '/' . CConfig::$CONF_FILE;
        $conf = CConfig::loadStaticConfig($this->getPrefix(),$pre, $this->callPath);
        $defs = explode(";",$conf->getDef());
        $links = array();

        $found = false;
        foreach ($defs as $key => $value){
            if ($key%2 == 0) continue;
            if (file_exists($value)){
                $found=true;
                $content = json_decode(file_get_contents($value),true);

                if (isset($content['links'])){
                    $links = array_merge($links,$content['links']);
                }
            }
        }

        if ($returnData){
            if (!$found) return array();
            return $links;
        } else {
            if (!$found){
                $this->_app->response->setStatus( 404 );
                $this->_app->response->setBody( '' );
            } else {
                $this->_app->response->setBody( json_encode($links) );
            }
        }
    }

    public function commands( $pre = '', $nativeOnly=false, $returnData=false )
    {
        if (file_exists(($this->callPath!=null ? $this->callPath.'/':'').'Commands.json')){
            if (!$returnData)
                $this->_app->response->setStatus( 200 );

            $commands = json_decode(file_get_contents(($this->callPath!=null ? $this->callPath.'/':'').'Commands.json'), true);

            if (!$nativeOnly){
                $commands[] = array('method' => 'get', 'path' => '(/:pre)/info/commands(/)');
                $commands[] = array('method' => 'get', 'path' => '(/:pre)/info/links(/)');
                if (!$this->_noInfo) $commands[] = array('method' => 'get', 'path' => '(/:pre)/info/:language(/)');
                $commands[] = array('method' => 'post', 'path' => '(/:pre)/control');
                $commands[] = array('method' => 'get', 'path' => '(/:pre)/control');
                if (!$this->_noHelp) $commands[] = array('method' => 'get', 'path' => '(/:pre)/help/:language/path+');
            }

            if ($returnData){
                return $commands;
            } else
                $this->_app->response->setBody( json_encode($commands) );

        }else{
            if ($returnData){
                return array();
            } else {
                $this->_app->response->setStatus( 404 );
                $this->_app->response->setBody( '' );
            }
        }
    }

    /**
     * returns the value of $_used
     *
     * @return the value of $_used
     */
    public function used( )
    {
        return $this->_used;
    }

    /**
     * POST Config
     * - to store new component data
     */
    public function postConfig( $pre = '' )
    {
        if ($pre != ''){
            $pre.='_';
        }

        $this->_app->response->setStatus( 451 );
        $body = $this->_app->request->getBody( );
        $Component = Component::decodeComponent( $body );
        $Component->setPrefix( $this->getPrefix( ) );
        $this->saveConfig( $pre, Component::encodeComponent( $Component ) );
        $this->_app->response->setStatus( 201 );
    }

    /**
     * GET Config
     * - to ask this component for his component data
     */
    public function getConfig( $pre = '', $path = ''  )
    {
        if ($path=='' && $this->callPath!=null) $path = $this->callPath;
        if ($path!='') $path.='/';
        $path = str_replace("\\",'/',$path);

        if ($pre != ''){
            $pre.='_';
        }

        if ( file_exists( $path . $pre . CConfig::$CONF_FILE ) ){
            $com = Component::decodeComponent( file_get_contents( $path . $pre . CConfig::$CONF_FILE ) );

            if (file_exists($path . 'Component.json')){
                $def = Component::decodeComponent( file_get_contents( $path . 'Component.json' ) );
                $com->setClassFile( $def->getClassFile() );
                $com->setClassName( $def->getClassName() );
                if ($path!=""){
                    $myPath = str_replace("\\",'/',dirname(__FILE__));
                    $com->setLocalPath( substr(substr($path,0,-1),strrpos($myPath,'/')+1 ) );
                }
            }

            $com->setPrefix( $this->getPrefix( ) );
            $this->_app->response->setBody( Component::encodeComponent( $com ) );
            $this->_app->response->setStatus( 200 );

        } else {
            $this->_app->response->setStatus( 409 );
            $com = new Component( );
            $com->setPrefix( $this->getPrefix( ) );
            $this->_app->response->setBody( Component::encodeComponent( $com ) );
        }
    }

    /**
     * stores a Component object (json encoded) to the $CONF_FILE file
     *
     * @param $content the json encode Component object
     */
    public function saveConfig( $pre='', $content )
    {
        $path='';
        if ($this->callPath!=null) $path = $this->callPath;
        $path = str_replace("\\",'/',$path);

        CConfig::saveConfigGlobal($pre,$content,$path);
        $this->pre = substr($pre,0,-1);
    }

    public static function saveConfigGlobal( $pre='', $content, $path='', $file=null )
    {
        if ($path!='')$path.='/';
        $path = str_replace("\\",'/',$path);

        $ff = $path . $pre . CConfig::$CONF_FILE;
        if ($file!=null)
            $ff=$path.$file;

        $file = fopen(
                      $ff,
                      'w'
                      );
        fwrite(
               $file,
               $content
               );
        fclose( $file );
    }

    /**
     * the function loads the $CONF_FILE file
     *
     * @return a Component object
     */
    public function loadConfig2( $path = '' )
    {
        if ($this->callPath==null && $path!='') $this->callPath=$path;
        $this->confFile = $path . '/' . CConfig::$CONF_FILE;
        return CConfig::loadStaticConfig($this->getPrefix(), '', $this->callPath);
    }

    public static function loadStaticConfig( $pref, $pre, $path='', $file=null )
    {
        CConfig::$onload = true;
        if ($path!='') $path.='/';
        $path = str_replace("\\",'/',$path);

        $ff = $path . $pre . CConfig::$CONF_FILE;
        if ($file!=null) $ff=$path . $file;
        if ( file_exists( $ff ) ){

            // file was found, create a Component object from file content
            $com = Component::decodeComponent( file_get_contents( $ff ) );
            $com->setPrefix( $pref );

            // check if the links
            // know their prefixes
            $conf = $com;
            $links = $conf->getLinks( );

            // always been an array
            if ( !is_array( $links ) )
                $links = array( $links );

            $changed = false;
            $possibleLinks = array();
            $failed=false;
            foreach ( $links as &$link ){

                // if a link has no prefix, we have to ask the link target
                // for the prefix list
                if ( $link->getPrefix( ) === null ){
                    if ($failed === true){
                        continue;
                    }

                    $result = Request::get(
                                           $link->getAddress( ) . '/control',
                                           array( ),
                                           ''
                                           );

                    if ( $result['status'] == 200 ){

                        // the link target has send its component definition,
                        // so that we can remember this
                        $changed = true;
                        $obj = Component::decodeComponent( $result['content'] );
                        $link->setPrefix( $obj->getPrefix( ) );
                        $link->setClassFile( $obj->getClassFile( ) );
                        $link->setClassName( $obj->getClassName( ) );
                        $link->setLocalPath( $obj->getLocalPath() );
                    } else {
                        $failed = true;
                        continue;
                    }
                }

                $possibleLinks[] = $link;
            }

            // if any new prefix was found, we have to store the link definitions
            if ( $changed ){
                $conf->setLinks( $links );
                CConfig::saveConfigGlobal( $pre, Component::encodeComponent( $conf ), substr($path,0,-1),$file );
                $com = $conf;
            }

            $com->setLinks( $possibleLinks );

            CConfig::$onload=false;
            return $com;

        } else {

            // can't find the file, create an empty object
            $com = new Component( );
            $com->setPrefix( $pref );
            CConfig::$onload=false;
            return $com;
        }
        CConfig::$onload=false;
    }

    public function loadConfig( $pre = '')
    {
        if ($pre != ''){
            $pre.='_';
        }

        $path ='';
        if ($this->callPath!=null) $path=$this->callPath;

        $this->confFile = $path . $pre . CConfig::$CONF_FILE;
        return CConfig::loadStaticConfig($this->getPrefix(), $pre, $this->callPath);
    }

    /**
     * to get a link from a link list with a specified name
     *
     * @param $linkList an array of links
     * @param $name the name of the searched link
     *
     * @return a link object, with the name $name
     */
    public static function getLink(
                                   $linkList,
                                   $name
                                   )
    {
        foreach ( $linkList as $link ){

            // search a link with the name $name
            if ( $link->getName( ) == $name )
                return $link;
        }

        return null;
    }

    /**
     * to get links from a link list with a specified name
     *
     * @param $linkList an array of links
     * @param $name the name of the searched links
     *
     * @return a array of link objects, which are named $name
     */
    public static function getLinks(
                                    $linkList,
                                    $name
                                    )
    {
        // wenn NULL übergeben wird, soll es keinen Absturz geben
        if (!isset($linkList)) $linkList = array();

        $result = array( );
        foreach ( $linkList as $link ){

            // return only links, which name is $name
            if ( $link->getName( ) == $name )
                $result[] = $link;
        }

        return $result;
    }

    /**
     * deletes a link ($linkName) from a link list ($links)
     *
     * @param $links an array of Links
     * @param $linkName a link name (String)
     *
     * @return the link list without these links
     */
    public static function deleteFromArray(
                                           $links,
                                           $linkName
                                           )
    {
        $result = array( );
        foreach ( $links as $link ){

            // add only links to the new list, which does not named $linkName
            if ( $link->getName( ) != $linkName ){
                $result[] = $link;
            }
        }
        return $result;
    }
}

