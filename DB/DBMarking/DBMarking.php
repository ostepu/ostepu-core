<?php
/**
 * @file DBMarking.php contains the DBMarking class
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @example DB/DBMarking/MarkingSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "Marking" table from database
 */
class DBMarking
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
        $component = new Model('marking', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits a marking.
     *
     * Called when this component receives an HTTP PUT request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     * The request body should contain a JSON object representing the marking's new
     * attributes.
     *
     * @param int $mid The id of the marking that is being updated.
     */
    public function editMarking( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditMarking.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Marking()),'Model::isProblem',array(new Marking()));
    }


    /**
     * Deletes a marking.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/$mid(/) or /marking/marking/$mid(/).
     *
     * @param int $mid The id of the marking that is being deleted.
     */
    public function deleteMarking( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteMarking.sql',$params,201,'Model::isCreated',array(new Marking()),'Model::isProblem',array(new Marking()));
    }

    /**
     * Deletes markings.
     *
     * Called when this component receives an HTTP DELETE request to
     * /marking/Markingsheet/$esid(/) or /marking/marking/Markingsheet/$esid(/).
     *
     * @param int $esid The id of the Marking sheet of the markings that are being deleted.
     */
    public function deleteSheetMarkings( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteSheetMarkings.sql',$params,201,'Model::isCreated',array(new Marking()),'Model::isProblem',array(new Marking()));
    }

    /**
     * Adds a marking.
     *
     * Called when this component receives an HTTP POST request to
     * /marking(/).
     * The request body should contain a JSON object representing the
     * marking's attributes.
     */
    public function addMarking( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new Marking( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/AddMarking.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Marking()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Marking data from db answer
                    $res = Marking::ExtractMarking( $inp->getResponse( ), false);
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
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/Samples.sql',$params,201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()));
    }
}


