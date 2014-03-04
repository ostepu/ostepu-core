<?php
/**
 * @file LCourse.php contains the LCourse class
 *
 * @author Christian Elze
 * @author Peter König
 * @author Martin Daute
 */

require '../Include/Slim/Slim.php';
include '../Include/Request.php';
include_once( '../Include/CConfig.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LCourse-Component
 */
class LCourse
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "course";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LCourse::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LCourse::$_prefix = $value;
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

        //POST AddCourse
        $this->app->post('/'.$this->getPrefix().'(/)', array($this, 'AddCourse'));

        //PUT EditCourse
        $this->app->put('/'.$this->getPrefix().'/course/:courseid(/)', array($this, 'editCourse'));

        //DELETE DeleteCourse
        $this->app->delete('/'.$this->getPrefix().'/course/:courseid(/)', array($this, 'deleteCourse'));

        //POST AddCourseMember
        $this->app->post('/'.$this->getPrefix().'/course/:courseid/user/:userid/status/:status(/)', array($this, 'addCourseMember'));

        //GET GetCourseMember
        $this->app->get('/'.$this->getPrefix().'/course/:courseid/user(/)', array($this, 'getCourseMember'));

        //GET GetCourses
        $this->app->get('/'.$this->getPrefix().'/user/:userid(/)', array($this, 'getCourses'));

        //run Slim
        $this->app->run();
    }

    /**
     * Adds a course.
     *
     * Called when this component receives an HTTP POST request to
     * /course(/).
     * The request body should contain a JSON object representing the course's
     * attributes.
     */
    public function AddCourse(){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Edits a course.
     *
     * Called when this component receives an HTTP PUT request to
     * /course/course/$courseid(/).
     * The request body should contain a JSON object representing the course's new
     * attributes.
     *
     * @param int $courseid The id of the course that is being updated.
     */
    public function editCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Deletes a course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /course/course/$courseid(/).
     *
     * @param int $courseid The id of the course that is being deleted.
     */
    public function deleteCourse($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/course/'.$courseid;
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Adds a user to a course.
     *
     * Called when this component receives an HTTP POST request to
     * /course/$courseid/user/$userid/status/$status(/).
     *
     * @param int $courseid The id of the course to which the user is being added.
     * @param int $userid The id of the user that is being added.
     * @param int $status The status this user should have in this course.
     */
    public function addCourseMember($courseid, $userid, $status){
        $header = $this->app->request->headers->all();
        $body = array('id' => $userid,
                      'courses' => array(array('status' => $status,
                                               'course' => array('id' => $courseid)
                                               )
                                         )
                      );
        $body = json_encode($body);
        $URL = $this->lURL.'/DB/coursestatus';
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    /**
     * Returns all users to a course.
     *
     * Called when this component receives an HTTP GET request to
     * /course/course/$courseid/user(/).
     *
     * @param int $courseid The id of the course whose users should be returned
     */
    public function getCourseMember($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/user/course/'.$courseid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }

    /**
     * Returns all courses a given user belongs to.
     *
     * Called when this component receives an HTTP GET request to
     * /course/user/$userid(/).
     *
     * @param int $userid The id of the user.
     */
    public function getCourses($userid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/user/'.$userid;
        $answer = Request::custom('GET', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
}
/**
 * get new Config-Datas from DB
 */
$com = new CConfig(LCourse::getPrefix());

/**
 * run a new instance of Course-Class with the Config-Datas
 */
if (!$com->used())
    new LCourse($com->loadConfig());
?>