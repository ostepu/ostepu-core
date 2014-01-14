/**
 * @file EditSession.sql
 * updates an specified session from %Session table
 * @author  Till Uhlig
 * @param string $seid a %Session identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */
 
UPDATE `Session`
SET $values
WHERE SE_id = '$seid'