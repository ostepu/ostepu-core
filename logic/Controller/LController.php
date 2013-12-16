<?php 

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';

\Slim\Slim::registerAutoloader();

class LController
{
    $this->app = new \Slim\Slim();
    $this->app->response->headers->set('Content-Type', 'application/json');


    $links = array();
    setLinks('config.ini');
    $DBController = $links["DBControl"];	
    $FSController = $links["FSControl"];

    $this->app->map('/:string+', array($this, 'chooseDestination')) 
    			->via('POST', 'GET', 'PUT', 'DELETE');

    
    /**
     * pick out the config.ini file and store all addresses in $links
     *
     * @param (param)
     */
    private function setLinks($dataName){
        $datei = file($dataName);
        $explodedRow = array();

        foreach ($datei AS $row) {
            $explodedRow = explode(' = ' , $row);			// Trenner in Config.ini definieren
            $links["$explodedRow[0]"] = $explodedRow[1];
        }
    }

    /**
     * select the case of the request based on the available method
     * and send the selected on
     * 
     * @param (param)
     */
    private function chooseCaseOfRequest($method, $URL, $header, $data){
        switch ($method) {
            case 'POST' :
                return createPost($URL, $header, $data);
                break;
                
            case 'PUT' :			
                return createPut($URL, $header, $data);
                break;
                
            case 'GET' :
                return createGet($URL, $header, $data);
                break;
                
            case 'DELETE' :
                return createDelete($URL, $header, $data);
                break;
                
            default :
                return "Fehler";			//Fehlermeldung
        }
    }
       
    private function chooseDestination($string){
    	$method = $this->app->request->getMethod();
    	$body = $this->app->request->getBody();
    	$header = $this->app->request->getHeader();
    	
    	if ($string[0] == "DB") {
            unset($string[0]);
            $URI = //DB-URL;															//URI ergänzen
            foreach ($string as $str) {
                $URI = $URI.'/'.$str;
            }
            $this->app->response
                ->SetBody(chooseCaseOfRequest($method, $URI, $header, $body));	
        } elseif ($string[0] == "FS") {
            unset($string[0]);
            $URI = //FS-URL;															//URI ergänzen
            foreach ($string as $str) {
                $URI = $URI.'/'.$str;
            }
            $this->app->response
                ->SetBody(chooseCaseOfRequest($method, $URI, $header, $body));
        } else {
            $URI = //L-URL
            foreach ($string as $str) {
                $URI = $URI.'/'.$str;
            }
            $this->app->response
                ->SetBody(chooseCaseOfRequest($method, $URI, $header, $body));	
        }
    }
    
    $this->app->run();
    
}

new LController;
?>