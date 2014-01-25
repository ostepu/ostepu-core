/** 
 * @file DeleteCourse.sql
 * deletes a specified course from %Course table
 * @author  Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result -
 */
 
DELETE FROM Course 
WHERE
    C_id = '$courseid'