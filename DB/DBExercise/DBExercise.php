<?php
/**
 * @file DBExercise.php contains the DBExercise class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 *
 * @example DB/DBExercise/ExerciseSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "Exercise" table from database
 */
class DBExercise
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
        $component = new Model('exercise', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an exercise.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercise/$eid(/) or /exercise/exercise/$eid(/).
     * The request body should contain a JSON object representing the exercise's new
     * attributes.
     *
     * @param int $eid The id of the exercise that is beeing updated.
     */
    public function editExercise( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editExercise',dirname(__FILE__).'/Sql/EditExercise.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Exercise()),'Model::isProblem',array(new Exercise()));
    }

    /**
     * Deletes an exercise.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercise/$eid(/) or /exercise/exercise/$eid(/).
     *
     * @param int $eid The id of the exercise that is beeing deleted.
     */
    public function deleteExercise( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteExercise',dirname(__FILE__).'/Sql/DeleteExercise.sql',$params,201,'Model::isCreated',array(new Exercise()),'Model::isProblem',array(new Exercise()));
    }

    /**
     * Adds an exercise.
     *
     * Called when this component receives an HTTP POST request to
     * /exercise(/).
     * The request body should contain a JSON object representing the exercise's
     * attributes.
     */
    public function addExercise( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new Exercise( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addExercise',dirname(__FILE__).'/Sql/AddExercise.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Exercise()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract exercise data from db answer
                    $res = Exercise::ExtractExercise( $inp->getResponse( ), false);
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
        return $this->_component->callSqlTemplate('postSamples',dirname(__FILE__).'/Sql/Samples.sql',$params,201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()));
    }

    public function getApiProfiles( $callName, $input, $params = array() )
    {   
        $myName = $this->_component->_conf->getName();
        $profiles = array();
        $profiles['readonly'] = GateProfile::createGateProfile(null,'readonly');
        $profiles['readonly']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /exercise/:path+',null));
        
        $profiles['general'] = GateProfile::createGateProfile(null,'general');
        $profiles['general']->setRules($profiles['readonly']->getRules());
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /exercise/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'PUT /exercise/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /exercise/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /platform/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /platform/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /link/exists/platform',null));
        
        $profiles['develop'] = GateProfile::createGateProfile(null,'develop');
        $profiles['develop']->setRules(array_merge($profiles['general']->getRules(), $this->_component->_com->apiRulesDevelop($myName)));

        ////$profiles['public'] = GateProfile::createGateProfile(null,'public');
        return Model::isOk(array_values($profiles));
    }
}


