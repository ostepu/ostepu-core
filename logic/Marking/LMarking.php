<?php
/**
 * @file LUser.php Contains the LUser class
 * 
 * @author Peter Koenig
 * @author Christian Elze
 * @author Martin Daute 
 */

require 'Slim/Slim.php';
include './Include/Request.php';
include_once( './Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LMarking-Component
 */
class LMarking
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "marking";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LMarking::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LMarking::$_prefix = $value;
    }

    /**
     * @var string $lURL the URL of the logic-controller
     */     
    private $lURL = ""; // readed out from config below

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    public function __construct($conf)
    {
        // initialize slim    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->query = CConfig::getLink($conf->getLinks(),"controller");

        // initialize lURL
        $this->lURL = $this->query->getAddress();

        // POST AddMarking
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'addMarking'));

        // GET GetMarkingURL
        $this->app->get('/'.$this->getPrefix().'/marking/:markingid(/)',
                        array ($this, 'getMarkingURL'));

        // DELETE DeleteMarking
        $this->app->delete('/'.$this->getPrefix().'/marking/:markingid(/)',
                        array($this, 'deleteMarking'));

        // PUT EditMarking
        $this->app->put('/'.$this->getPrefix().'/marking/:markingid(/)',
                        array($this, 'editMarking'));

        // PUT EditMarkingStatus
        $this->app->put('/'.$this->getPrefix().'/marking/:markingid/status(/)',
                        array($this, 'editMarkingStatus'));

        // run Slim
        $this->app->run();
    }

    /**
     * Adds a new marking.
     *
     * Called when this component reveives an HTTP POST request to
     * /marking(/).
     * The request body should contain a JSON object representing the new marking.
     */
    public function addMarking(){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'file'});
        // request to filesystem
        $URL = $this->lURL.'/FS/exercise/'.$exerciseid.'/tutor/'.$tutorid;
        $answer = Request::custom('POST', $URL, $header, $file);

        if($answer['status'] == 200){
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/exercise/'.$exerciseid.'/tutor/'.$tutorid;
            $answer = Request::custom('POST', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Returns the URL to a given marking.
     *
     * Called when this component receives an HTTP GET request to
     * /marking/marking/$markingid(/).
     *
     * @param int $markingid The id of the marking the returned URL belongs to.
     */
    public function getMarkingURL($markingid) {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes a marking.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/marking/$markingid(/).
     *
     * @param int $markingid The id of the marking that is being deleted.
     */
    public function deleteMarking($markingid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);

        /**
         * if DB-Request was succsessfull the file also gets removed from FS
         * otherwise returns the Status-Code from DB
         */
        $fileObject = json_decode($answer['content']);
        //if address-field exists, read it out
        if (isset($fileObject->{'address'})){
            $fileAddress = $fileObject->{'address'};
        }

        if( $answer['status'] < 300){
            $URL = $this->lURL.'/FS/'.$fileAddress; 
            $answer = Request::custom('DELETE', $URL, $header, $body);
        }
    }

    /**
     * Edits a marking.
     *
     * Called when this component receives an HTTP PUT request to
     * /marking/marking/$markingid(/).
     * The request body should contain a JSON object representing the marking's new
     * attributes.
     *
     * @param int $markingid The id of the marking that is being updated.
     */
    public function editMarking($markingid){
        $header = $this->app->request->headers->all();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'file'});
        //Anfrage an FileSystem
        $URL = $this->lURL.'/FS/marking/'.$markingid.'/tutor/'.$tutorid;
        $answer = Request::custom('PUT', $URL, $header, $file);

        if($answer['status'] == 200){
            $body->{'_file'} = json_decode($answer['content']);
            //Anfrage an DataBase
            $URL = $this->lURL.'/DB/marking/'.$markingid.'/tutor/'.$tutorid;
            $answer = Request::custom('PUT', $URL, $header, json_encode($body));
            $this->app->response->setStatus($answer['status']);
        }
    }

    /**
     * Edits a marking status.
     *
     * Called when this component receives an HTTP PUT request to
     * /marking/marking/$markingid/status(/).
     * The request body should contain a JSON object representing the marking's new
     * attributes.
     *
     * @param int $markingid The id of the marking that is being updated.
     */
    public function editMarkingStatus($markingid){
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/marking/'.$markingid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setBody($answer['content']);
        $this->app->response->setStatus($answer['status']);
    }
}

// get new config data from DB
$com = new CConfig(LMarking::getPrefix());

// create a new instance of LUser class with the config data
if (!$com->used())
    new LMarking($com->loadConfig());
?>