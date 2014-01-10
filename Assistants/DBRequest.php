<?php
/**
 * @file DBRequest.php contains the DBRequest class
 */ 
include_once( 'Structures.php' );

/**
 * (description)
 *
 * @author Till Uhlig
 */
class DBRequest
{
    public static $config = null;
    
    /**
     * (description)
     *
     * @param $sql_statement (description)
     */
    public static function request($sqlStatement)
    {
        if (DBRequest::$config==null)
            DBRequest::$config = parse_ini_file("config.ini", TRUE);
        
        $dbconn = mysql_connect(DBRequest::$config['DB']['db_path'], DBRequest::$config['DB']['db_user'],DBRequest::$config['DB']['db_passwd']);    

        mysql_select_db(DBRequest::$config['DB']['db_name']);

        $query_result['content'] = mysql_query($sqlStatement, $dbconn);
        $query_result['affectedRows'] = mysql_affected_rows();
        $query_result['insertId'] = mysql_insert_id();
        $query_result['errno'] = mysql_errno();
        $query_result['error'] = mysql_error();
         
        
        if (gettype($query_result['content'])!='boolean'){
            $query_result['numRows'] = mysql_num_rows($query_result['content']);
        }
        
        mysql_close($dbconn);
        $dbconn=null;
        return $query_result;
    }
    
    /**
     * (description)
     *
     * @param $querys (description)
     * @param $sqlFile (description)
     * @param $vars (description)
     */
    public static function getRoutedSqlFile($querys, $sqlFile, $vars)
    {
        extract($vars, EXTR_OVERWRITE);

        $obj = new Query();
        eval("\$sql = \"" . file_get_contents($sqlFile) . "\";"); 
        $obj->setRequest($sql);
        
        return Request::routeRequest("GET",
                                    '/query',
                                    array(),
                                    Query::encodeQuery($obj),
                                    $querys,
                                    "query");
    }
}
?>