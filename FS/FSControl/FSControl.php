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
class FSControl extends Controller
{
    protected static $_prefix = "";
    
    /**
     * (description)
     */
    public static function getPrefix()
    {
        return FSControl::$_prefix;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function setPrefix($value)
    {
        FSControl::$_prefix = $value;
    }
}

\Slim\Slim::registerAutoloader();

$com = new CConfig(FSControl::getPrefix()); 

if (!$com->used())
    new FSControl($com->loadConfig());
?>