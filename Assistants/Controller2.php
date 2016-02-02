<?php


/**
 * @file Controller2.php contains the Controller2 class
 *
 * @author Till Uhlig
 * @date 2014
 */

require_once ( dirname( __FILE__ ) . '/vendor/Slim/Slim/Slim.php' );
include_once ( dirname( __FILE__ ) . '/Structures.php' );
include_once ( dirname( __FILE__ ) . '/Request.php' );

\Slim\Slim::registerAutoloader( );

/**
 * the Controller class represents a component, which routes incoming rest
 * requests to relevant components
 */
class Controller2
{
    public static function UrlAnd($textA, $textB){
        $textASplitted = explode('/',$textA);
        $textBSplitted = explode('/',$textB);

        if (count($textBSplitted) > 0 && $textBSplitted[0] == ''){
            unset($textBSplitted[0]);
            $textBSplitted = array_values($textBSplitted);
        }

        if (count($textASplitted) === 0 || count($textBSplitted) === 0)
            return null;

        $i = count($textASplitted)-count($textBSplitted);
        if ($i<0)
            $i=0;

        for (; $i<count($textASplitted); $i++)
            for ($c=$i,$textLength=0;$c<count($textASplitted) && $textBSplitted[$c-$i] === $textASplitted[$c];$c++){
                $textLength+=strlen($textBSplitted[$c-$i])+1;
                if ($c+1 == count($textASplitted))
                    return $textLength;
            }
        return null;
    }

    /**
     * @var $_prefix the prefix, the class works with
     */
    protected static $_prefix = '';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return Controller2::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        Controller2::$_prefix = $value;
    }

    /**
     * @var $_app the slim object
     */
    protected $_app;

    /**
     * @var $_conf the component data object
     */
    protected $_conf = null;

    /**
     * the component constructor
     *
     * @param $conf component data
     */
    public function __construct( $_conf )
    {

        // initialize component
        $this->_conf = $_conf;

        // initialize slim
        $this->_app = new \Slim\Slim( );
        $this->_app->map(
                         '/:data+',
                         array(
                               $this,
                               'getl'
                               )
                         )->via(
                                'GET',
                                'POST',
                                'DELETE',
                                'PUT',
                                'INFO'
                                );

        // run Slim
        $this->_app->run( );
    }

    /**
     * the getl function uses a list of links to find a
     * relevant component for the $data request
     *
     * @param $data a slim generated array of URI segments (String[])
     */
    public function getl( $data )
    {
        Logger::Log(
                    'starts Controller routing',
                    LogLevel::DEBUG
                    );

        // if no URI is received, abort process
        if ( count( $data ) == 0 ){
            Logger::Log(
                        'Controller nothing to route',
                        LogLevel::DEBUG
                        );

            $this->_app->response->setStatus( 409 );
            $this->_app->stop( );
            return;
        }

        $URI = '/'.implode('/',$data);

        // get possible links
        $list = CConfig::getLinks($this->_conf->getLinks( ),'out');

        foreach ( $list as $links ){

            $componentURL = $links->getAddress( );
            $similar = Controller2::UrlAnd($componentURL,$URI );

            if ($similar != null){
                $URI2 = substr($URI,Controller2::UrlAnd($componentURL,$URI ));
                $relevanz = explode(' ',$links->getRelevanz());
                $found = false;
                foreach ($relevanz as $rel){
                    if ($rel == 'ALL'){
                        $found = true;
                        break;
                    }

                    $sub = strpos($rel, '_');
                    if ($sub !== false){
                        $method = substr($rel, 0, $sub);
                        $path = substr($rel, $sub+1);

                        if (strtoupper($method) == strtoupper($this->_app->request->getMethod())){
                            $router = new \Slim\Router();
                            $route = new \Slim\Route($path,'is_array');
                            $route->via(strtoupper($method));
                            $router->map($route);

                            $routes = count($router->getMatchedRoutes(strtoupper($method), $URI2),true);
                            if ($routes===0){
                                continue;
                            }

                            $found = true;
                            break;
                        }
                    }
                }

                if (!$found) continue;


                // create a custom request
                $ch = Request::custom(
                                      $this->_app->request->getMethod( ),
                                      $componentURL.substr($URI,$similar),
                                      array(), //$this->_app->request->headers->all( )
                                      $this->_app->request->getBody( )
                                      );

                // checks the answered status code
                if ( $ch['status'] >= 200 &&
                     $ch['status'] <= 299 ){

                    // finished
                    $this->_app->response->setStatus( $ch['status'] );
                    $this->_app->response->setBody( $ch['content'] );

                    if ( isset( $ch['headers']['Content-Type'] ) )
                        $this->_app->response->headers->set(
                                                            'Content-Type',
                                                            $ch['headers']['Content-Type']
                                                            );

                    if ( isset( $ch['headers']['Content-Disposition'] ) )
                        $this->_app->response->headers->set(
                                                            'Content-Disposition',
                                                            $ch['headers']['Content-Disposition']
                                                            );

                    Logger::Log(
                                'Controller2 search done',
                                LogLevel::DEBUG
                                );

                    $this->_app->stop( );
                }
            }
        }

        // no positive response or no operative link
        $this->_app->response->setStatus( 409 );
        $this->_app->response->setBody( '' );
    }
}

 