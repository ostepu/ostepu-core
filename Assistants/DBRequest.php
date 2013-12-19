<?php
/**
 * @file (filename)
 * %(description)
 */ 
include_once( 'Structures.php' );

/**
 * (description)
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
        /*if (!$dbconn) {
            die('Keine Verbindung möglich: ' . mysql_error());
        }*/   

        mysql_select_db(DBRequest::$config['DB']['db_name']);
        $sql_statement = explode("---",$sqlStatement);
        foreach ($sql_statement as $statement){
        $query_result = mysql_query($statement, $dbconn);
         /*   if (!$query_result){
                die('Keine Verbindung möglich: ' . mysql_error());
            }*/
        }
        
        mysql_close($dbconn);
        $dbconn=null;
        return $query_result;
    }
    
    /**
     * (description)
     *
     * @param $sql_statement (description)
     */
    public static function getRoutedSqlFile($querys, $sqlFile, $vars)
    {
        extract($vars, EXTR_OVERWRITE);

        $obj = new Query();
        eval("\$sql = \"" . implode('\n',file($sqlFile)) . "\";");
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