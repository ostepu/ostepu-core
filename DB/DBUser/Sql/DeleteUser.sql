/** 
 * @file DeleteUser.sql
 * updates a user from %User table, sets the flag to zero
 * @author  Till Uhlig
 * @param string $userid a %User identifier or username
 * @result -
 */
 
update User 
set 
    U_flag = 0
where
    U_id = '$userid' or U_username = '$userid' or U_externalId = '$userid'