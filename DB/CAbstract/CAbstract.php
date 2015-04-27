<?php 


/**
 * @file CAbstract.php contains the CAbstract class
 *
 * @author Till Uhlig
 * @date 2013-2014
 */

require_once ( dirname(__FILE__) . '/../../Assistants/Slim/Slim.php' );
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

        // runs the DBSubmission
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