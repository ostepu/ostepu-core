<?php
/**
 *
 * @author Till Uhlig
 * @date 2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * ???
 */
class CGate extends Model
{

    /**
     * ???
     */
    public function __construct( )
    {
        parent::__construct('', dirname(__FILE__), $this);
        $this->run();
    }

    /**
     * ???
     */
    public function request( $callName, $input, $params = array() )
    {
        $order = implode('/',$params['path'];
        return $this->callByURI('request','/'.$order,array(),$input,201,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));
    }
}
