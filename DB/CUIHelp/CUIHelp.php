<?php
/**
 * @file CUIHelp.php Contains the CUIHelp class
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * ???
 */
class CUIHelp
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
    private $config = array();
    public function __construct( )
    {
        $component = new Model('', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }
}
