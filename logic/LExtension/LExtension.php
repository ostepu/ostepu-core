<?php
/**
 * @file LExtension.php contains the LExtension class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

require_once dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LExtension-Component
 */
class LExtension
{
    /**
     * @var Slim $_app the slim object
     */
    private $app = null;

    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "link";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LExtension::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LExtension::$_prefix = $value;
    }

    private $_extension = array( );

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LExtension::getPrefix( ) . ',course', dirname(__FILE__) );

        // runs the LExtension
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );

        // initialize slim
        $this->app = new \Slim\Slim(array('debug' => true));
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_extension = CConfig::getLinks($conf->getLinks(),"extension");

        //POST AddCourseExtension
        $this->app->post('/link/course/:courseid/extension/:name', array($this, 'addCourseExtension'));

        //DELETE DeleteCourseExtension
        $this->app->delete('/link/course/:courseid/extension/:name', array($this, 'deleteCourseExtension'));

        //DELETE DeleteCourse
        $this->app->delete('/course/:courseid/', array($this, 'deleteCourse'));

        //GET GetExtensionInstalled
        $this->app->get('/link/exists/course/:courseid/extension/:name', array($this, 'getExtensionInstalled'));

        //GET GetInstalledExtensions
        $this->app->get('/link/course/:courseid/extension', array($this, 'getInstalledExtensions'));





        //GET GetExtensions
        $this->app->get('/link/extension(/)', array($this, 'getExtensions'));

        //GET GetExtensionExists
        $this->app->get('/link/exists/extension/:name', array($this, 'getExtensionExists'));

        //GET GetExtension
        $this->app->get('/link/extension/:name', array($this, 'getExtension'));

        //run Slim
        $this->app->run();
    }

    /**
     * Removes the given extension from course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /link/course/$courseid/extension/$name(/).
     *
     * @param int $courseid The id of the course.
     * @param int $name The name of the component
     */
    public function deleteCourseExtension($courseid, $name)
    {
        foreach($this->_extension as $link){
            if ($link->getTargetName() === $name || $link->getTarget() === $name){

                $result = Request::routeRequest(
                                                'DELETE',
                                                '/course/'.$courseid,
                                                $this->app->request->headers->all(),
                                                '',
                                                $link,
                                                'course'
                                                );

                // checks the correctness of the query
                if ( $result['status'] >= 200 &&
                     $result['status'] <= 299 ){

                    $this->app->response->setStatus( 201 );
                    $this->app->response->setBody( null );
                    if ( isset( $result['headers']['Content-Type'] ) )
                        $this->app->response->headers->set(
                                                            'Content-Type',
                                                            $result['headers']['Content-Type']
                                                            );
                    $this->app->stop( );

                } else {
                    Logger::Log(
                                'DELETE DeleteCourseExtension failed',
                                LogLevel::ERROR
                                );
                    $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                    $this->app->stop( );
                }
            }
        }

        $this->app->response->setStatus( 404 );
        $this->app->response->setBody( null );
    }

    /**
     * Removes all extensions from the given course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /link/course/$courseid/extension/$name.
     *
     * @param int $courseid The id of the course.
     * @param int $name The name of the component
     */
    public function deleteCourse($courseid)
    {
        $extensions = array();
        foreach($this->_extension as $link){
            $result = Request::routeRequest(
                                            'GET',
                                            '/link/exists/course/'.$courseid,
                                            $this->app->request->headers->all(),
                                            '',
                                            $link,
                                            'link'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $extensions[] = $link;
            }
        }

        foreach($extensions as $link){

            $result = Request::routeRequest(
                                            'DELETE',
                                            '/course/'.$courseid,
                                            $this->app->request->headers->all(),
                                            '',
                                            $link,
                                            'course'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                // ok

            } else {
                Logger::Log(
                            'DELETE DeleteCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->stop( );
            }
        }

        $this->app->response->setStatus( 201 );
        $this->app->response->setBody( null );
    }

    /**
     * Install the given component to a course.
     *
     * Called when this component receives an HTTP POST request to
     * /link/course/$courseid/extension/$name(/).
     *
     * @param int $courseid The id of the course.
     * @param int $name The name of the component
     */
    public function addCourseExtension($courseid, $name)
    {
        foreach($this->_extension as $link){
            if ($link->getTargetName() === $name || $link->getTarget() === $name){

                // TODO: hier eventuell alle Course Daten verwenden (vorher Abrufen)
                $courseObject = Course::createCourse(
                                                    $courseid,
                                                    null,
                                                    null,
                                                    null
                                                    );

                $result = Request::routeRequest(
                                                'POST',
                                                '/course',
                                                $this->app->request->headers->all(),
                                                Course::encodeCourse($courseObject),
                                                $link,
                                                'course'
                                                );

                // checks the correctness of the query
                if ( $result['status'] >= 200 &&
                     $result['status'] <= 299 ){

                    $this->app->response->setStatus( 201 );
                    $this->app->response->setBody( null );
                    if ( isset( $result['headers']['Content-Type'] ) )
                        $this->app->response->headers->set(
                                                            'Content-Type',
                                                            $result['headers']['Content-Type']
                                                            );
                    $this->app->stop( );

                } else {
                    Logger::Log(
                                'POST AddCourseExtension failed',
                                LogLevel::ERROR
                                );
                    $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                    $this->app->stop( );
                }
            }
        }

        $this->app->response->setStatus( 404 );
        $this->app->response->setBody( null );
    }

    /**
     * Returns all installed extensions for the given course.
     *
     * Called when this component receives an HTTP GET request to
     * /link/course/$courseid/extension(/).
     *
     * @param int $courseid The id of the course.
     */
    public function getInstalledExtensions($courseid)
    {
        $extensions = array();

        foreach($this->_extension as $link){
            $result = Request::routeRequest(
                                            'GET',
                                            '/link/exists/course/'.$courseid,
                                            $this->app->request->headers->all(),
                                            '',
                                            $link,
                                            'link'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 &&
                 $result['status'] <= 299 ){
                $extensions[] = $link;
            }
        }

        if (!empty($extensions)){
            $this->app->response->setStatus( 200 );
        } else
            $this->app->response->setStatus( 404 );

        $this->app->response->setBody( Link::encodeLink( $extensions ) );
    }

    /**
     * Returns whether the component is installed for the given course
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/course/$courseid/extension/$name(/).
     *
     * @param int $courseid The id of the course.
     * @param int $name The name of the component
     */
    public function getExtensionInstalled($courseid, $name)
    {
        foreach($this->_extension as $link){
            if ($link->getTargetName() === $name || $link->getTarget() === $name){
                $result = Request::routeRequest(
                                                'GET',
                                                '/link/exists/course/'.$courseid,
                                                $this->app->request->headers->all(),
                                                '',
                                                $link,
                                                'course'
                                                );

                // checks the correctness of the query
                if ( $result['status'] >= 200 &&
                     $result['status'] <= 299 ){

                    $this->app->response->setStatus( 200 );
                    $this->app->response->setBody( null );
                    if ( isset( $result['headers']['Content-Type'] ) )
                        $this->app->response->headers->set(
                                                            'Content-Type',
                                                            $result['headers']['Content-Type']
                                                            );
                    $this->app->stop( );
                } else {
                    Logger::Log(
                                'POST GetExtensionInstalled failed',
                                LogLevel::ERROR
                                );
                    $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                    $this->app->stop( );
                }
            }
        }
        $this->app->response->setStatus( 404 );
        $this->app->response->setBody( null );
    }

    /**
     * Returns whether the extension exists (can be installed)
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/extension/$name(/).
     *
     * @param int $name The name of the component
     */
    public function getExtensionExists($name)
    {
        foreach($this->_extension as $link){
            if ($link->getTargetName() === $name || $link->getTarget() === $name){
                $this->app->response->setStatus( 200 );
                $this->app->response->setBody(null);
                $this->app->stop( );
            }
        }

        $this->app->response->setStatus( 404 );
        $this->app->response->setBody( null );
    }

    /**
     * Returns informations about a given extension
     *
     * Called when this component receives an HTTP GET request to
     * /link/extension/$name(/).
     *
     * @param int $name The name of the component
     */
    public function getExtension($name)
    {
        foreach($this->_extension as $link){
            if ($link->getTargetName() === $name || $link->getTarget() === $name){
                $this->app->response->setStatus( 200 );
                $this->app->response->setBody(Link::encodeLink($link));
                $this->app->stop( );
            }
        }

        $this->app->response->setStatus( 404 );
        $this->app->response->setBody( null );
    }

    /**
     * Returns informations about all existing extensions
     *
     * Called when this component receives an HTTP GET request to
     * /link/extension(/).
     */
    public function getExtensions()
    {
        $this->app->response->setStatus( 200 );
        $this->app->response->setBody( Link::encodeLink( $this->_extension ) );
    }
}