/**
 * @file EditUserSession.sql
 * updates an specified session from %Session table
 * @author  Till Uhlig
 * @param int $userid a %User identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */
 
UPDATE ExerciseSheet
SET $values
WHERE U_id = '$userid'