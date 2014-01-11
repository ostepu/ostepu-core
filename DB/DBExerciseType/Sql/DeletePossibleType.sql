/** 
 * @file DeletePossibleType.sql
 * deletes a specified possible type from %PossibleType table
 * @author  Till Uhlig
 * @param int $etid a %PossibleType identifier
 * @result -
 */
 
DELETE FROM ExerciseType 
WHERE
    ET_id = '$etid'