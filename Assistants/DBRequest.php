<?php 
/**
 * @file DBRequest.php contains the DBRequest class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @author Sandro Wefel <sandro.wefel@informatik.uni-halle.de>
 * @date 2015
 */

include_once ( dirname( __FILE__ ) . '/Structures.php' );

/**
 * the class provides functions for database queries
 */
class DBRequest
{

    /**
     * performs a database query
     *
     * @param string $sql_statement the sql statement you want to send
     *
     * @return  assoc array with multiple query result informations (String[])
     * - ['content'] = the content/table you received from database
     * - ['affectedRows'] = the affected rows
     * - ['insertId'] = on post/insert with auto-increment, the id of the inserted entry
     * - ['errno'] = the error number
     * - ['error'] = the error message
     * - ['numRows'] = on get, the received number of rows
     * - you have to check for yourself, that the records exist, with isset()
     */
    public static function request( 
                                   $sqlStatement,
                                   $checkSession,
                                   $config = null,
                                   $useDbOperator = false
                                   )
    {
        throw new Exception("DBRequest::request is deprecated");
    }
    
    // benutzt die mysqli-Erweiterung, wobei mehrere Anfragen in einem sqlStatement erlaubt sind
    public static function request2( 
                                   $sqlStatement,
                                   $checkSession,
                                   $config = null,
                                   $useDbOperator = false
                                   )
    {

        if ($config===null){
            // loads the mysql server config from file
            $config = parse_ini_file( 
                                     'config.ini',
                                     TRUE
                                     );
        }

        //ini_set('mysql.connect_timeout','60');
        
        // creates a new connection to database
        if (!isset($config['ZV']['zv_type']) || (isset($config['ZV']['zv_type']) && $config['ZV']['zv_type']=='local')){
            $path = (strpos($config['PL']['urlExtern'],$config['DB']['db_path'])===false ? $config['DB']['db_path'] : 'localhost' );
        } else
            $path = $config['DB']['db_path'];
            
        if (!$useDbOperator){
            $dbconn = @mysqli_connect( 
                                    $path,
                                    $config['DB']['db_user'],
                                    $config['DB']['db_passwd'],
                                    $config['DB']['db_name'] 
                                    );
        } else {
            $dbconn = @mysqli_connect( 
                                    $path,
                                    $config['DB']['db_user_operator'],
                                    $config['DB']['db_passwd_operator'],
                                    $config['DB']['db_name'] 
                                    );

        }
                                
        if (!$dbconn){
            $query_result['errno'] = 10;
            return $query_result;
        }

	// use UTF8
	mysqli_set_charset($dbconn,"utf8");

        $currentTime = $_SERVER['REQUEST_TIME'];

        // check session
        ///if (error_reporting() & E_NOTICE)
            $checkSession = false; // remove the comment this line to disable the session examination
        
        // Storing whether or not a session condition is not satisfied
        $sessionFail = false;
        if ( $checkSession === true ){
            Logger::Log( 
                        'starts session validation',
                        LogLevel::DEBUG
                        );
            if ( isset( $_SERVER['HTTP_SESSION'] ) && 
                 isset( $_SERVER['HTTP_USER'] ) && 
                 isset( $_SERVER['HTTP_DATE'] ) && 
                 ctype_digit( $_SERVER['HTTP_USER'] ) && 
                 ( int )$_SERVER['REQUEST_TIME'] <= ( int )$_SERVER['HTTP_DATE'] + 45 * 60 ){
                $content = mysqli_query( 
                                        $dbconn,
                                       'select SE_sessionID from Session where U_id = ' . $_SERVER['HTTP_USER']
                                       );

                // evaluates the session
                $errno = mysqli_errno( $dbconn );
                if ( $errno == 0 && 
                     gettype( $content ) != 'boolean' ){
                    $data = DBJson::getRows2( $content );
                    if ( $data != null && 
                         $data[0]['SE_sessionID'] == $_SERVER['HTTP_SESSION'] ){
                        $sessionFail = false;
                        
                    } else 
                        $sessionFail = true;
                    
                } else 
                    $sessionFail = true;
                
            } else 
                $sessionFail = true;
        }

        // if a condition is not met, the request is invalid
        if ( $sessionFail == true ){
            $query_result['content'] = '';
            $query_result['errno'] = 401;
            $query_result['error'] = 'access denied';
            $query_result['numRows'] = 0;
            mysqli_close( $dbconn );
            $dbconn = null;
            return array($query_result);
        }

        // performs the request
        $answ = mysqli_multi_query(
                                                $dbconn,
                                                $sqlStatement
                                               );
        $query_result=array();

    if ($answ===false){
        $result=array();    
        $result['affectedRows'] = mysqli_affected_rows( $dbconn);
        $result['insertId'] = mysqli_insert_id( $dbconn);
        $result['errno'] = mysqli_errno( $dbconn );
        $result['error'] =mysqli_error( $dbconn );
        $query_result[] = $result;
    }
    else{
        do {
            $result=array();
            $res=null;
            if ($res = mysqli_use_result( $dbconn )) {
                $hash='';
                $result['content']  = DBJson::getRows2( $res, $hash );
                $result['hash'] = $hash;
                $result['numRows'] = count($result['content']);
                
                // evaluates the request
                $result['affectedRows'] = mysqli_affected_rows( $dbconn );
                $result['insertId'] = mysqli_insert_id( $dbconn);
                $result['errno'] = mysqli_errno( $dbconn );
                $result['error'] = mysqli_error( $dbconn );
                mysqli_free_result($res);
            }
            else
            {
                $hash='';
                $result['content'] = null;
                $result['hash'] = $hash;
                $result['affectedRows'] = mysqli_affected_rows( $dbconn );
                $result['insertId'] = mysqli_insert_id( $dbconn);
                $result['errno'] = mysqli_errno( $dbconn );
                $result['error'] = mysqli_error( $dbconn );
                
            }

            $query_result[] = $result;
        } while (mysqli_more_results($dbconn) && mysqli_next_result($dbconn));
    }
        // closes the connection and returns the result
        mysqli_close( $dbconn );
        $dbconn = null;
        return $query_result;
    }
    
