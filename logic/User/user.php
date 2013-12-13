<?php

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';
	
\Slim\Slim::registerAutoloader();
	
$_app = new \Slim\Slim();
	
$LController = "";				//Einlesen aus config.ini

//SetUserRights
$_app->put('/user/:id/right', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/user/user/'. $id.'/right';
    $status = createPut($URL, $header, $req);
    $_app->response->setStatus($status);
    
});

//AddUser
$_app->post('', function() use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/user';
    $status = createPost($URL, $header, $req);
    $_app->response->setStatus($status);
    
});

//EditUser
$_app->put('/user/:id', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/user/course/'. $id;
    $status = createPut($URL, $header, $req);
    $_app->response->setStatus($status);
    
});

//GetUsers
$app->get('/user', function() use($app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/user/user';
    $dbAnswer = createGet($URL, $header, $req);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
}); 

//GetUser
$app->get('/user/:id', function($id) use($app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/user/user/'.$id;
    $dbAnswer = createGet($URL, $header, $req);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
}); 
   
?>   