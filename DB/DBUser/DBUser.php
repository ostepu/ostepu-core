<?php
/**
 * @file DBUser.php Contains the DBUser class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBUser/UserSample.json
 * @date 2013-2014
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
    public function editUser( $input, $userid )
    {
        $userid = DBJson::mysql_real_escape_string( $userid );
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditUser.sql',array('userid' => $userid, 'values' => $input->getInsertData( )),200,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));
    }

    /**
     * Deletes a user (updates the user flag = 0).
     *
     * Called when this component receives an HTTP DELETE request to
     * /user/$userid(/) or /user/user/$userid(/).
     *
     * @param string $userid The id or the username of the user that is being deleted.
     */
    public function removeUser( $input, $userid )
    {
        $userid = DBJson::mysql_real_escape_string( $userid );
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteUser.sql',array('userid' => $userid),200,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));  
    }

    /**
     * Deletes a user permanent.
     *
     * Called when this component receives an HTTP DELETE request to
     * /user/$userid/permanent(/) or /user/user/$userid/permanent(/).
     *
     * @param string $userid The id or the username of the user that is being deleted.
     */
    public function removeUserPermanent( $input, $userid )
    {
        $userid = DBJson::mysql_real_escape_string( $userid );
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteUserPermanent.sql',array('userid' => $userid),200,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));
    }

    /**
     * Adds a user and then returns the created user.
     *
     * Called when this component receives an HTTP POST request to
     * /user(/).
     * The request body should contain a JSON object representing the new user.
     */
    public function addUser( $input )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new User( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/AddUser.sql',array( 'values' => $input->getInsertData( )),200,$positive,array(),'Model::isProblem',array(new User()));
    }

    public function get( $functionName,$sqlFile,$params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            $input = $input[count($input)-1];
            $result = Model::isEmpty();
            if ( $input->getNumRows( ) > 0 ){
                // extract user data from db answer
                $result['content'] = User::ExtractUser( $input->getResponse( ), $singleResult);
                $result['status'] = 200;            
            }
            return $result;
        };
        
        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->callSqlTemplate('out',$sqlFile,$params,200,$positive,array($singleResult),'Model::isProblem',array(),$checkSession);
    }

    /**
     * Returns all users.
     *
     * Called when this component receives an HTTP GET request to
     * /user(/) or /user/user(/).
     */
    public function getUsers( $input )
    {
        return $this->get('GetUsers',dirname(__FILE__).'/Sql/GetUsers.sql',array() );
    }

    /**
     * Returns a user.
     *
     * Called when this component receives an HTTP GET request to
     * /user/$userid(/) or user/user/$userid(/).
     *
     * @param string $userid The id or the username of the user that should be returned.
     */
    public function getUser( $input, $userid )
    {
        return $this->get('GetUser',dirname(__FILE__).'/Sql/GetUser.sql',array("userid"=>$userid),true,false);
    }

    /**
     * Increases the number of failed login attempts of a user and then returns the user.
     *
     * Called when this component receives an HTTP GET request to
     * /user/$userid/IncFailedLogin(/) or /user/user/$userid/IncFailedLogin(/).
     *
     * @param string $userid The id or the username of the user.
     */
    public function getIncreaseUserFailedLogin( $input, $userid )
    {
        return $this->get('GetIncreaseUserFailedLogin',dirname(__FILE__).'/Sql/GetIncreaseUserFailedLogin.sql',array("userid"=>$userid),true,false);
    }

    /**
     * Returns all users of a course.
     *
     * Called when this component receives an HTTP GET request to
     * /user/course/$courseid(/).
     *
     * @param int $courseid The id or the course.
     */
    public function getCourseMember( $input, $courseid )
    {
        return $this->get('GetCourseMember',dirname(__FILE__).'/Sql/GetCourseMember.sql',array("courseid"=>$courseid));
    }

    /**
     * Returns all members of the group the user is part of
     * regarding a specific exercise sheet.
     *
     * Called when this component receives an HTTP GET request to
     * /user/group/user/$userid/exercisesheet/$esid(/).
     *
     * @param string $userid The id or the username of the user.
     * @param int $esid The id of the exercise sheet.
     */
    public function getGroupMember($input,$userid,$esid)
    {
        return $this->get('GetGroupMember',dirname(__FILE__).'/Sql/GetGroupMember.sql',array("esid"=>$esid,"statusid"=>$statusid) );
    }

    /**
     * Returns all users with a given status.
     *
     * Called when this component receives an HTTP GET request to
     * /user/status/$statusid(/).
     *
     * @param string $statusid The status the users should have.
     */
    public function getUserByStatus($input,$statusid )
    {
        return $this->get('GetUserByStatus',dirname(__FILE__).'/Sql/GetUserByStatus.sql',array("statusid"=>$statusid) );
    }

    /**
     * Returns all users with a given status which are members of a
     * specific course.
     *
     * Called when this component receives an HTTP GET request to
     * /course/$courseid/status/$statusid(/).
     *
     * @param string $courseid The courseid of the course.
     * @param string $statusid The status the users should have.
     */
    public function getCourseUserByStatus($input,$courseid,$statusid)
    {
        return $this->get('GetCourseUserByStatus',dirname(__FILE__).'/Sql/GetCourseUserByStatus.sql',array("courseid"=>$courseid,"statusid"=>$statusid));
    }
    
    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( $input )
    {
        return $this->get('GetExistsPlatform',dirname(__FILE__).'/Sql/GetExistsPlatform.sql',array(),true,false);
    }
    
    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $input )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeletePlatform.sql',array(),200,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $input )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddPlatform.sql',array('object' => $input),200,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
}
?>