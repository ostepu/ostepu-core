<?php
/**
 * @file CSystem.php Contains the CSystem class
 *
 * @author Till Uhlig
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * 
 */
class CSystem
{

    /**
     * REST actions
     *
     * This function contains the REST actions with the assignments to
     * the functions.
     *
     * @param Component $conf component data
     */
    private $_component = null;
    public function __construct( )
    {
        $component = new Model('', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }
    
    public function getTimestamp( $callName, $input, $params = array())
    {
        // returns the currect timestamp
        return Model::isOk(array('timestamp'=>time()));
    }
}
