<?php
/**
 * @file LGroup.php Contains the LGroup class
 * 
 * @author Peter Koenig
 * @author Christian Elze
 * @author Martin Daute 
 */

require '../../Assistants/Slim/Slim.php';
include '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LGroup-Component
 */
class LGroup
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "group";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LGroup::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LGroup::$_prefix = $value;
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

        // POST inviteInGroup
        $this->app->post('/'.$this->getPrefix().'(/)',
                            array($this, 'inviteInGroup'));

        // PUT joinGroup
        $this->app->put('/'.$this->getPrefix().'/accept(/)', array($this, 'joinGroup'));

        // PUT leaveGroup
        $this->app->put('/'.$this->getPrefix().'/user/:userid/leave(/)',
                            array($this, 'leaveGroup'));

        // PUT ejectFromGroup
        $this->app->put('/'.$this->getPrefix().'/user/:userid/deleteMember(/)',
                            array($this, 'ejectFromGroup'));

        // GET getGroup
        $this->app->get('/'.$this->getPrefix().'/user/:userid/exercisesheet/:sheetid(/)',
                        array($this, 'getGroup'));

        //run Slim
        $this->app->run();
    }

    /**
     * Adds an invitation.
     *
     * Called when this component receives an HTTP POST request to
     * /group(/).
     * The request body should contain a JSON object representing the invitation.
     */
    public function inviteInGroup(){
        $body = $this->app->request()->getBody();
        $header = $this->app->request()->headers->all();
        $URL = $this->lURL.'/DB/invitation/';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Accepts an invitation.
     *
     * Called when this component receives an HTTP PUT request to
     * /group/accept(/).
     * The request body should contain a JSON object representing the invitation.
     */
    public function joinGroup(){
        $body = $this->app->request()->getBody();
        $header = $this->app->request()->headers->all();
        $URL = $this->lURL.'/DB/accept';
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Leaves a group.
     *
     * Called when this component receives an HTTP PUT request to
     * /group/user/$userid/leave(/).
     * The request body should contain a JSON object representing the group.
     *
     * @param int $userid The id of the user who leavs the group
     */
    public function leaveGroup($userid){
        $body = $this->app->request()->getBody();
        $header = $this->app->request()->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid.'/leave';
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Ejects a group member.
     *
     * Called when this component receives an HTTP PUT request to
     * /group/user/$userid/deleteMember(/).
     * The request body should contain a JSON object representing the group.
     *
     * @param int $userid The id of the user who ejects another one.
     */
    public function ejectFromGroup($userid){
        $body = $this->app->request()->getBody();
        $header = $this->app->request()->headers->all();
        // ??? deletMember ???
        $URL = $this->lURL.'/DB/user/'.$userid.'/deletMember';
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns a group.
     *
     * Called when this component receives an HTTP PUT request to
     * /group/user/$userid/exercisesheet/$sheetid(/).
     *
     * @param int $userid The id of the user.
     * @param int $sheetid The id of the exercisesheet.
     */
    public function getGroup($userid, $sheetid){
        $body = $this->app->request()->getBody();
        $header = $this->app->request()->headers->all();
        $URL = $this->lURL.'/DB/user/'.$userid.'/exerciseSheet/'.$sheetid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
}
?>