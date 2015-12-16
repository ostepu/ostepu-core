<?php


/**
 * @file LSample.php contains the LSample class
 *
 * @author Till Uhlig
 * @date 2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "LSample" table from database
 */
class LSamples
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
        $component = new Model('sample', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    public function generateSamples( )
    {echo "OK";
        /*$positive = function($input) {
            // sets the new auto-increment id
            $obj = new Course( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddCourse.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Course()));*/
    }
}