    // benutzt die mysqli_query Methode, sodass nur eine einzelne Anfrage ausgewertet wird
    public static function request2Single( 
                                   $sqlStatement,
                                   $checkSession,
                                   $config = null,
                                   $useDbOperator = false
                                   )
    {

        if ($config===null){
            // loads the mysql server config from file
            $config = parse_ini_file( 
                                     'config.ini',
                                     TRUE
                                     );
        }

        //ini_set('mysql.connect_timeout','60');
        
        // creates a new connection to database
        if (!isset($config['ZV']['zv_type']) || (isset($config['ZV']['zv_type']) && $config['ZV']['zv_type']=='local')){
            $path = (strpos($config['PL']['urlExtern'],$config['DB']['db_path'])===false ? $config['DB']['db_path'] : 'localhost' );
        } else
            $path = $config['DB']['db_path'];
            
        if (!$useDbOperator){
            $dbconn = @mysqli_connect( 
                                    $path,
                                    $config['DB']['db_user'],
                                    $config['DB']['db_passwd'],
                                    $config['DB']['db_name'] 
                                    );
        } else {
            $dbconn = @mysqli_connect( 
                                    $path,
                                    $config['DB']['db_user_operator'],
                                    $config['DB']['db_passwd_operator'],
                                    $config['DB']['db_name'] 
                                    );

        }
                                
        if (!$dbconn){
            $query_result['errno'] = 10;
            return $query_result;
        }

        // use UTF8
        mysqli_set_charset($dbconn,"utf8");

        // performs the request
        $answ = mysqli_query(
                                                $dbconn,
                                                $sqlStatement, 
                                                MYSQLI_USE_RESULT
                                               );
                                               
        $query_result=array();   

        if ($answ===false){     
            $hash=''; 
            $query_result['affectedRows'] = mysqli_affected_rows( $dbconn);
            $query_result['insertId'] = mysqli_insert_id( $dbconn);
            $query_result['errno'] = mysqli_errno( $dbconn );
            $query_result['error'] =mysqli_error( $dbconn );     
        } else {             
            $hash='';
            $query_result['content']  = DBJson::getRows2( $answ, $hash );
            $query_result['numRows'] = count($query_result['content']);
            $query_result['hash'] = $hash;
            $query_result['affectedRows'] = mysqli_affected_rows( $dbconn);
            $query_result['insertId'] = mysqli_insert_id( $dbconn);
            $query_result['errno'] = mysqli_errno( $dbconn );
            $query_result['error'] =mysqli_error( $dbconn );
        }

        // closes the connection and returns the result
        mysqli_close( $dbconn );
        $dbconn = null;
        return $query_result;
    }

