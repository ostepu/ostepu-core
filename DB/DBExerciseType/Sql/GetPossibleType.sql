/**
 * @file GetPossibleType.sql
 * gets an specified possible type from %PossibleType table
 * @author Till Uhlig
 * @param int \$etid a %PossibleType identifier
 * @result ET_id, ET_name
 */
 
select 
    ET_id, ET_name
from
    ExerciseType
where
    ET_id = '$etid'