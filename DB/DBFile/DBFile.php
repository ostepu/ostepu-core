<?php
/**
 * @file DBFile.php contains the DBFile class
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
 * @example DB/DBFile/FileSample.json
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to abstract the "File" table from database
 */
class DBFile
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
        $component = new Model('file', dirname(__FILE__), $this, false, false, array('cloneable'=>true,
                                                                                     'addProfileToParametersAsPostfix'=>true));
        $this->_component=$component;
        $component->run();
    }

    /**
     * Edits a file.
     *
     * Called when this component receives an HTTP PUT request to
     * /file/$fileid(/) or /file/file/$fileid(/).
     * The request body should contain a JSON object representing the file's new
     * attributes.
     *
     * @param string $fileid The id of the file that is being updated.
     */
    public function editFile( $callName, $input, $params = array() )
    {
        return $this->_component->callSqlTemplate('editFile',dirname(__FILE__).'/Sql/EditFile.sql',array_merge($params,array('values' => $input->getInsertData( ))),201,'Model::isCreated',array(new File()),'Model::isProblem',array(new File()));
    }

    /**
     * Deletes a file.
     *
     * Called when this component receives an HTTP DELETE request to
     * /file/$fileid(/) or /file/file/$fileid(/).
     *
     * @param string $fileid The id of the file that is being deleted.
     */
    public function removeFile( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            $result = Model::isProblem();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract File data from db answer
                      $res = File::ExtractFile( $inp->getResponse( ), false);
                    $result['content'] = array_merge($result['content'], (is_array($res) ? $res : array($res)));
                    $result['status'] = 201;
                }
            }
            return $result;
        };
        return $this->_component->callSqlTemplate('removeFile',dirname(__FILE__).'/Sql/DeleteFile.sql',$params,201,$positive,array(),'Model::isProblem',array(new File()), 'Query');
    }

    /**
     * Adds a file.
     *
     * Called when this component receives an HTTP POST request to
     * /file(/).
     * The request body should contain a JSON object representing the file's
     * attributes.
     */
    public function addFile( $callName, $input, $params = array() )
    {
        $positive = function($input) {
            // sets the new auto-increment id
            $obj = new File( );
            $id = $input[count($input)-1]->getResponse();
            $id = $id[0]['ID'];
            $obj->setFileId( $id );
            return array("status"=>201,"content"=>$obj);
        };
        return $this->_component->callSqlTemplate('addFile',dirname(__FILE__).'/Sql/AddFile.sql',array_merge($params,array( 'values' => $input->getInsertData( ))),201,$positive,array(),'Model::isProblem',array(new File()));
    }

    public function get( $functionName, $linkName, $params=array(), $checkSession = true )
    {
        $positive = function($input) {
            //$input = $input[count($input)-1];
            $result = Model::isEmpty();$result['content']=array();
            foreach ($input as $inp){
                if ( $inp->getNumRows( ) > 0 ){
                    // extract File data from db answer
                      $res = File::ExtractFile( $inp->getResponse( ), false);
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
