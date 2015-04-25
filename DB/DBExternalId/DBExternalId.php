<?php 


/**
 * @file DBExternalId.php contains the DBExternalId class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExternalId/ExternalIdSample.json
 * @date 2013-2015
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
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/EditExternalId.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new ExternalId()),'Model::isProblem',array(new ExternalId()));
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
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteExternalId.sql',$params,201,'Model::isCreated',array(new ExternalId()),'Model::isProblem',array(new ExternalId()));  
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
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddExternalId.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new ExternalId()));
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract ExternalId data from db answer
                    $result['content'] = array_merge($result['content'], ExternalId::ExtractExternalId( $inp->getResponse( ), $singleResult));
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

 
