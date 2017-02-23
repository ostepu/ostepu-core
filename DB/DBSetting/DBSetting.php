<?php
/**
 * @file DBSetting.php contains the DBSetting class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

\Slim\Slim::registerAutoloader( );

/**
 * A class, to abstract the "Settings" table from database
 */
class DBSetting
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
        $component = new Model('setting,course', dirname(__FILE__), $this, false, false, array('cloneable'=>true,
                                                                                               'addOptionsToParametersAsPostfix'=>true,
                                                                                               'addProfileToParametersAsPostfix'=>true));
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits an Setting.
     */
    public function editSetting( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editSetting',dirname(__FILE__).'/Sql/EditSetting.sql',array_merge($params,array('in' => $input)),201,'Model::isCreated',array(new Setting()),'Model::isProblem',array(new Setting()));
    }

    /**
     * Deletes an Setting.
     */
    public function deleteSetting( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteSetting',dirname(__FILE__).'/Sql/DeleteSetting.sql',$params,201,'Model::isCreated',array(new Setting()),'Model::isProblem',array(new Setting()));
    }

    /**
     * Adds an Setting.
     */
    public function addSetting( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
           $course = Course::ExtractCourse($input[count($input)-1]->getResponse(),true);

            // sets the new auto-increment id
            $obj = new Setting( );                
            $obj->setId( $course->getId() . '_' . $input[count($input)-2]->getInsertId( ) );
            
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addSetting',dirname(__FILE__).'/Sql/AddSetting.sql',array_merge($params,array( 'in' => $input)),201,$positive,array(),'Model::isProblem',array(new Setting()),false);
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        if (isset($params['setid'])){
            $params['courseid'] = Setting::getCourseFromSettingId($params['setid']);
            $params['setid'] = Setting::getIdFromSettingId($params['setid']);
        }
        
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract redirect data from db answer
                    $res = Setting::ExtractSetting( $inp->getResponse( ), false);
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
     * Removes the component from a given course
    */
    public function deleteCourse( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('deleteCourse',dirname(__FILE__).'/Sql/DeleteCourse.sql',$params,201,'Model::isCreated',array(new Course()),'Model::isProblem',array(new Course()),false);
    }

    /**
     * Adds the component to a course
     */
    public function addCourse( $callName, $input, $params = array() )
    {
        $positive = function($input, $course) {
            return array("status"=>201,"content"=>$course);
        };
        return $this->_component->callSqlTemplate('addCourse',dirname(__FILE__).'/Sql/AddCourse.sql',array_merge($params,array('object' => $input)),201,$positive,array('course'=>$input),'Model::isProblem',array(new Course()),false);
    }
}

 