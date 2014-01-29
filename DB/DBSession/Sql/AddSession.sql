/**
 * @file AddSession.sql
 * inserts a session into %Session table
 * @author  Till Uhlig, edited by Lisa Dietrich
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
INSERT INTO Session (U_id, SE_sessionID) 
VALUES ($userid, '$sessionid')
ON DUPLICATE KEY UPDATE SE_sessionID = '$sessionid'