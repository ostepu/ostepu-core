<?php 


/**
 * @file DBGroup.php contains the DBGroup class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBGroup/GroupSample.json
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "Group" table from database
 */
class DBGroup
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
        $component = new Model('group', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits the group the user is part of regarding the given
     * exercise sheet.
     *
     * Called when this component receives an HTTP PUT request to
     * /group/user/$userid/exercisesheet/$esid(/).
     * The request body should contain a JSON object representing
     * the group's new attributes.
     *
     * @param int $userid The id of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function editGroup( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditGroup.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Group()),'Model::isProblem',array(new Group()));
    }

    /**
     * Deletes the group the user is part of regarding the given
     * exercise sheet.
     *
     * Called when this component receives an HTTP DELETE request to
     * /group/user/$userid/exercisesheet/$esid(/).
     *
     * @param int $userid The id of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function deleteGroup( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteGroup.sql',$params,201,'Model::isCreated',array(new Group()),'Model::isProblem',array(new Group()));  
    }

    /**
     * Adds a new group.
     *
     * Called when this component receives an HTTP POST request to
     * /group(/).
     * The request body should contain a JSON object representing
     * the group's attributes.
     */
    public function addGroup( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new Group( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/AddGroup.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Group()));
    }
    
    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Group data from db answer
                    $result['content'] = array_merge($result['content'], Group::ExtractGroup( $inp->getResponse( ), $singleResult));
                    $result['status'] = 200;
                }
            }
            return $result;
        };
        
        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($linkName, $params, '', 200, $positive, array($singleResult), 'Model::isProblem', array(), 'Query');
    }

    public function getMatch($callName, $input, $params = array())
    {
        return $this->get($callName,$callName,$params);
    }
    public function getMatchSingle($callName, $input, $params = array())
    {
        return $this->get($callName,$callName,$params,true,false);
    }
    
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeletePlatform.sql',array(),201,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddPlatform.sql',array('object' => $input),201,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
}

 
