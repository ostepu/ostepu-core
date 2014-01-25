/**
 * @file GetExerciseType.sql
 * gets an specified possible type from %ExerciseType table
 * @author Till Uhlig
 * @param int \$etid a %ExerciseType identifier
 * @result ET_id, ET_name
 */
 
select 
    ET_id, ET_name
from
    ExerciseType
where
    ET_id = '$etid'