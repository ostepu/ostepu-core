/**
 * @file EditFile.sql
 * updates an specified file from %File table
 * @author  Till Uhlig
 * @param int $fileid a %File identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */
 
UPDATE File
SET $values
WHERE F_id = $fileid