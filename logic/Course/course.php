<?php

require 'Slim/Slim.php';
include 'include/Assistants/request/createRequest.php';
	
\Slim\Slim::registerAutoloader();
	
$_app = new \Slim\Slim();
	
$LController = "";				//Einlesen aus config.ini

//SetCourse
$_app->post('', function() use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/course';
    $status = createPost($URL, $header, $req);
    $_app->response->setStatus($status);
    
});

//EditCourse

$_app->put('/course/:id', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/course/course/'. $id;
    $status = createPut($URL, $header, $req);
    $_app->response->setStatus($status);
    
});

//DeleteCourse

$_app->delete('/course/:id', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/course/course/'. $id;
    $status = createDelete($URL, $header, $req);
    $_app->response->setStatus($status);
    
});
    
}

//AddCourseMember

$_app->post('/course/:id/user/:id', function($courseid, $userid) use($_app){
	
	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/course/'.$courseid.'/course/'.$userid;
    $status = createPut($URL, $header, $req);
    $app->response->setStatus($status);
    
});

//GetCourseMember

$_app->get('/course/:id/user', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/course/'.$id.'/user';
    $dbAnswer = createGet($URL, $header, $req);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
});


//GetCourses
$app->get('/user/:id', function($id) use($app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
	$header = \Slim\Slim::getInstance()->request()->getHeader();
    $URL = $LController.'/DB/course/user/'.$id;
    $dbAnswer = createGet($URL, $header, $req);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
});    
?>   