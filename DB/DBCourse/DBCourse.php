<?php
/**
 * @file DBCourse.php contains the DBCourse class
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
 * @example DB/DBCourse/CourseSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "DBCourse" table from database
 */
class DBCourse
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
        $component = new Model('course', dirname(__FILE__), $this, false, false, array('cloneable'=>true,
                                                                                       'defaultParams'=>array('exerciseSheetProfile'=>'','settingProfile'=>''),
                                                                                       'addOptionsToParametersAsPostfix'=>true,
                                                                                       'addProfileToParametersAsPostfix'=>true));
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits a course.
     *
     * Called when this component receives an HTTP PUT request to
     * /course/course/$courseid(/) or /course/$courseid(/).
     * The request body should contain a JSON object representing the course's new
     * attributes.
     *
     * @param int $courseid The id of the course that is being updated.
     */
    public function editCourse( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editCourse',dirname(__FILE__).'/Sql/EditCourse.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()));
    }

    /**
     * Deletes a course.
     *
     * Called when this component receives an HTTP DELETE request to
     * /course/course/$courseid(/) or /course/$courseid(/).
     *
     * @param int $courseid The id of the course that is being deleted.
     */
    public function deleteCourse( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteCourse',dirname(__FILE__).'/Sql/DeleteCourse.sql',$params,201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()));
    }

    /**
     * Adds a course.
     *
     * Called when this component receives an HTTP POST request to
     * /course(/).
     * The request body should contain a JSON object representing the course's
     * attributes.
     */

    public function addCourse( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $id = 0;
            $queryResult = $input[count($input)-1];
            $resp =$queryResult->getResponse();
            if (isset($resp[0]['@a']))
                $id = $resp[0]['@a'];

            // sets the new auto-increment id
            $obj = new Course( );
            $obj->setId( ($input[0]->getInsertId( )==0 ? $id : $input[0]->getInsertId( )) );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addCourse',dirname(__FILE__).'/Sql/AddCourse.sql',array_merge($params,array( 'values' => $input->getInsertData( ), 'in' => $input)),201,$positive,array(),'Model::isProblem',array(new Course()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract Course data from db answer
                    $res = Course::ExtractCourse( $inp->getResponse( ), false);
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
}