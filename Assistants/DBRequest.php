<?php
/**
 * @file (filename)
 * %(description)
 */ 


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
    public static function request($sql_statement)
    {
        if (DBRequest::$config==null)
            DBRequest::$config = parse_ini_file("config.ini", TRUE);
        
        $dbconn = mysql_connect(DBRequest::$config['DB']['db_path'], DBRequest::$config['DB']['db_user'],DBRequest::$config['DB']['db_passwd']);    
        if (!$dbconn) {
            die('Keine Verbindung möglich: ' . mysql_error());
        }   

        mysql_select_db(DBRequest::$config['DB']['db_name']);
        $query_result = mysql_query($sql_statement, $dbconn);
        if (!$query_result){
            die('Keine Verbindung möglich: ' . mysql_error());
        }
        mysql_close($dbconn);
        $dbconn=null;
        return $query_result;
    }
}
?>