/**
 * @file EditExercise.sql
 * updates an specified exercise from %Exercise table
 * @author  Till Uhlig
 * @param int $eid an %Exercise identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */
 
UPDATE Exercise
SET $values
WHERE E_id = $eid