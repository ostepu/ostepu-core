<?php
/**
 * @file LController.php Contains the LController class
 *
 * @author Martin Daute
 * @author Christian Elze
 * @author Peter Koenig
 * @date 2013-2014
 */

require_once dirname(__FILE__) . '/../../Assistants/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to send all requests from userinterface
 * on the correct logic component.
 */
class LController
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LController::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LController::$_prefix = $value;
    }

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LController::getPrefix( ), dirname(__FILE__) );

        // runs the LController
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = $conf->getLinks();

        // POST/GET/PUT/DELETE chooseDestination
        $this->app->map('/:string+', array($this, 'chooseDestination'))
                    ->via('POST', 'GET', 'PUT', 'DELETE');

        // run Slim
        $this->app->run();
    }

    /**
     * Chooses the destination for a request.
     *
     * Called always when this component receives a
     * HTTP POST, GET, PUT or DELETE
     *
     * @param array $string An array of strings that contains the URL
     * with which the controller has been called.
     */
    public function chooseDestination($string){
        $method = $this->app->request->getMethod();
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();

        // if the prefix is "DB", send the request on the database controller
        if ($string[0] == "DB") {
            unset($string[0]);
            // set the database URL
            $URI = CConfig::getLink($this->query, "database")->getAddress();
            $this->sendNewRequest($string, $method, $URI, $header, $body);
            $this->app->stop();
        } elseif ($string[0] == "FS") {
        // if the prefix is "FS", send the request on the database controller
            unset($string[0]);
            // set the filesystem URL
            $URI = CConfig::getLink($this->query, "filesystem")->getAddress();
            $this->sendNewRequest($string, $method, $URI, $header, $body);
            $this->app->stop();
        } else {
        // if the prefix is another one, send the request on the corresponding logic component
            $arrayOfLinks = $this->query;
            // search for the correct component URL and set it
            foreach ($arrayOfLinks as $linkObj){
                if ($linkObj->getPrefix() == $string[0]){
                    $URI = $linkObj->getAddress();
                    $this->sendNewRequest($string, $method, $URI, $header, $body);
                    $this->app->stop();
                }
            }
            
            $this->app->response->setStatus(412);
            $this->app->response->stop();
        }
    }

    /**
     * Sends a new request.
     *
     * Called allways if the controller receives a request for
     * database, filesystem or a logic component.
     * Completes the URL, sends the new request and sets the response.
     *
     * @param array $string An array of strings that contains the URL
     * with which the controller has been called.
     * @param string $method The method of the received request.
     * @param string $URI The first part of the new request.
     * @param array $header The header of the received request.
     * @param string $body The body of the received request.
     */
    public function sendNewRequest($string, $method, $URI, $header, $body){
        // completes the URL by attach each field of $string to the given URL-part
        foreach ($string as $str) {
            $URI = $URI.'/'.$str;
        }

        // send the new request and set the response
        $answer = Request::custom($method, $URI, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
        if(isset($answer['headers']['Content-Type']))
            $this->app->response->headers->set('Content-Type', $answer['headers']['Content-Type']);
        if(isset($answer['headers']['Content-Disposition']))
            $this->app->response->headers->set('Content-Disposition', $answer['headers']['Content-Disposition']);
    }
}