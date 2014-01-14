/**
 * @file SetGroup.sql
 * creates a new entry in %Group table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */

INSERT INTO `Group` SET $values