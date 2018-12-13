<?php
/**
 * @file DBQuery2.php contains the DBQuery2 class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2016
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 */

include_once ( dirname(__FILE__) . '/../../Assistants/Model.php' );

/**
 * A class, to perform requests to the database
 */
class DBQuery2
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
        $component = new Model('query', dirname(__FILE__), $this, false, false, array('cloneable'=>true));
        $this->_component=$component;
        $component->run();
    }

    /**
     * Needed to send a SQL query to the database.
     *
     * Each component which wants to send a SQL query to the database needs to send
     * the SQL query as a query object to this component. This component then returns
     * another query object including the response and possible errors.
     *
     * Called when this component receives an HTTP GET, an HTTP PUT or an HTTP POST
     * request to /query/.
     */
    public function generateParam($a)
    {
        return "'{$a}'";
    }
    public function generateQuery($procedure, $params)
    {
        if ($params == "") $params = array("");
        return "CALL `{$procedure}`(".implode(',',array_map(array($this,'generateParam'), $params)).");";
    }

    private static $config = null;
    private static $configName = null;
    public function postMultiGetRequest( $callName, $input, $par = array() )
    {
        $params=array();
        extract(
                $par,
                EXTR_OVERWRITE
        );

        $name = (isset($par['profileName']) && $par['profileName']!=='' ? '_'.$par['profileName'] : '');
        if (self::$config === null || self::$configName !== $name){
            self::$config = parse_ini_file(
                                            dirname(__FILE__).'/config'.$name.'.ini',
                                            TRUE
                                            );
        }
        
        $querys = $input;
        $sql='';
        $querys=explode("\n",$querys);
        foreach ($querys as $query){
            $query=explode('/query/procedure',$query)[1];
            $params = explode('/',$query);
            array_shift($params);
            $procedure=array_shift($params);
            $sql.=$this->generateQuery($procedure,$params)."select 'next';";
        }

        $answer = DBRequest::request2(
                                           $sql,
                                           false,
                                           self::$config
                                     );
        $result = Model::isOK();
        $res = array();
        $resArray=array();
        foreach ($answer as $query_result){
            $obj = new Query( );
            if (isset($query_result['content']) && $query_result['content']===array(array("next"=>'next'))){
                $resArray[]=array(Query::encodeQuery($res),'ETag'=>CacheManager::generateETag($res));
                $res=array();
                continue;
            }

        if ( $query_result['errno'] != 0 ){
            if ( isset($query_result['errno']) && $query_result['errno'] != 0 )
                Logger::Log(
                            'GET queryResult failed errno: ' . $query_result['errno'] . ' error: ' . $query_result['error'],
                            LogLevel::INFO
                            );

            if ( !isset($query_result['content']) || !$query_result['content'] )
                Logger::Log(
                            'GET queryResult failed, no content',
                            LogLevel::INFO
                            );

            if ( isset($query_result['errno']) && $query_result['errno'] == 401 ){
                $result = Model::isRejected();

            } else
                $result = Model::isProblem();

        }elseif ( gettype( $query_result['content'] ) == 'boolean' ){
            $obj->setResponse( array( ) );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

          if ( isset( $query_result['errno'] ) && $query_result['errno']>0 ){
            $result = Model::isProblem();
          }
          else
            $result = Model::isOK();

        } else {
            $data = array( );
            if ( isset( $query_result['numRows'] ) &&
                 $query_result['numRows'] > 0 ){
                $data = $query_result['content'];
            }

            $obj->setResponse( $data );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );


            $result = Model::isOK();
        }
        $res[]=$obj;
        }
        //$resArray[]=array('content'=>$res,'ETag'=>CacheManager::generateETag($res));
        $result['content'] = $resArray;

        return $result;
    }

    // führt Prozeduren aus 
    public function getProcedureQuery( $callName, $input, $par = array() )
    {
        $par = DBJson::mysql_real_escape_string( $par );
        $procedure = $par['procedure'];
        $profileName = $par['profileName'];
        $params = isset($par['params']) ? $par['params'] : array();

        $name = (isset($profileName) && $profileName!=='' ? '_'.$profileName : '');
        if (self::$config === null || self::$configName !== $name){
            self::$config = parse_ini_file(
                                            dirname(__FILE__).'/config'.$name.'.ini',
                                            TRUE
                                            );
        }

        $result = Model::isOK();$result['content']=array();
        $sql = $this->generateQuery($procedure,$params); // einzelne Anweisung
        $query_result = DBRequest::request2Single(
                                           $sql,
                                           false,
                                           self::$config
                                     );

        $hash=null;

        $obj = new Query( );

        if ( $query_result['errno'] != 0 ){
            if ( isset($query_result['errno']) && $query_result['errno'] != 0 ){
                Logger::Log(
                            'GET queryResult failed errno: ' . $query_result['errno'] . (isset($query_result['error']) ? ' error: ' . $query_result['error'] : ''),
                            LogLevel::INFO
                            );
                // der Fehler wird noch nicht zurückgegeben
            }

            if ( !isset($query_result['content']) || !$query_result['content'] ){
                Logger::Log(
                            'GET queryResult failed, no content',
                            LogLevel::INFO
                            );
            }

            if ( isset($query_result['errno']) && $query_result['errno'] == 401 ){
                $result = Model::isRejected();

            } else
                $result = Model::isProblem();

        }elseif ( gettype( $query_result['content'] ) == 'boolean' ){
            $obj->setResponse( array( ) );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

          if ( isset( $query_result['errno'] ) && $query_result['errno']>0 ){
            $result = Model::isProblem();
          }

        } else {
            $data = array( );
            if ( isset( $query_result['numRows'] ) &&
                 $query_result['numRows'] > 0 ){
                $data = $query_result['content'];
            }

            $obj->setResponse( $data );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

            $result = Model::isOK();
        }

        $result['content'] = $obj;

        return $result;
    }

    public function postQuery( $callName, $input, $par = array() )
    {
        $par = DBJson::mysql_real_escape_string( $par );
        $params=array();
        extract(
                $par,
                EXTR_OVERWRITE
        );

        $name = (isset($par['profileName']) && $par['profileName']!=='' ? '_'.$par['profileName'] : '');
        if (self::$config === null || self::$configName !== $name){
            self::$config = parse_ini_file(
                                            dirname(__FILE__).'/config'.$name.'.ini',
                                            TRUE
                                            );
        }

        $obj = $input;

        $answer = DBRequest::request2(
                                           $obj->getRequest( ),
                                           $obj->getCheckSession( ),
                                           self::$config
                                     );

        $result = Model::isOK();$result['content']=array();
        $res=array();

        foreach ($answer as $query_result){
            $obj = new Query( );

        if ( $query_result['errno'] != 0 ){
            if ( isset($query_result['errno']) && $query_result['errno'] != 0 )
                Logger::Log(
                            'GET queryResult failed errno: ' . $query_result['errno'] . ' error: ' . $query_result['error'],
                            LogLevel::INFO
                            );

            if ( !isset($query_result['content']) || !$query_result['content'] )
                Logger::Log(
                            'GET queryResult failed, no content',
                            LogLevel::INFO
                            );

            if ( isset($query_result['errno']) && $query_result['errno'] == 401 ){
                $result = Model::isRejected();

            } else {
                $result = Model::isProblem();
            }

        }elseif ( gettype( $query_result['content'] ) == 'boolean' ){
            $obj->setResponse( array( ) );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

          if ( isset( $query_result['errno'] ) && $query_result['errno']>0 ){
            $result = Model::isProblem();
          }
          else {
            $result = Model::isCreated();
          }

        } else {
            $data = array( );
            if ( isset( $query_result['numRows'] ) &&
                 $query_result['numRows'] > 0 ){
                $data = $query_result['content'];
            }

            $obj->setResponse( $data );
            if ( isset( $query_result['affectedRows'] ) )
                $obj->setAffectedRows( $query_result['affectedRows'] );
            if ( isset( $query_result['insertId'] ) )
                $obj->setInsertId( $query_result['insertId'] );
            if ( isset( $query_result['errno'] ) )
                $obj->setErrno( $query_result['errno'] );
            if ( isset( $query_result['numRows'] ) )
                $obj->setNumRows( $query_result['numRows'] );

          if ( isset( $query_result['errno'] ) && $query_result['errno']>0 ){
            $result = Model::isProblem();
          }
          else {
            $result = Model::isCreated();
          }
            //$result = Model::isOK();
        }
        $res[]=$obj;
        }
        if (count($res)==1) $res = $res[0];
        $result['content'] = $res;

        return $result;
    }

    /**
     * Returns status code 200, if this component is correctly installed for the platform
     *
     * Called when this component receives an HTTP GET request to
     * /link/exists/platform.
     */
    public function getExistsPlatform( $callName, $input, $params = array() )
    {
        if (!file_exists(dirname(__FILE__) . '/config'.(isset($params['profileName']) && $params['profileName']!=='' ? '_'.$params['profileName'] : '').'.ini')){
            return Model::isProblem();
        }

        return Model::isOK();
    }

    /**
     * Removes the component from the platform
     *
     * Called when this component receives an HTTP DELETE request to
     * /platform.
     */
    public function deletePlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts DELETE DeletePlatform',
                    LogLevel::DEBUG
                    );

        $this->loadConfig($name);
        $configFile = dirname(__FILE__) . '/config'.(isset($params['profileName']) && $params['profileName']!=='' ? '_'.$params['profileName'] : '').'.ini';
        if (file_exists($configFile) && !unlink($configFile)){
            return Model::isProblem();
        }

        return Model::isCreated();
    }

    /**
     * Adds the component to the platform
     *
     * Called when this component receives an HTTP POST request to
     * /platform.
     */
    public function addPlatform( $callName, $input, $params = array() )
    {
        Logger::Log(
                    'starts POST AddPlatform',
                    LogLevel::DEBUG
                    );

        //$this->loadConfig($name);
        // decode the received course data, as an object
        $insert = $input;
        $result=Model::isOK();

        // always been an array
        $arr = true;
        if ( !is_array( $insert ) ){
            $insert = array( $insert );
            $arr = false;
        }

        // this array contains the indices of the inserted objects
        $res = array( );
        foreach ( $insert as $in ){

            $file = dirname(__FILE__) . '/config'.(isset($params['profileName']) && $params['profileName']!=='' ? '_'.$params['profileName'] : '').'.ini';
            $text = "[DB]\n".
                    "db_path = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseUrl())."\"\n".
                    "db_user = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseOperatorUser())."\"\n".
                    "db_passwd = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseOperatorPassword())."\"\n".
                    "db_name = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getDatabaseName())."\"\n".
                    "[PL]\n".
                    "urlExtern = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getExternalUrl())."\"\n".
                    "url = \"".str_replace(array("\\","\""),array("\\\\","\\\""),$in->getBaseUrl())."\"";

            if (!@file_put_contents($file,$text)){
                Logger::Log(
                            'POST AddPlatform failed, config.ini no access',
                            LogLevel::ERROR
                            );

                $result=Model::isProblem();
            }

            $platform = new Platform();
            $platform->setStatus(201);
            $res[] = $platform;
            $result=Model::isCreated();
        }
        $return['content'] = $res;
        return $return;
    }

    public function getApiProfiles( $callName, $input, $params = array() )
    {   
        $myName = $this->_component->_conf->getName();
        $profiles = array();
        ///$profiles['readonly'] = GateProfile::createGateProfile(null,'readonly');

        
        $profiles['general'] = GateProfile::createGateProfile(null,'general');
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /query',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /multiGetRequest',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /query/procedure/:procedure(/:params+)',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'DELETE /platform',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'POST /platform',null));
        $profiles['general']->addRule(GateRule::createGateRule(null,'httpCall',$myName,'GET /link/exists/platform',null));
        
        $profiles['develop'] = GateProfile::createGateProfile(null,'develop');
        $profiles['develop']->setRules(array_merge($profiles['general']->getRules(), $this->_component->_com->apiRulesDevelop($myName)));

        ////$profiles['public'] = GateProfile::createGateProfile(null,'public');
        return Model::isOk(array_values($profiles));
    }
}


