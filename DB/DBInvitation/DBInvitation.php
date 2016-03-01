<?php
/**
 * @file DBInvitation.php contains the DBInvitation class
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
 *
 * @example DB/DBInvitation/InvitationSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "ExerciseType" table from database
 */
class DBInvitation
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
        $component = new Model('invitation', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an invitation.
     *
     * Called when this component receives an HTTP PUT request to
     * /invitation/user/$userid/exercisesheet/$esid/user/$memberid(/)
     * The request body should contain a JSON object representing the
     * invitations's new attributes.
     *
     * @param int $userid The id of the user that invites a new user.
     * @param int $esid The id of the exercise sheet the group belongs to.
     * @param int $memberid The id of the user that is invited.
     */
    public function editInvitation( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/EditInvitation.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Invitation()),'Model::isProblem',array(new Invitation()));
    }

    /**
     * Deletes an invitation.
     *
     * Called when this component receives an HTTP DELETE request to
     * /invitation/user/$userid/exercisesheet/$esid/user/$memberid(/)
     *
     * @param int $userid The id of the user that invites a new user.
     * @param int $esid The id of the exercise sheet the group belongs to.
     * @param int $memberid The id of the user that is invited.
     */
    public function deleteInvitation( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteInvitation.sql',$params,201,'Model::isCreated',array(new Invitation()),'Model::isProblem',array(new Invitation()));
    }

    /**
     * Adds an invitation.
     *
     * Called when this component receives an HTTP POST request to
     * /invitation(/).
     * The request body should contain a JSON object representing the
     * invitations's attributes.
     */
    public function addInvitation( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new Invitation( );
            //$obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddInvitation.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Invitation()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Invitation data from db answer
                    $res = Invitation::ExtractInvitation( $inp->getResponse( ), false);
                    $result['content'] = array_merge($result['content'], (is_array($res) ? $res : array($res)));
                    $result['status'] = 200;
                }
            }
            return $result;
        };

        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($linkName, $params, '', 200, $positive, array(), 'Model::isProblem', array(), 'Query');
    }

    public function getMatch($callName, $input, $params = array())
    {
        return $this->get($callName,$callName,$params);
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


