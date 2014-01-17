/**
 * @file EditGroup.sql
 * updates a specified entry in %Group table
 * @author  Till Uhlig
 * @param int \$esid a %Group identifier
 * @param int \$userid a %Group identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */

UPDATE `Group`
SET $values
WHERE ES_id = '$esid# and U_id_leader = '$userid'