<?php 


/**
 * @file DBRequest.php contains the DBRequest class
 *
 * @author Till Uhlig
 * @date 2013-2014
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

        if ($config===null){
            // loads the mysql server config from file
            $config = parse_ini_file( 
                                     'config.ini',
                                     TRUE
                                     );
        }
        
        ini_set('mysql.connect_timeout','60');

        // creates a new connection to database
        if (!isset($config['ZV']['zv_type']) || (isset($config['ZV']['zv_type']) && $config['ZV']['zv_type']=='local')){
            $path = (strpos($config['PL']['urlExtern'],$config['DB']['db_path'])===false ? $config['DB']['db_path'] : 'localhost');
        } else 
            $path = $config['DB']['db_path'];
            
        $dbconn = @mysql_connect( 
                                $path,
                                $config['DB']['db_user'],
                                $config['DB']['db_passwd'],
                                false,
                                MYSQL_CLIENT_COMPRESS
                                );
        if (!$dbconn){
            $query_result['errno'] = mysql_errno( );
            $query_result['error'] = mysql_error( );
            return $query_result;
        }

	// use UTF8
	mysql_query("SET NAMES 'utf8'");
        
        // selects the database
        if ($config['DB']['db_name'] !== null)
            mysql_select_db( $config['DB']['db_name'] );

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
                 ( int )$_SERVER['REQUEST_TIME'] <= ( int )$_SERVER['HTTP_DATE'] + 10 * 60 ){
                $content = mysql_query( 
                                       'select SE_sessionID from Session where U_id = ' . $_SERVER['HTTP_USER'],
                                       $dbconn
                                       );

                // evaluates the session
                $errno = mysql_errno( );
                if ( $errno == 0 && 
                     gettype( $content ) != 'boolean' ){
                    $data = DBJson::getRows( $content );
                    if ( $data != null && 
                         $data[0]['SE_sessionID'] == $_SERVER['HTTP_SESSION'] ){
                        $sessionFail = false;
                        $query_result['error'] = 'access denied V';
                        
                    } else {
                        $sessionFail = true;
                        $query_result['error'] = 'access denied IV';
                    }
                } else {
                    $sessionFail = true;
                    $query_result['error'] = 'access denied III';
                }
                
            } else {
                $sessionFail = true;
                $query_result['error'] = "access denied II";
            }
        }

        // if a condition is not met, the request is invalid
        if ( $sessionFail == true ){
            $query_result['content'] = '';
            $query_result['errno'] = 401;
            if (!isset($query_result['error'])) $query_result['error'] = 'unknown access denied';
            $query_result['numRows'] = 0;
            mysql_close( $dbconn );
            $dbconn = null;
            return $query_result;
        }

        // performs the request
        $query_result['content'] = mysql_query( 
                                               $sqlStatement,
                                               $dbconn
                                               );

        // evaluates the request
        $query_result['affectedRows'] = mysql_affected_rows( );
        $query_result['insertId'] = mysql_insert_id( );
        $query_result['errno'] = mysql_errno( );
        $query_result['error'] = mysql_error( );

        if ( gettype( $query_result['content'] ) != 'boolean' ){
            $query_result['numRows'] = mysql_num_rows( $query_result['content'] );
        }

        // closes the connection and returns the result
        mysql_close( $dbconn );
        $dbconn = null;
        return $query_result;
    }
    
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

        ini_set('mysql.connect_timeout','60');
        
        // creates a new connection to database
        //echo "type: ".$config['ZV']['zv_type']."<br>";
        if (!isset($config['ZV']['zv_type']) || (isset($config['ZV']['zv_type']) && $config['ZV']['zv_type']=='local')){
            $path = (strpos($config['PL']['urlExtern'],$config['DB']['db_path'])===false ? $config['DB']['db_path'] : 'localhost' );
        } else
            $path = $config['DB']['db_path'];
            
        //echo "Path: ".$path."<br>";
        if (!$useDbOperator){
        //echo "User: ".$config['DB']['db_user']."<br>";
        //echo "Passwort: ".$config['DB']['db_passwd']."<br>";
            $dbconn = @mysqli_connect( 
                                    $path,
                                    $config['DB']['db_user'],
                                    $config['DB']['db_passwd'],
                                    $config['DB']['db_name'] 
                                    );
        } else {
        //echo "User: ".$config['DB']['db_user_operator']."<br>";
        //echo "Passwort: ".$config['DB']['db_passwd_operator']."<br>";
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
            if ($res = mysqli_store_result( $dbconn )) {
                $result['content']  = $res;

                // evaluates the request
                $result['affectedRows'] = mysqli_affected_rows( $dbconn );
                $result['insertId'] = mysqli_insert_id( $dbconn);
                $result['errno'] = mysqli_errno( $dbconn );
                $result['error'] = mysqli_error( $dbconn );

                if ( gettype( $result['content'] ) != 'boolean' ){
                    $result['numRows'] = mysqli_num_rows( $result['content'] );
                }
            }
            else
            {
                $result['content']  = $res;
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
///echo $sql;
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
}

 
