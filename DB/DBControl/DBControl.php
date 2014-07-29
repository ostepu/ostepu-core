<?php 


/**
 * @file DBControl.php contains the DBControl class
 *
 * @author Till Uhlig
 * @date 2013-2014
 */

require_once ( '../../Assistants/Slim/Slim.php' );
include_once ( '../../Assistants/Structures.php' );
include_once ( '../../Assistants/CConfig.php' );
include_once ( '../../Assistants/Request.php' );
include_once ( '../../Assistants/Controller.php' );
include_once ( '../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to forwards requests into the heap of database components
 *
 * @author Till Uhlig
 */
class DBControl extends Controller
{

    /**
     * @var string $_prefix the prefixes, the class works with (comma separated)
     */
    protected static $_prefix = '';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return DBControl::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        DBControl::$_prefix = $value;
    }
} 
?>