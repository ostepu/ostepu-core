/**
 * @file EditPossibleType.sql
 * updates an specified possible type from %PossibleType table
 * @author  Till Uhlig
 * @param int $etid a %PossibleType identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */
 
UPDATE ExerciseType
SET $values
WHERE ET_id = $etid