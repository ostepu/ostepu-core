<?php

require 'Slim/Slim.php';
include 'include/Helpers.php';
	
\Slim\Slim::registerAutoloader();
	
$_app = new \Slim\Slim();

$LController = "";				//Einlesen aus config.ini


//InvitateInGroup

$_app->put('/user/:id', function($userid) use($_app)
{
	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/user/'.$userid;
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);

})

//JoinGroup

$_app->put('/accept', function() use($_app)
{
	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/accept';
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);

})

//LeaveGroup

$_app->put('/user/:id/exit', function($userid) use($_app)
{
	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/user/'.$userid.'/exit';
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);

})

//EjectFromGroup

$_app->put('/user/:id/deleteMember', function($userid) use($_app)
{
	$req = \Slim\Slim::getInstance()->request()->getBody();
    $URL = $LController.'/DB/user/'.$userid.'/deletMember';
    $status = http_put_data($URL, $req);
    $_app->response->setStatus($status);

})

//GetGroup

$_app->get('/user/:id/exerciseSheet/:id', function($userid, $sheetid) use($_app)
{
    $URL = $LController.'/DB/user/'.$userid.'/exerciseSheet/'.$sheetid;
    $dbAnswer = http_get($URL);
    $_app->response->headers->set('Content-Type', 'application/json');
    $_app->response->setStatus(200);
    $_app->response->setBody($dbAnswer);

})
?>