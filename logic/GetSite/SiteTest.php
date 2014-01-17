<?php

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';

\Slim\Slim::registerAutoloader();



class SiteTest
{    
    
    public function __construct(){
        $this->app = new \Slim\Slim();
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        $this->app->get('/DB/coursestatus/course/:courseid/status/1', array($this, 'getTutors'));
        
        $this->app->get('/DB/exercisesheet/:sheetid', array($this, 'getMarkings'));
        
        $this->app->get('/DB/selectedsubmission/exercisesheet/:sheetid', array($this, 'getSubmissions'));
        
        $this->app->run();
        
    }
    
    public function getTutors($courseid){
        $response = array();
        for ($i = 0 ; $i < 10; $i++){
            $tutor = array(
                'id' => "$i",
                'userName' => "tut".$i,
                'firstName' => "tut".$i."First",
                'lastName' => "tut".$i."last"
                );
            array_push($response, $tutor);
        }
        //print_r(utf8_decode(json_encode($response)));
        $this->app->response->setBody(json_encode($response));
        
    }
    
    public function getMarkings($sheetid){
        $response = array();
        for ($i = 0 ; $i < 10; $i++){
            $marking = array(
                'id' => $i*10,
                'submission' => array('id' => $i * 10 + 5),
                'tutorId' => "$i"
                );
            array_push($response, $marking);
        }
        print_r($response);
        $this->app->response->setBody(json_encode($response));
    }
    
    public function getSubmissions($sheetid){
        $response = array();
        for ($i = 0 ; $i < 10; $i++){
            $int = $i * 10 +5;
            $submission = array(
                'id' => "$int"
                );
            array_push($response, $submission);
        }
        for ($i = 96 ; $i < 100; $i++){
            $submission = array(
                'id' => "$i"
                );
            array_push($response, $submission);
        }
        
        $this->app->response->setBody(json_encode($response));
    }
}

new SiteTest();


?>