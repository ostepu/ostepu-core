<?php
/**
 * @file DBUser.php Contains the DBUser class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBUser/UserSample.json
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "User" table from database
 */
class DBUser
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
        $component = new Model('user', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits a user.
     *
     * Called when this component receives an HTTP PUT request to
     * /user/$userid(/) or /user/user/$userid(/).
     * The request body should contain a JSON object representing the user's new
     * attributes.
     *
     * @param string $userid The id or the username of the user that is being updated.
     */
    public function editUser( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditUser.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));
    }

    /**
     * Deletes a user (updates the user flag = 0).
     *
     * Called when this component receives an HTTP DELETE request to
     * /user/$userid(/) or /user/user/$userid(/).
     *
     * @param string $userid The id or the username of the user that is being deleted.
     */
    public function removeUser( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteUser.sql',$params,201,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));  
    }

    /**
     * Deletes a user permanent.
     *
     * Called when this component receives an HTTP DELETE request to
     * /user/$userid/permanent(/) or /user/user/$userid/permanent(/).
     *
     * @param string $userid The id or the username of the user that is being deleted.
     */
    public function removeUserPermanent( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteUserPermanent.sql',$params,201,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));
    }

    /**
     * Adds a user and then returns the created user.
     *
     * Called when this component receives an HTTP POST request to
     * /user(/).
     * The request body should contain a JSON object representing the new user.
     */
    public function addUser( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new User( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/AddUser.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new User()));
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract user data from db answer
                    $result['content'] = array_merge($result['content'], User::ExtractUser( $inp->getResponse( ), $singleResult));
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
    
    public function getSamplesInfo( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    foreach($inp->getResponse( ) as $key => $value)
                        foreach($value as $key2 => $value2){
                            $result['content'][] = $value2;
                        }
                    $result['status'] = 200;
                }
            }
            return $result;
        };
        
        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($callName, $params, '', 200, $positive,  array(), 'Model::isProblem', array(), 'Query');
    }
    
    public function postSamples( $callName, $input, $params = array() )
    {   
        set_time_limit(0);
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/Samples.sql',$params,201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()));  
    }
}
?>