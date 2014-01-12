/** 
 * @file DeleteUserSession.sql
 * deletes a specified session from %Session table
 * @author  Till Uhlig
 * @param int $userid a %User identifier
 * @result -
 */
 
DELETE FROM `Session` 
WHERE
    U_id = '$userid'