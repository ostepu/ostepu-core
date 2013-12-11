<?php
/**
 * @file (filename)
 * (description)
 */ 
 
require_once('Include/Slim/Slim.php');
include_once('Include/Structures.php');
include_once('Include/CConfig.php');
include_once('Include/Request.php');
include_once('Include/Controller.php');

/**
 * (description)
 */
class FsControl extends Controller
{
    protected static $_prefix = "";
}

\Slim\Slim::registerAutoloader();

$com = new CConfig(FsControl::getPrefix()); 

if (!$com->used())
    new FsControl($com->loadConfig());
?>