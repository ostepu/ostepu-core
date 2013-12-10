<?php
/**
 * @file (filename)
 * %(description)
 */ 

require 'Include/Slim/Slim.php';
include_once( 'Include/structures.php' );
include_once( 'DbCourse.php' );
include_once( 'DbUser.php' );
include_once( 'Include/DbJson.php' );
include_once( 'Include/DbRequest.php' );
include_once( 'Include/Com.php' );

\Slim\Slim::registerAutoloader();
if (!(new CConf(""))->used()){
    new DbCourse;
    new DbUser;
}
?>