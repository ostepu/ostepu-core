<?php 

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';
include 'include/Assistants/StructFile.php';
include 'include/Assistants/StructSubmission.php';

\Slim\Slim::registerAutoloader();
	
class Submission
{
    private $dbURL = ""; //aus config lesen
    private $fsURL = ""; //aus config lesen
    
    $this->app = new \Slim\Slim();
    $this->app->response->headers->set('Content-Type', 'application/json');
    
    //AddSubmission
    $this->app->post(':data+', array($this, 'addSubmission'));
    
    //EditSubmissionState
    $this->app->put('/submission/:submissionid', 
                    array ($this, 'editSubmissionState'));
    
    //deleteSubmission
    $this->app->delete('/submission/:submissionid', 
                    array($this, 'deleteSubmission'));
                    
    //LoadSubmissionAsZip
    $this->app->get('/exerciseSheet/:sheetid/user/:userid', 
                    array($this, 'loadSubmissionAsZip'));
    
    //ShowSubmissionsHistory
    $this->app->get('/exerciseSheet/:sheetid/user/:userid/history', 
                    array($this, 'showSubmissionsHistory'));
               
    //GetSubmissionURL
    $this->app->get('/submission/:submissionid', 
                    array($this, 'getSubmissionURL'));
    
    
    
    private function addSubmission($data){
        //Parameter abfangen wenn $data "nicht leer"
        
        $header = $this->app->request->getHeader();
        $body = json_decode($this->app->request->getBody());
        $file = json_encode($body->{'_file'});
        //Anfrage an FileSystem
        $body->{'_file'} = createPost($fsURL, $header, $file);
        //Anfrage an DataBase
        $status = createPost($dbURL, $header, json_encode($body));
        $this->app->response->setStatus($status);
    }
    
    private function editSubmissionState($submissionid) {
    	
        $header = $this->app->request->getHeader();
        $body = $this->app->request->getBody();
        $URL = $dbURL.'/submission/'.$submissionid;
        
        $status = createPut($URL, $header, $body);
        $this-app->response->setStatus($status);    
    }

    private function deleteSubmission($submissionid){
    
        $header = $this->app->request->getHeader();
        $body = $this->app->request->getBody();
        $URL = $dbURL.'/submission/'.$submissionid;
        
        $status = createDelete($URL, $header, $body);
        
        if( $status == 200){
            $URL = $fsURL.'/submission/'.$submissionid; 
            $status = createDelete($URL, $header, $body);
        }
        
        $this-app->response->setStatus($status);  
    }
    
    private function loadSubmissionAsZip($sheetid, $userid){                    //Annahme ZipURL in DB abrufbar
    
        $header = $this->app->request->getHeader();
        $body = $this->app->request->getBody();
        $URL = $dbURL.'/exerciseSheet/'.$sheetid.'/user/'.$userid;
        $body = createGet($URL, $header, $body);
        $this->app->response->setBody($body);
    }
    
    private function showSubmissionsHistory($sheetid, $userid){
        
        $header = $this->app->request->getHeader();
        $body = $this->app->request->getBody();
        $URL = $dbURL.'/exerciseSheet/'.$sheetid.'/user/'.$userid.'/history';
        $body = createGet($URL, $header, $body);
        $this->app->response->setBody($body);
    }
    
    private function getSubmissionURL($submissionid){
        
        $header = $this->app->request->getHeader();
        $body = $this->app->request->getBody();
        $URL = $dbURL.'/submission/'.$submissionid;
        $body = createGet($URL, $header, $body);
        $this->app->response->setBody($body);
    }
}

?>