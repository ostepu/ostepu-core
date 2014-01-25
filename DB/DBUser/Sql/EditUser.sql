/** 
 * @file EditUser.sql
 * updates an specified user from %User table
 * @author  Till Uhlig
 * @param string \$userid a %User identifier or username
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
UPDATE User
SET $values
WHERE U_id = '$userid' or U_username = '$userid'