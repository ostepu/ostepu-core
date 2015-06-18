<?php 


/**
 * @file DBSubmission.php contains the DBSubmission class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBSubmission/SubmissionSample.json
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "Submission" table from database
 */
class DBSubmission
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
        $component = new Model('submission', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits a submission.
     *
     * Called when this component receives an HTTP PUT request to
     * /submission/submission/$suid(/) or /submission/$suid(/).
     * The request body should contain a JSON object representing
     * submission's new attributes.
     *
     * @param int $suid The id of the submission which is being updated.
     */
    public function editSubmission( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditSubmission.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Submission()),'Model::isProblem',array(new Submission()));
    }

    /**
     * Deletes a submission.
     *
     * Called when this component receives an HTTP DELETE request to
     * /submission/submission/$suid(/) or /submission/$suid(/).
     *
     * @param int $suid The id of the submission which is being deleted.
     */
    public function deleteSubmission( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteSubmission.sql',$params,201,'Model::isCreated',array(new Submission()),'Model::isProblem',array(new Submission()));  
    }

    /**
     * Creates a submission and then returns it.
     *
     * Called when this component receives an HTTP POST request to
     * /submission(/).
     * The request body should contain a JSON object representing the new submission.
     */
    public function addSubmission( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new Submission( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/AddSubmission.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new Submission()));
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Submission data from db answer
                    $result['content'] = array_merge($result['content'], Submission::ExtractSubmission( $inp->getResponse( ), $singleResult));
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
