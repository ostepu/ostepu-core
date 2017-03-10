<?php
/**
 * @file DBSelectedSubmission.php contains the DBSelectedSubmission class
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
 * @example DB/DBSelectedSubmission/SelectedSubmissionSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "SelectedSubmission" table from database
 */
class DBSelectedSubmission
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
        $component = new Model('selectedsubmission', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP PUT request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $eid The id of the exercise.
     */
    public function editSelectedSubmission( $callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('editSelectedSubmission',dirname(__FILE__).'/Sql/EditSelectedSubmission.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new SelectedSubmission()),'Model::isProblem',array(new SelectedSubmission()));
    }

    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP PUT request to
     * /selectedsubmission/submission/$suid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     *
     * @param string $suid The id or the submission.
     */
    public function editSubmissionSelectedSubmission( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editSubmissionSelectedSubmission',dirname(__FILE__).'/Sql/EditSubmissionSelectedSubmission.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new SelectedSubmission()),'Model::isProblem',array(new SelectedSubmission()));
    }

    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $eid The id of the exercise.
     */
    public function deleteSelectedSubmission($callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('deleteSelectedSubmission',dirname(__FILE__).'/Sql/DeleteSelectedSubmission.sql',$params,201,'Model::isCreated',array(new SelectedSubmission()),'Model::isProblem',array(new SelectedSubmission()));
    }

    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/user/$userid/exercisesheet/$esid(/).
     *
     * @param string $userid The id or the user which leads the group.
     * @param int $esid The id of the exercise sheet.
     */
    public function deleteUserSheetSelectedSubmission($callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('deleteUserSheetSelectedSubmission',dirname(__FILE__).'/Sql/DeleteUserSheetSelectedSubmission.sql',$params,201,'Model::isCreated',array(new SelectedSubmission()),'Model::isProblem',array(new SelectedSubmission()));
    }

    /**
     * Unsets the submission that should be marked.
     *
     * Called when this component receives an HTTP DELETE request to
     * /selectedsubmission/submission/$suid(/).
     *
     * @param string $suid The id or the submission.
     */
    public function deleteSubmissionSelectedSubmission( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteSubmissionSelectedSubmission',dirname(__FILE__).'/Sql/DeleteSubmissionSelectedSubmission.sql',$params,201,'Model::isCreated',array(new SelectedSubmission()),'Model::isProblem',array(new SelectedSubmission()));
    }

    /**
     * Sets the submission that should be marked.
     *
     * Called when this component receives an HTTP POST request to
     * /selectedsubmission/leader/$userid/exercise/$eid(/).
     * The request body should contain a JSON object representing the new selectedSubmission.
     */
    public function addSelectedSubmission($callName, $input, $params = array())
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new SelectedSubmission( );
            //$obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addSelectedSubmission',dirname(__FILE__).'/Sql/AddSelectedSubmission.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new SelectedSubmission()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract SelectedSubmission data from db answer
                    $res = SelectedSubmission::ExtractSelectedSubmission( $inp->getResponse( ), false);
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
        return $this->_component->callSqlTemplate('deletePlatform',dirname(__FILE__).'/Sql/DeletePlatform.sql',array(),201,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }

    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('addPlatform',dirname(__FILE__).'/Sql/AddPlatform.sql',array('object' => $input),201,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
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
        return $this->_component->callSqlTemplate('postSamples',dirname(__FILE__).'/Sql/Samples.sql',$params,201,'Model::isCreated',array(new SelectedSubmission()),'Model::isProblem',array(new SelectedSubmission()));
    }

    public function getApiProfiles( $callName, $input, $params = array() )
    {   
        $myName = $this->_component->_conf->getName();
        $profiles = array();
        $profiles['readonly'] = GateProfile::createGateProfile(null,'readonly');
        $profiles['readonly']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /selectedsubmission/:path+',null));
        
        $profiles['general'] = GateProfile::createGateProfile(null,'general');
        $profiles['general']->setRules($profiles['readonly']->getRules());
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /selectedsubmission/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'PUT /selectedsubmission/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /selectedsubmission/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /platform',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /platform',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /link/exists/platform',null));
        
        $profiles['develop'] = GateProfile::createGateProfile(null,'develop');
        $profiles['develop']->setRules(array_merge($profiles['general']->getRules(), $this->_component->_com->apiRulesDevelop($myName)));

        ////$profiles['public'] = GateProfile::createGateProfile(null,'public');
        return Model::isOk(array_values($profiles));
    }
}


