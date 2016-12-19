<?php
/**
 * @file DBGate.php Contains the DBGate class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "User" table from database
 */
class DBGate
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
        $component = new Model('gateprofile,gaterule,gateauth',
                               dirname(__FILE__), $this, false, false, array('cloneable'=>true,
                                                                             'defaultParams'=>array('ruleProfile'=>'',
                                                                                                    'authProfile'=>''),
                                                                             'addOptionsToParametersAsPostfix'=>true,
                                                                             'addProfileToParametersAsPostfix'=>true));
        $this->_component=$component;
        $component->run();
    }

    public function editGateProfile( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editGateProfile',dirname(__FILE__).'/Sql/EditGateProfile.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new GateProfile()),'Model::isProblem',array(new GateProfile()));
    }

    public function deleteGateProfile( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteGateProfile',dirname(__FILE__).'/Sql/DeleteGateProfile.sql',$params,201,'Model::isCreated',array(new GateProfile()),'Model::isProblem',array(new GateProfile()));
    }

    public function deleteGateProfileByName( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteGateProfileByName',dirname(__FILE__).'/Sql/DeleteGateProfileByName.sql',$params,201,'Model::isCreated',array(new GateProfile()),'Model::isProblem',array(new GateProfile()));
    }

    public function addGateProfile( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new GateProfile( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addGateProfile',dirname(__FILE__).'/Sql/AddGateProfile.sql',array_merge($params,array( 'values' => $input->getInsertData( ))),201,$positive,array(),'Model::isProblem',array(new GateProfile()),false);
    }

    public function editGateRule( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editGateRule',dirname(__FILE__).'/Sql/EditGateRule.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new GateRule()),'Model::isProblem',array(new GateRule()));
    }

    public function deleteGateRule( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteGateRule',dirname(__FILE__).'/Sql/DeleteGateRule.sql',$params,201,'Model::isCreated',array(new GateRule()),'Model::isProblem',array(new GateRule()));
    }

    public function addGateRule( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new GateRule( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addGateRule',dirname(__FILE__).'/Sql/AddGateRule.sql',array_merge($params,array( 'values' => $input->getInsertData( ))),201,$positive,array(),'Model::isProblem',array(new GateRule()),false);
    }

    public function editGateAuth( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editGateAuth',dirname(__FILE__).'/Sql/EditGateAuth.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new GateAuth()),'Model::isProblem',array(new GateAuth()));
    }

    public function deleteGateAuth( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteGateAuth',dirname(__FILE__).'/Sql/DeleteGateAuth.sql',$params,201,'Model::isCreated',array(new GateAuth()),'Model::isProblem',array(new GateAuth()));
    }

    public function addGateAuth( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new GateAuth( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addGateAuth',dirname(__FILE__).'/Sql/AddGateAuth.sql',array_merge($params,array( 'values' => $input->getInsertData( ))),201,$positive,array(),'Model::isProblem',array(new GateAuth()),false);
    }
    
    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract profile data from db answer
                    $res = GateProfile::ExtractGateProfile( $inp->getResponse( ), false);
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
        return $this->_component->callSqlTemplate('deletePlatform',dirname(__FILE__).'/Sql/DeletePlatform.sql',$params,201,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }

    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('addPlatform',dirname(__FILE__).'/Sql/AddPlatform.sql',array_merge($params,array('object' => $input)),201,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
}
