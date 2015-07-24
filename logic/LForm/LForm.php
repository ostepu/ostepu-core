<?php
/**
 * @file LForm.php Contains the LForm class
 * 
 * @author Till Uhlig
 * @date 2014
 */

require_once dirname(__FILE__) . '/../../Assistants/Slim/Slim.php';
include_once dirname(__FILE__) . '/../../Assistants/Request.php';
include_once dirname(__FILE__) . '/../../Assistants/CConfig.php';
include_once dirname(__FILE__) . '/../../Assistants/Structures.php';

\Slim\Slim::registerAutoloader();

/**
 * A class, to handle requests to the LForm-Component
 */
class LForm
{
    /**
     * @var Component $_conf the component data object
     */
    private $_conf=null;

    /**
     * @var string $_prefix the prefix, the class works with
     */
    private static $_prefix = "form";

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix()
    {
        return LForm::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        LForm::$_prefix = $value;
    }

    /**
     * @var Link[] $_form a list of links
     */
    private $_form = array( );
    
    /**
     * @var Link[] $_choice a list of links
     */
    private $_choice = array( );
    private $_createCourse = array( );
    
    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     */
    public function __construct()
    {
        // runs the CConfig
        $com = new CConfig( LForm::getPrefix( ) . ',course,link', dirname(__FILE__) );

        // runs the LForm
        if ( $com->used( ) ) return;
            $conf = $com->loadConfig( );
            
        // initialize slim    
        $this->app = new \Slim\Slim(array('debug' => true));
        $this->app->response->headers->set('Content-Type', 'application/json');

        // initialize component
        $this->_conf = $conf;
        $this->_form = CConfig::getLinks($conf->getLinks(),"form");
        $this->_choice = CConfig::getLinks($conf->getLinks(),"choice");
        $this->_createCourse = CConfig::getLinks($conf->getLinks(),"postCourse");

        // POST AddForm
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'addForm'));
                        
        // POST AddCourse
        $this->app->post( 
                         '/course(/)',
                         array( 
                               $this,
                               'addCourse'
                               )
                         );
                         
        // DELETE DeleteCourse
        $this->app->delete( 
                         '/course/:courseid(/)',
                         array( 
                               $this,
                               'deleteCourse'
                               )
                         );
                         
        // GET GetExistsCourse
        $this->app->get( 
                         '/link/exists/course/:courseid(/)',
                         array( 
                               $this,
                               'getExistsCourse'
                               )
                        );
                         
        // run Slim
        $this->app->run();
    }
    
    /**
     * Returns whether the component is installed for the given course
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/course/$courseid(/).
     *
     * @param int $courseid A course id.
     */
    public function getExistsCourse($courseid)
    {
         Logger::Log( 
                    'starts GET GetExistsCourse',
                    LogLevel::DEBUG
                    );

        foreach ( $this->_createCourse as $_link ){
            $result = Request::routeRequest( 
                                            'GET',
                                            '/link/exists/course/'.$courseid,
                                            $this->app->request->headers->all(),
                                            '',
                                            $_link,
                                            'link'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                // nothing
            } else {
                $this->app->response->setStatus( 409 );
                $this->app->response->setBody( null );
                $this->app->stop( );
            }
        }
        
        $this->app->response->setStatus( 200 );
        $this->app->response->setBody( null );
    }
   
   /**
     * Adds the component to a course
     *
     * Called when this component receives an HTTP POST request to
     * /course(/).
     */
    public function addCourse()
    {
         Logger::Log( 
                    'starts POST AddCourse',
                    LogLevel::DEBUG
                    );
                    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        
        $course = Course::decodeCourse($body);
    
        foreach ( $this->_createCourse as $_link ){
            $result = Request::routeRequest( 
                                            'POST',
                                            '/course',
                                            $header,
                                            Course::encodeCourse($course),
                                            $_link,
                                            'course'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){

                $this->app->response->setStatus( 201 );
                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
            
                if ($course->getId()!==null){
                    $this->deleteCourse($course->getId());
                }
            
                Logger::Log( 
                            'POST AddCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->response->setBody( Course::encodeCourse( $course ) );
                $this->app->stop( );
            }
        }
        
        $this->app->response->setBody( Course::encodeCourse( $course ) );
    }
   
    /**
     * Removes the component from a given course
     *
     * Called when this component receives an HTTP DELETE request to
     * /course/$courseid(/).
     *
     * @param string $courseid The id of the course.
     */
    public function deleteCourse($courseid)
    {
        $this->app->response->setStatus( 201 );
        Logger::Log( 
                    'starts DELETE DeleteCourse',
                    LogLevel::DEBUG
                    );
                    
        $header = $this->app->request->headers->all();
        $courseid = DBJson::mysql_real_escape_string( $courseid ); 
        
        foreach ( $this->_createCourse as $_link ){
            $result = Request::routeRequest( 
                                            'DELETE',
                                            '/course/'.$courseid,
                                            $header,
                                            '',
                                            $_link,
                                            'course'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){


                if ( isset( $result['headers']['Content-Type'] ) )
                    $this->app->response->headers->set( 
                                                        'Content-Type',
                                                        $result['headers']['Content-Type']
                                                        );
                
            } else {
                Logger::Log( 
                            'POST DeleteCourse failed',
                            LogLevel::ERROR
                            );
                $this->app->response->setStatus( isset( $result['status'] ) ? $result['status'] : 409 );
                $this->app->stop( );
            }
        }
    }
   
    /**
     * Adds a form.
     *
     * Called when this component receives an HTTP POST request to
     * /form(/).
     */
    public function addForm()
    {
        Logger::Log( 
                    'starts POST AddForm',
                    LogLevel::DEBUG
                    );
                    
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $this->app->response->setStatus( 201 );
        
        $forms = Form::decodeForm($body);
        
        // always been an array
        $arr = true;
        if ( !is_array( $forms ) ){
            $forms = array( $forms );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        $choices = array();
        foreach ( $forms as $key => $form ){
            $choices[] = $form->getChoices();
            $forms[$key]->setChoices(null);
        }
               
        $resForms = array();
        
        foreach ( $forms as $form ){
            
            $method='POST';
            $URL='/form';
            if ($form->getFormId() !== null){
               $method='PUT'; 
               $URL = '/form/'.$form->getFormId();
            }
            
            $result = Request::routeRequest( 
                                            $method,
                                            $URL,
                                            array(),
                                            Form::encodeForm($form),
                                            $this->_form,
                                            'form'
                                            );

            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 && isset($result['content'])){
                 $newform = Form::decodeForm($result['content']);
                 if ($form->getFormId() === null)
                    $form->setFormId($newform->getFormId());
                $resForms[] = $form; 
            } else {
                $f = new Form();
                $f->setStatus(409);
                $resForms[] = $f;
            }
        }
        $forms = $resForms;
        
        $i=0;
        foreach ( $choices as &$choicelist ){
            foreach ( $choicelist as $key2 => $choice ){
                if ($forms[$i]->getFormId() !== null){
                    $formId = $forms[$i]->getFormId();
                    $choicelist[$key2]->setFormId($formId);
                    
                    $method='POST';
                    $URL='/choice';
                    if ($choicelist[$key2]->getChoiceId() !== null){
                       $method='PUT'; 
                       $URL = '/choice/'.$choice->getChoiceId();
                    }
                    $result = Request::routeRequest( 
                                                    $method,
                                                    $URL,
                                                    array(),
                                                    Choice::encodeChoice($choicelist[$key2]),
                                                    $this->_choice,
                                                    'choice'
                                                    );
                                                    
                    if ( $result['status'] >= 200 && 
                        $result['status'] <= 299 ){
                        $newchoice = Choice::decodeChoice($result['content']);
                        if ($choicelist[$key2]->getChoiceId() === null)
                            $choicelist[$key2]->setChoiceId($newchoice->getChoiceId());
                        $choicelist[$key2]->setStatus(201);
                    } else {
                        $choicelist[$key2]->setStatus(409);
                    }
                }
            }
            $forms[$i]->setChoices($choicelist);
            $i++; 
        }
                                    
        // checks the correctness of the query
        /*if ( $result['status'] >= 200 && 
             $result['status'] <= 299 && isset($result['content'])){
            $newforms = Form::decodeForm($result['content']);
            if ( !is_array( $newforms ) )
                $newforms = array($newforms);
                
            $i=0;    
            foreach ($forms as &$form){
                if ($form->getFormId() === null)
                    $form->setFormId($newforms[$i]->getFormId());
                $i++;
            }            

            $sendChoices = array();
            $i=0;
            foreach ( $choices as $choicelist ){
                foreach ( $choicelist as $choice ){
                    $choice->setFormId($forms[$i]->getFormId());
                    $sendChoices[] = $choice;
                }
            $i++; 
            }
            $choices = $sendChoices;
            
            $result = Request::routeRequest( 
                                            'POST',
                                            '/choice',
                                            $this->app->request->headers->all( ),
                                            Choice::encodeChoice($choices),
                                            $this->_choice,
                                            'choice'
                                            );
                            
            // checks the correctness of the query
            if ( $result['status'] >= 200 && 
                 $result['status'] <= 299 ){
                $newchoices = Choice::decodeChoice($result['content']);

                $choicelist = array();
                $i=0;
                foreach ( $choices as &$choice ){
                    $choice->setChoiceId($newchoices[$i]->getChoiceId());
                    
                    if (!isset($choicelist[$choice->getFormId()]))
                        $choicelist[$choice->getFormId()] = array();
                      
                    $choicelist[$choice->getFormId()][] = $choice;
                    
                    $i++;
                }
                
                foreach ( $forms as &$form ){
                    $form->setChoices($choicelist[$form->getFormId()]);
                }
                
                $res[] = $forms;
                
            } else{
                // remove forms on failure
                foreach ($forms as $form){
                    $result = Request::routeRequest( 
                                    'DELETE',
                                    '/form/'.$form->getFormId(),
                                    $this->app->request->headers->all( ),
                                    '',
                                    $this->_form,
                                    'form'
                                    );
                }
                
                $res[] = null;
                $this->app->response->setStatus( 409 );
            }
                      
        } else {
            $res[] = null;
            $this->app->response->setStatus( 409 );
        }*/
           
        if ($this->app->response->getStatus( ) != 201)
            Logger::Log( 
                        'POST AddForms failed',
                        LogLevel::ERROR
                        );

        if ( !$arr && 
             count( $res ) == 1 ){
            $this->app->response->setBody( Form::encodeForm( $res[0] ) );
            
        } else 
            $this->app->response->setBody( Form::encodeForm( $res ) );
    }
}