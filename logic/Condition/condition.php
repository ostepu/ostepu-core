<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
//include 'include/Assistants/StructCourse.php';


\Slim\Slim::registerAutoloader();
	
class Condition
{    
    private $lURL = ""; //aus config lesen
    
    public function __construct()
    {    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        //SetConditions
        $this->app->post('/course/:courseid', array($this, 'setConditions'));        //Adressen noch anpassen (Parameter mit Compo-Namen)
        
        //EditConditions
        $this->app->put('/course/:courseid', array($this, 'editConditions'));
        
        //CheckConditions
        $this->app->get('/course/:courseid/user/:userid',
                        array($this, 'checkConditions'));
                        
        $this->app->run();
    }
    
    public function setConditions($courseid){        
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid;
        $answer = Request::custom('POST', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    public function editConditions($courseid){
        $body = $this->app->request->getBody();
        $header = $this->app->request->headers->all();
        $URL = $this->lURL.'/DB/course/'.$courseid;
        $answer = Request::custom('PUT', $URL, $header, $body);
        $this->app->response->setStatus($answer['status']);
    }

    public function CheckConditions($courseid, $userid){        //Funktion unklar in Aufbau, benötigt Punkte von Marking
    
    }
}

new Condition()
?>