<?php
/**
* @file (filename)
* %(description)
*/ 

require 'Slim/Slim.php';
include 'include/structures.php';
include 'include/DBCourse.php';
include 'include/DBUser.php';
include 'include/Json.php';
include 'include/DBRequest.php';

\Slim\Slim::registerAutoloader();

new DBCourse;
new DBUser;
?>