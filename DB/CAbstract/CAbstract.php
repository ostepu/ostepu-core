<?php
/**
 * @file CAbstract.php contains the CAbstract class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

require_once ( dirname(__FILE__) . '/../../Assistants/vendor/Slim/Slim/Slim.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Structures.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Request.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBRequest.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/DBJson.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/CConfig.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/Logger.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract ...
 *
 */
class CAbstract
{

    /**
     * the component constructor
     */
    public function __construct( )
    {
        // runs the CConfig
        $com = new CConfig( 'link,definition' );

        // runs the CAbstract
        if ( $com->used( ) ) {
            $conf = $com->loadConfig( dirname(__FILE__).'/'.(isset($com->pre) ?  $com->pre : '') );
            $options = $conf->getOption();
            $confFile = $com->confFile;
            if (isset($options)){
                $data = explode(',',$options);
                foreach ($data as $dat){
                    $dat = explode('=',$dat);
                    if ($dat[0] == 'confPath')
                        copy($confFile, dirname(__FILE__) . '/../../'.$dat[1]);
                }
            }
        }

        return;
    }
} 