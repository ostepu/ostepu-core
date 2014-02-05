<?php
/**
 * @file DBRequest.php contains the DBRequest class
 *
 * @author Till Uhlig
 */ 
include_once( '/Structures.php' );

/**
 * the class provides functions for database queries
 */
class DBRequest
{
    
    /**
     * performs a database query
     *
     * @param string $sql_statement the sql statement you want send
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
    public static function request($sqlStatement, $checkSession)
    {
        // loads the mysql server config from file
        $config = parse_ini_file("config.ini", TRUE);
        
        // creates a new connection to database
        $dbconn = mysql_connect($config['DB']['db_path'], $config['DB']['db_user'],$config['DB']['db_passwd']);    

        // selects the database
        mysql_select_db($config['DB']['db_name']);

        $currentTime = $_SERVER['REQUEST_TIME'];
        
        // check session
         $checkSession = false; // remove the comment this line to disable the session examination

        // Storing whether or not a session condition is not satisfied
        $sessionFail = false;
        if ($checkSession === true){
            Logger::Log("starts session validation",LogLevel::DEBUG);
            if (isset($_SERVER['HTTP_SESSION']) && 
                isset($_SERVER['HTTP_USER']) && 
                isset($_SERVER['HTTP_DATE']) && 
                ctype_digit($_SERVER['HTTP_USER']) && 
                (int)$_SERVER['REQUEST_TIME'] <= (int)$_SERVER['HTTP_DATE'] + 10*60 ){
                $content = mysql_query('select SE_sessionID from Session where U_id = ' . $_SERVER['HTTP_USER'], $dbconn);
                
                // evaluates the session 
                $errno = mysql_errno();
                if ($errno==0 && gettype($content)!='boolean'){
                    $data = DBJson::getRows($content);
                    if ($data != null && $data[0]['SE_sessionID'] == $_SERVER['HTTP_SESSION']){
                        $sessionFail = false; 
                    } else
                        $sessionFail = true;  
                } else
                    $sessionFail = true;                      
            } else
                $sessionFail = true;
        }
        
        // if a condition is not met, the request is invalid
        if ($sessionFail == true){
            $query_result['content'] = "";
            $query_result['errno'] = 401;
            $query_result['error'] = 'access denied';
            $query_result['numRows'] = 0;
            mysql_close($dbconn);
            $dbconn=null;
            return $query_result;
        }
        
        // performs the request
        $query_result['content'] = mysql_query($sqlStatement, $dbconn);
        
        // evaluates the request 
        $query_result['affectedRows'] = mysql_affected_rows();
        $query_result['insertId'] = mysql_insert_id();
        $query_result['errno'] = mysql_errno();
        $query_result['error'] = mysql_error();

        if (gettype($query_result['content'])!='boolean'){
            $query_result['numRows'] = mysql_num_rows($query_result['content']);
        }
        
        // closes the connection and returns the result
        mysql_close($dbconn);
        $dbconn=null;
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
    public static function getRoutedSqlFile($querys, $sqlFile, $vars, $checkSession=true)
    {
        // generates the variable content
        extract($vars, EXTR_OVERWRITE);

        // loads the given sql file and creates the Query object
        $obj = new Query();
        eval("\$sql = \"" . file_get_contents($sqlFile) . "\";");
        $obj->setRequest($sql);
        $obj->setCheckSession($checkSession);

        // perform the route process
        return Request::routeRequest("POST",
                                    '/query',
                                    array(),
                                    Query::encodeQuery($obj),
                                    $querys,
                                    "query");
    }
}
?>