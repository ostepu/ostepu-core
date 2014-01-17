/**
 * @file EditCourse.sql
 * updates a specified course from %Course table
 * @author  Till Uhlig
 * @param int \$courseid a %Course identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
UPDATE Course
SET $values
WHERE C_id = '$courseid'