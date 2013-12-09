<?php

require 'Slim/Slim.php';
include 'include/Helpers.php';
	
\Slim\Slim::registerAutoloader();
	
$_app = new \Slim\Slim();
	
$LController = "";				//Einlesen aus config.ini

//SetCourse
$_app->post('', function() use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/course';
    $status = http_post_data($URL, $req);
    $_app->response->setStatus($status);
    
});

//EditCourse

$_app->put('/course/:id', function($id) use($_app){

	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/course/course/'. $id;
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);
    
});

//DeleteCourse

$_app->delete('/course/:id', function($id) use($_app){

    $URL = $LController.'/DB/course/course/'. $id;
    $status = http_delete($URL);
    $_app->response->setStatus($status);
    
});
    
}

//AddCourseMember

$_app->post('/course/:id/user/:id', function($courseid, $userid) use($_app){
	
	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/course/'.$courseid.'/course/'.$userid;
    $status = http_put_data($URL, $req);
    $app->response->setStatus($status);
    
});

//GetCourseMember

$_app->get('/course/:id/user', function($id) use($_app){

    $URL = $LController.'/DB/course/'.$id.'/user';
    $dbAnswer = http_get($URL);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
});


//GetCourses
$app->get('/user/:id', function($id) use($app){

    $URL = $LController.'/DB/course/user/'.$id;
    $dbAnswer = http_get($URL);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);
});    
?>   