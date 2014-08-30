<?php 


/**
 * @file CConfig.php contains the CConfig class
 *
 * @author Till Uhlig
 * @date 2013-2014
 */
include_once ( dirname( __FILE__ ) . '/Structures.php' );

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

    /**
     * the CConfig constructor
     *
     * @param $prefix the prefix, the component works with
     */
    public function __construct( $prefix )
    {

        // initialize slim
        $this->setPrefix( $prefix );
        $this->_app = new \Slim\Slim( array('debug' => true) );

        $this->_app->response->headers->set( 
                                            'Content-Type',
                                            'application/json'
                                            );
                                            
        // GET Commands
        $this->_app->get( 
                          '(/:pre+)/info/commands(/)',
                          array( 
                                $this,
                                'commands'
                                )
                          );

        // GET Instruction
        $this->_app->get( 
                          '(/:pre+)/info/links(/)',
                          array( 
                                $this,
                                'instruction'
                                )
                          );   

        // GET Info
        $this->_app->get( 
                          '(/:pre+)/info/:language(/)',
                          array( 
                                $this,
                                'info'
                                )
                          );                                              

        // POST Config
        $this->_app->post( 
                          '(/:pre+)/control',
                          array( 
                                $this,
                                'postConfig'
                                )
                          );

        // GET Config
        $this->_app->get( 
                         '(/:pre+)/control',
                         array( 
                               $this,
                               'getConfig'
                               )
                         );

        // starts slim only if the right prefix was received
        if ( strpos($this->_app->request->getResourceUri( ),'/control') !== false  ||  strpos( 
                    $this->_app->request->getResourceUri( ),
                    '/info'
                    ) !== false ){

            // run Slim
            $this->_used = true;
            $this->_app->run( );
            
        }
    }
    
    public function info( $pre = array(), $language = 'de')
    {
        if (file_exists('info/'.$language)){
            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( file_get_contents('info/'.$language) );
        }else{
            $this->_app->response->setStatus( 404 );
            $this->_app->response->setBody( '' );
        }
    }
    
    public function instruction( $pre = array())
    {
        if (file_exists('Links.json')){
            $this->_app->response->setStatus( 200 );
            $this->_app->response->setBody( file_get_contents('Links.json') );
        }else{
            $this->_app->response->setStatus( 404 );
            $this->_app->response->setBody( '' );
        }
    }
    
    public function commands( $pre = array() )
    {
        if (file_exists('Commands.json')){
            $this->_app->response->setStatus( 200 );
            $commands = json_decode(file_get_contents('Commands.json'), true);
            $commands[] = array('method' => 'get', 'path' => '(/:pre+)/info/commands(/)');
            $commands[] = array('method' => 'get', 'path' => '(/:pre+)/info/links(/)');
            $commands[] = array('method' => 'get', 'path' => '(/:pre+)/info/:language(/)');
            $commands[] = array('method' => 'post', 'path' => '(/:pre+)/control');
            $commands[] = array('method' => 'get', 'path' => '(/:pre+)/control');
            $this->_app->response->setBody( json_encode($commands) );

        }else{
            $this->_app->response->setStatus( 404 );
            $this->_app->response->setBody( '' );
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
    public function postConfig( $pre = array() )
    {
        $tempPre = '';
        foreach($pre as $pr){
            if ($pr !== '')
                $tempPre .= $pr . '_';
        }
        $pre = $tempPre;
        
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
    public function getConfig( $pre = array()  )
    {
        $tempPre = '';
        foreach($pre as $pr){
            if ($pr !== '')
                $tempPre .= $pr . '_';
        }
        $pre = $tempPre;
        
        if ( file_exists( $pre . CConfig::$CONF_FILE ) ){
            $com = Component::decodeComponent( file_get_contents( $pre . CConfig::$CONF_FILE ) );
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
    public function saveConfig( $pre='', $content ){
        CConfig::saveConfigGlobal($pre,$content);
    }
     
    public static function saveConfigGlobal( $pre='', $content )
    {
        $file = fopen( 
                      $pre . CConfig::$CONF_FILE,
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
    public function loadConfig( )
    {
        $tempPre = '';
        $args = func_get_args();
        foreach($args as $n => $field){
            if ($field !== '')
                $tempPre .= $field. '_' ;
        }
        $pre = $tempPre;
        
        if ( file_exists( $pre . CConfig::$CONF_FILE ) ){

            // file was found, create a Component object from file content
            $com = Component::decodeComponent( file_get_contents( $pre . CConfig::$CONF_FILE ) );
            $com->setPrefix( $this->getPrefix( ) );
            
            // check if the links
            // know their prefixes
            $conf = $com;
            $links = $conf->getLinks( );

            // always been an array
            if ( !is_array( $links ) )
                $links = array( $links );

            $changed = false;
            foreach ( $links as & $link ){

                // if a link has no prefix, we have to ask the link target
                // for the prefix list
                if ( $link->getPrefix( ) === null ){
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
                    }
                }
            }

            // if any new prefix was found, we have to store the link definitions
            if ( $changed ){
                $conf->setLinks( $links );
                $this->saveConfig( $pre, Component::encodeComponent( $conf ) );
                $com = $conf;
            }
            
            return $com;
            
        } else {

            // can't find the file, create an empty object
            $com = new Component( );
            $com->setPrefix( $this->getPrefix( ) );
            return $com;
        }
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

 
?>