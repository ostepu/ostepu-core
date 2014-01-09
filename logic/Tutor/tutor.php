<?php 

require 'Slim/Slim.php';
include 'include/Assistants/Request.php';
//include 'include/Assistants/StructTutorAssignment.php';

\Slim\Slim::registerAutoloader();
    
class Tutor
{    
    private $lURL = ""; //aus config lesen
    
    public function __construct()
    {    
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        //AllocateByExercise
        $this->app->get('', array($this, 'allocateByExercise'));        //Adressen/Parameter fehlen
        
        //AllocateByStudent
        $this->app->get('', array($this, 'allocateByStudent'));        //Adressen/Parameter fehlen
        
        //AutoAllocate
        $this->app->put('/course/:courseid/exercise/:sheetid/auto/autoart',
                        array($this, 'autoAllocate'));        //Adressen noch anpassen (Parameter mit Compo-Namen)

        //GetTutorList
        $this->app->get('/course/:courseid/exercise/:sheetid/manu/manuart',
                        array($this, 'getTutorList'));
                        
        //SetTutorList
        $this->app->post('/course/:courseid/exercise/:sheetid/manu/manuart',
                        array($this, 'setTutorList'));
                        
        $this->app->run();
    }
    
    public function allocateByExercise(){
    
    }
    
    public function allocateByStudent(){
    
    }
    
    public function autoAllocate($courseid, $sheetid, $autoart){
    
    }
    
    public function getTutorList($courseid, $sheetid, $manuart){
    
    }

    public function setTutorList($courseid, $sheetid, $manuart){
    
    }
}

new Tutor();
?>