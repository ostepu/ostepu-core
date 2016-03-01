<?php


/**
 * @file DBSession.php contains the DBSession class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBSession/SessionSample.json
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "Session" table from database
 */
class DBSession
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
        $component = new Model('session', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits a session identified by a sessionId.
     *
     * Called when this component receives an HTTP PUT request to
     * /session/session/$seid(/) or /session/$seid(/).
     * The request body should contain a JSON object representing the
     * sessions's new attributes.
     *
     * @param string $seid The id of the session which is being updated.
     */
    public function editSession( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditSession.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Session()),'Model::isProblem',array(new Session()));
    }

    /**
     * Deletes a session identified by a sessionId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /session/session/$seid(/) or /session/$seid(/).
     *
     * @param string $seid The id of the session which is being deleted.
     */
    public function deleteSession( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteSession.sql',$params,201,'Model::isCreated',array(new Session()),'Model::isProblem',array(new Session()));
    }

    /**
     * Edits a session identified by an userId.
     *
     * Called when this component receives an HTTP PUT request to
     * /session/user/$userid(/).
     * The request body should contain a JSON object representing the
     * sessions's new attributes.
     *
     * @param int $userid The id of the user that is being updated.
     */
    public function editUserSession( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditUserSession.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new User()),'Model::isProblem',array(new User()));
    }

    /**
     * Deletes a session identified by an userId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /session/user/$userid(/).
     *
     * @param int $userid The id of the user that is being deleted.
     */
    public function deleteUserSession( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteUserSession.sql',$params,201,'Model::isCreated',array(new Session()),'Model::isProblem',array(new Session()));
    }

    /**
     * Adds a session.
     *
     * Called when this component receives an HTTP POST request to
     * /session(/).
     * The request body should contain a JSON object representing the new session.
     */
    public function addSession( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new Session( );
            //$obj->setSession( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };

        $userid = $input->getUser( );
        $sessionid = $input->getSession( );
        $sessionid = DBJson::mysql_real_escape_string( $sessionid );
        $userid = DBJson::mysql_real_escape_string( $userid );

        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddSession.sql',array('sessionid' => $sessionid,'userid' => $userid, 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Session()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Session data from db answer
                    $res = Session::ExtractSession( $inp->getResponse( ), false);
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
        $sql=array();
        for($i=1;$i<=$params['amount'];$i++){
            $rr = md5($i);
            $obj = Session::createSession($i,$rr);
            $sql[]="insert ignore into Session SET ".$obj->getInsertData( ).";";
            if ($i%1000==0){
                $this->_component->callSql('out2',implode('',$sql),201,'Model::isCreated',array(),'Model::isProblem',array(new File()));
                $sql=array();
            }
        }
        $this->_component->callSql('out2',implode('',$sql),201,'Model::isCreated',array(),'Model::isProblem',array(new File()));

        return Model::isCreated();
    }
}


