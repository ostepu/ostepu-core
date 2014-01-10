<?php
/**
 * @file DBControl.php contains the DBControl class
 */ 
 
require_once('Include/Slim/Slim.php');
include_once('Include/Structures.php');
include_once('Include/CConfig.php');
include_once('Include/Request.php');
include_once('Include/Controller.php');
include_once( 'Include/Logger.php' );

/**
 * A class, to forwards requests into the heap of database components
 *
 * @author Till Uhlig
 */
class DBControl extends Controller
{
    /**
     * @var $_prefix the prefix, the class works with
     */ 
    protected static $_prefix = "";
    
    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */ 
    public static function getPrefix()
    {
        return DBControl::$_prefix;
    }
    
    /**
     * the $_prefix setter
     *
     * @param $value the new value for $_prefix
     */
    public static function setPrefix($value)
    {
        DBControl::$_prefix = $value;
    }
}

\Slim\Slim::registerAutoloader();

// runs the CConfig
$com = new CConfig(DBControl::getPrefix()); 

// runs the DBControl
if (!$com->used())
    new DBControl($com->loadConfig());
?>