<?php


/**
 * @file DBExerciseSheet.php contains the DBExerciseSheet class
 *
 * @author Till Uhlig
 * @author Felix Schmidt
 * @example DB/DBExerciseSheet/ExerciseSheetSample.json
 * @date 2013-2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );
include_once ( dirname(__FILE__) . '/../../Assistants/LArraySorter.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "ExerciseSheet" table from database
 */
class DBExerciseSheet
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
        $component = new Model('exercisesheet', dirname(__FILE__), $this);
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an exercise sheet.
     *
     * Called when this component receives an HTTP PUT request to
     * /exercisesheet/$esid(/) or /exercisesheet/exercisesheet/$esid(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's new attributes.
     *
     * @param int $esid The id of the exercise sheet that is being updated.
     */
    public function editExerciseSheet( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/EditExerciseSheet.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new ExerciseSheet()),'Model::isProblem',array(new ExerciseSheet()));
    }

    /**
     * Deletes an exercise sheet.
     *
     * Called when this component receives an HTTP DELETE request to
     * /exercisesheet/$esid(/) or /exercisesheet/exercisesheet/$esid(/).
     *
     * @param int $esid The id of the exercise sheet that is being deleted.
     */
    public function deleteExerciseSheet( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/DeleteExerciseSheet.sql',$params,201,'Model::isCreated',array(new ExerciseSheet()),'Model::isProblem',array(new ExerciseSheet()));
    }

    /**
     * Adds an exercise sheet.
     *
     * Called when this component receives an HTTP POST request to
     * /exercisesheet(/).
     * The request body should contain a JSON object representing the exercise
     * sheet's attributes.
     */
    public function addExerciseSheet( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new ExerciseSheet( );
            $obj->setId( $input[0]->getInsertId( ) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('out2',dirname(__FILE__).'/Sql/AddExerciseSheet.sql',array( 'values' => $input->getInsertData( )),201,$positive,array(),'Model::isProblem',array(new ExerciseSheet()));
    }

    public function getURL( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Course data from db answer
                    $result['content'] = array_merge($result['content'], File::ExtractFile( $inp->getResponse( ), false));
                    $result['status'] = 200;
                }
            }
            return $result;
        };

        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call($linkName, $params, '', 200, $positive, array(), 'Model::isProblem', array(), 'Query');
    }

    public function getMatchURL($callName, $input, $params = array())
    {
        return $this->getURL($callName,$callName,$params);
    }

    public static function finalizeSheets( $result)
    {
        $result['content'] = LArraySorter::orderBy($result['content'], 'startDate', SORT_ASC, 'id',  SORT_ASC);

        // sets the sheet names
        $id = 1;
        reset($result['content']);
        $isArray=true;
        if (gettype(current($result['content'])) == 'object') $isArray=false;
        foreach ( $result['content'] as &$sheet ){
            if ($isArray){
                if ( !isset( $sheet['sheetName'] ) ||
                     $sheet['sheetName'] == null ){
                    $sheet['sheetName'] = 'Serie ' . ( string )$id;
                    $id++;
                }
            } else {
                if ( $sheet->getSheetName() == null ){
                    $sheet->setSheetName( 'Serie ' . ( string )$id);
                    $id++;
                }
            }
        }

        return $result;
    }

    public function getExerciseSheet( $callName, $input, $params = array() )
    {
        $getSheet = function($input,$esid, $exercise=null) {
            $getExercises = function($input, $sheet) {
                $result = Model::isEmpty();$result['content']=array();
                $data=array();
                foreach ($input as $inp){
                    if ( $inp->getNumRows( ) > 0 ){
                        // extract Course data from db answer
                        $result['content'] = Exercise::ExtractExercise( $inp->getResponse( ),false,'','','','',false);
                        $data = array_merge($data,$inp->getResponse( ));
                        $result['status'] = 200;
                    }
                }

                $result['content'] = DBJson::concatResultObjectLists(
                                                                   $data,
                                                                   $sheet,
                                                                   ExerciseSheet::getDBPrimaryKey( ),
                                                                   ExerciseSheet::getDBConvert( )['ES_exercises'],
                                                                   $result['content'],
                                                                   Exercise::getDBPrimaryKey( )
                                                                   );

                //$result['content'] = array_values($result['content'] );
                $result['content'] = array_merge( $result['content'] );
                if (count($result['content'])>0)
                    $result['content']=$result['content'][0];
                return $result;
            };

            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    $result['content'] = ExerciseSheet::ExtractExerciseSheet( $inp->getResponse( ),true,'','','',($exercise!==null ? false:true));
                    $result['status'] = 200;
                }
            }

            if ($exercise===null){
                return $result;
            }
            return $this->_component->call('getSheetExercises', array("esid"=>$esid), '', 200, $getExercises, array("sheet"=>$result['content']), 'Model::isProblem', array(), 'Query');;
        };

        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call('getExerciseSheet', $params, '', 200, $getSheet, $params, 'Model::isProblem', array(), 'Query');
    }

    public function getCourseSheets( $callName, $input, $params = array() )
    {
        $getSheets = function($input,$courseid, $exercise=null) {
            $getExercises = function($input, $sheet) {
                $result = Model::isEmpty();$result['content']=array();
                $data=array();
                foreach ($input as $inp){
                    if ( $inp->getNumRows( ) > 0 ){
                        // extract Course data from db answer
                        $result['content'] = Exercise::ExtractExercise( $inp->getResponse( ),false,'','','','',false);
                        $data = array_merge($data,$inp->getResponse( ));
                        $result['status'] = 200;
                    }
                }

                $result['content'] = DBJson::concatResultObjectLists(
                                                                   $data,
                                                                   $sheet,
                                                                   ExerciseSheet::getDBPrimaryKey( ),
                                                                   ExerciseSheet::getDBConvert( )['ES_exercises'],
                                                                   $result['content'],
                                                                   Exercise::getDBPrimaryKey( )
                                                                   );

                $result['content'] = array_merge( $result['content'] );
                return self::finalizeSheets($result);
            };

            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    $result['content'] = ExerciseSheet::ExtractExerciseSheet( $inp->getResponse( ),false,'','','',($exercise!==null ? false:true));
                    $result['status'] = 200;
                }
            }

            if ($exercise===null){
                return self::finalizeSheets($result);
            }
            return $this->_component->call('getCourseExercises', array("courseid"=>$courseid), '', 200, $getExercises, array("sheet"=>$result['content']), 'Model::isProblem', array(), 'Query');;
        };

        $params = DBJson::mysql_real_escape_string( $params );
        return $this->_component->call('getCourseSheets', $params, '', 200, $getSheets, $params, 'Model::isProblem', array(), 'Query');
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


