<?php
/**
 * @file LForm.php Contains the LForm class
 * 
 * @author Till Uhlig
 */

require_once '../../Assistants/Slim/Slim.php';
include_once '../../Assistants/Request.php';
include_once '../../Assistants/CConfig.php';

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
        $this->_form = CConfig::getLinks($conf->getLinks(),"form");
        $this->_choice = CConfig::getLinks($conf->getLinks(),"choice");

        // POST AddForm
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'addForm'));
                        
        // PUT EditForm
        $this->app->post('/'.$this->getPrefix().'/:formId(/)',
                        array($this, 'editForm'));
                        
        // PUT EditFormObject
        $this->app->post('/'.$this->getPrefix().'(/)',
                        array($this, 'editFormObject'));


        // run Slim
        $this->app->run();
    }

    public function addForm(){
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
        foreach ( $forms as &$form ){
            $choices[] = $form->getChoices();
            $form->setChoices(null);
        }
 
        $result = Request::routeRequest( 
                                        'POST',
                                        '/form',
                                        $this->app->request->headers->all( ),
                                        Form::encodeForm($forms),
                                        $this->_form,
                                        'form'
                                        );
                                    
        // checks the correctness of the query
        if ( $result['status'] >= 200 && 
             $result['status'] <= 299 ){
            $newforms = Form::decodeForm($result['content']);
            if ( !is_array( $newforms ) )
                $newforms = array($newforms);
                
            $i=0;    
            foreach ($forms as &$form){
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
        }
            
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
    
    public function editForm($formId){
    
    }
    
    public function editFormObject(){
    
    }
}

// get new config data from DB
$com = new CConfig(LForm::getPrefix());

// create a new instance of LForm class with the config data
if (!$com->used())
    new LForm($com->loadConfig());
?>