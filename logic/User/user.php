<?php

require 'Slim/Slim.php';
include 'include/Helpers.php';
	
\Slim\Slim::registerAutoloader();
	
$_app = new \Slim\Slim();
	
$LController = "";				//Einlesen aus config.ini

//SetUserRights
$_app->put('/user/:id/right', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/user/user/'. $id.'/right';
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);
    
});

//AddUser
$_app->post('', function() use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/user';
    $status = http_post_data($URL, $req);
    $_app->response->setStatus($status);
    
});

//EditUser
$_app->put('/user/:id', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/user/course/'. $id;
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);
    
});

//GetUsers
$app->get('/user', function() use($app){

    $URL = $LController.'/DB/user/user';
    $dbAnswer = http_get($URL);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
}); 

//GetUser
$app->get('/user/:id', function($id) use($app){

    $URL = $LController.'/DB/user/user/'.$id;
    $dbAnswer = http_get($URL);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
}); 
   
?>   