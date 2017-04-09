<?php
/**
 * @file DBExternalId.php contains the DBExternalId class
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
 * @example DB/DBExternalId/ExternalIdSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "ExternalId" table from database
 */
class DBExternalId
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
        $component = new Model('externalid', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an alias for an already existing ExternalId.
     *
     * Called when this component receives an HTTP PUT request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     * The request body should contain a JSON object representing the
     * externalId's new attributes.
     *
     * @param string $exid The alias of the ExternalId that is being updated.
     */
    public function editExternalId( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editExternalId',dirname(__FILE__).'/Sql/EditExternalId.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new ExternalId()),'Model::isProblem',array(new ExternalId()));
    }

    /**
     * Deletes an alias for an already existing ExternalId.
     *
     * Called when this component receives an HTTP DELETE request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     *
     * @param string $exid The alias of the ExternalId that is being deleted.
     */
    public function deleteExternalId( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteExternalId',dirname(__FILE__).'/Sql/DeleteExternalId.sql',$params,201,'Model::isCreated',array(new ExternalId()),'Model::isProblem',array(new ExternalId()));
    }

    /**
     * Adds an alias for an already existing ExternalId.
     *
     * Called when this component receives an HTTP POST request to
     * /externalid/$exid(/) or /externalid/externalid/$exid(/).
     * The request body should contain a JSON object representing the
     * externalId's attributes.
     *
     * @param string $exid The alias of the ExternalId that is being created.
     */
    public function addExternalId( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new ExternalId( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addExternalId',dirname(__FILE__).'/Sql/AddExternalId.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new ExternalId()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract ExternalId data from db answer
                    $res = ExternalId::ExtractExternalId( $inp->getResponse( ), false);
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

    public function getApiProfiles( $callName, $input, $params = array() )
    {   
        $myName = $this->_component->_conf->getName();
        $profiles = array();
        $profiles['readonly'] = GateProfile::createGateProfile(null,'readonly');
        $profiles['readonly']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /externalid/:path+',null));
        
        $profiles['general'] = GateProfile::createGateProfile(null,'general');
        $profiles['general']->setRules($profiles['readonly']->getRules());
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /externalid/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'PUT /externalid/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /externalid/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /platform/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /platform/:path+',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /link/exists/platform',null));
        
        $profiles['develop'] = GateProfile::createGateProfile(null,'develop');
        $profiles['develop']->setRules(array_merge($profiles['general']->getRules(), $this->_component->_com->apiRulesDevelop($myName)));

        ////$profiles['public'] = GateProfile::createGateProfile(null,'public');
        return Model::isOk(array_values($profiles));
    }
}


