<?php 


/**
 * @file DBExerciseType.php contains the DBExerciseType class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExerciseType/ExerciseTypeSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "ExerciseType" table from database
 */
class DBExerciseType
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
        $component = new Model('exercisetype', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an exercise type.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisetype/exercisetype/$etid(/) or /exercisetype/$etid(/).
     * The request body should contain a JSON object representing the
     * exercise type's new attributes.
     *
     * @param int $etid The id or the exercise type.
     */
    public function editExerciseType( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/EditExerciseType.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new ExerciseType()),'Model::isProblem',array(new ExerciseType()));
    }

    /**
     * Deletes an exercise type.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisetype/exercisetype/$etid(/) or /exercisetype/$etid(/).
     *
     * @param int $etid The id or the exercise type that is being deleted.
     */
    public function deleteExerciseType( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/DeleteExerciseType.sql',$params,201,'Model::isCreated',array(new ExerciseType()),'Model::isProblem',array(new ExerciseType()));  
    }

    /**
     * Adds a new exercise type.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisetype(/).
     * The request body should contain a JSON object representing the
     * new exercise type's attributes.
     */
    public function addExerciseType( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new ExerciseType( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out',dirname(__FILE__).'/Sql/AddExerciseType.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new ExerciseType()));
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract ExerciseType data from db answer
                    $result['content'] = array_merge($result['content'], ExerciseType::ExtractExerciseType( $inp->getResponse( ), $singleResult));
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
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeletePlatform.sql',array(),200,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
    
    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array())
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddPlatform.sql',array('object' => $input),200,'Model::isCreated',array(new Platform()),'Model::isProblem',array(new Platform()),false);
    }
}

 
?>