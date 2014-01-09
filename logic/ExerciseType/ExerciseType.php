<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
include_once( 'include/CConfig.php' ); 

\Slim\Slim::registerAutoloader();

class ExerciseType
{
    private $_conf=null;
    
    private static $_prefix = "exercisetype";
    
    public static function getPrefix()
    {
        return ExerciseType::$_prefix;
    }
    public static function setPrefix($value)
    {
        ExerciseType::$_prefix = $value;
    }
    
    private $lURL = ""; //aus config lesen
    
    public function __construct($conf)
    {   
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->_conf = $conf;
        $this->query = array();
        
        $this->query = array(CConfig::getLink($conf->getLinks(),"controller"))
        $this->lURL = querry['address'];
        
        
        //setPossibleTypes
        $this->app->post('/course/:courseid' ,
                            array($this, 'setPossibleTypes'));
        
        //DeletePossibleTypes
        $this->app->delete('/course/:courseid' ,
                            array($this, 'deletePossibleTypes'));
        
        //EditPossibleTypes
        $this->app->put('/course/:courseid' , 
                            array($this, 'editPossibleTypes'));
                            
        $this->app->run();                    
    }
    
    public function setPossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;        
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']); 
    }
    
    public function deletePossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;        
        $answer = Request::custom('DELETE', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']); 
    }
    
    public function editPossibleTypes($courseid)
    {
        $header = $this->app->request->headers->all();
        $body = $this->app->request->getBody();
        $URL = $this->lURL.'/DB/exerciseType/course/'.$courseid;        
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
        $this->app->response->setBody($answer['content']);
    }
}


if (!$com->used())
    new ExerciseType($com->loadConfig());
?>