    /**
     * performs a database query by using a given file
     *
     * @param Query[] $querys an array of Query objects
     * @param string $sqlFile a file with a sql statement
     * @param string[] $vars an associative array, with the variables for the placeholder
     * - e.g. array("userid" => $userid, "values" => $values)
     *
     * @return  assoc array with multiple query result informations (String[])
     * - ['content'] = the content/table you received from database
     * - ['affectedRows'] = the affected rows
     * - ['insertId'] = on post/insert with auto-increment, the id of the inserted entry
     * - ['errno'] = the error number
     * - ['error'] = the error message
     * - ['numRows'] = on get, the received number of rows
     * - you have to check for yourself, that the records exist, with isset()
     */
    public static function getRoutedSqlFile( 
                                            $querys,
                                            $sqlFile,
                                            $vars,
                                            $checkSession = true
                                            )
    {
        $vars['sqlPath'] = dirname($sqlFile);
        // generates the variable content
        extract( 
                $vars,
                EXTR_OVERWRITE
                );

        // loads the given sql file and creates the Query object
        $obj = new Query( );
        ob_start();
        $sqlParsed = eval("?>" .  file_get_contents( $sqlFile ));
        $sql = ob_get_contents();
        ob_end_clean();
//echo $sql;
        if ($sqlParsed === false){
            $answer = array();
            $answer['status'] = 409;
            return $answer;
        } else {
            $obj->setRequest( $sql );
            $obj->setCheckSession( $checkSession );

            // perform the route process
            return Request::routeRequest( 
                                         'POST',
                                         '/query',
                                         array( ),
                                         Query::encodeQuery( $obj ),
                                         $querys,
                                         'query'
                                         );
        }

        $answer = array();
        $answer['status'] = 409;
        return $answer;
    }
    
    public static function getRoutedSql( 
                                            $querys,
                                            $sql,
                                            $checkSession = true
                                            )
    {

        $obj = new Query( );
        $obj->setRequest( $sql );
        $obj->setCheckSession( $checkSession );

        // perform the route process
        return Request::routeRequest( 
                                     'POST',
                                     '/query',
                                     array( ),
                                     Query::encodeQuery( $obj ),
                                     $querys,
                                     'query'
                                     );
    }
    
        public static function prepareSqlFile(
                                            $sqlFile,
                                            $vars
                                            )
    {
        $vars['sqlPath'] = dirname($sqlFile);
        // generates the variable content
        extract( 
                $vars,
                EXTR_OVERWRITE
                );

        // loads the given sql file
        ob_start();
        $sqlParsed = eval("?>" .  file_get_contents( $sqlFile ));
        $sql = ob_get_contents();
        ob_end_clean();
//echo $sql;
        if ($sqlParsed === false){
            return false;
        } else {
            return $sql;
        }
    }
}

 
