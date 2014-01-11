/**
 * @file EditExerciseSheet.sql
 * updates an specified exercise sheet from %ExerciseSheet table
 * @author  Till Uhlig
 * @param int $esid an %ExerciseSheet identifier
 * @param string $values the input data, e.g. "a=1, b=2"
 * @result -
 */
 
UPDATE ExerciseSheet
SET $values
WHERE ES_id = $esid