<?php
/**
 * @file FSControl.php contains the FSControl class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/Controller3.php' );

\Slim\Slim::registerAutoloader( );

/**
 * The controller of the filesystem.
 */
class FSControl extends Controller3
{

    /**
     * @var string $_prefix the prefixes the class works with (comma-separated)
     */
    protected static $_prefix = '';

    /**
     * the $_prefix getter
     *
     * @return the value of $_prefix
     */
    public static function getPrefix( )
    {
        return FSControl::$_prefix;
    }

    /**
     * the $_prefix setter
     *
     * @param string $value the new value for $_prefix
     */
    public static function setPrefix( $value )
    {
        FSControl::$_prefix = $value;
    }
} 