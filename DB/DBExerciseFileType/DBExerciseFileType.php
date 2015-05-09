<?php 


/**
 * @file DBExerciseFileType.php contains the DBExerciseFileType class
 *
 * @author Till Uhlig
 * @example DB/DBExerciseFileType/ExerciseFileTypeSample.json
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "ExerciseFileType" table from database
 */
class DBExerciseFileType
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
        $component = new Model('exercisefiletype', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an exercise file type.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisefiletype/exercisefiletype/$eftid(/) or /exercisefiletype/$eftid(/).
     * The request body should contain a JSON object representing the
     * exercise type's new attributes.
     *
     * @param int $eftid The id or the exercise file type.
     */
    public function editExerciseFileType( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/EditExerciseFileType.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new ExerciseFileType()),'Model::isProblem',array(new ExerciseFileType()));
    }

    /**
     * Deletes an exercise file type.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisefiletype/exercisefiletype/$eftid(/) or /exercisefiletype/$eftid(/).
     *
     * @param int $eftid The id or the exercise file type that is being deleted.
     */
    public function deleteExerciseFileType( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteExerciseFileType.sql',$params,201,'Model::isCreated',array(new ExerciseFileType()),'Model::isProblem',array(new ExerciseFileType()));  
    }
    
    public function deleteExerciseExerciseFileType( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteExerciseFileType.sql',$params,201,'Model::isCreated',array(new ExerciseFileType()),'Model::isProblem',array(new ExerciseFileType()));  
    }
    
    public function deleteExerciseSheetExerciseFileType( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteExerciseFileType.sql',$params,201,'Model::isCreated',array(new ExerciseFileType()),'Model::isProblem',array(new ExerciseFileType()));  
    }
    
    /**
     * Adds a new exercise type.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisefiletype(/).
     * The request body should contain a JSON object representing the
     * new exercise file type's attributes.
     */
    public function addExerciseFileType( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new ExerciseFileType( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddExerciseFileType.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new ExerciseFileType()));
    }

    public function get( $functionName, $linkName, $params=array(),$singleResult = false, $checkSession = true )
    {
        $positive = function($input, $singleResult) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract ExerciseFileType data from db answer
                    $result['content'] = array_merge($result['content'], ExerciseFileType::ExtractExerciseFileType( $inp->getResponse( ), $singleResult));
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