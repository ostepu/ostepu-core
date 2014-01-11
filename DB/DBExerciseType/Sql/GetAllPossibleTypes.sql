/**
 * @file GetAllPossibleTypes.sql
 * gets all possible types from %PossibleType table
 * @author Till Uhlig
 * @result ET_id, ET_name
 */
 
select 
    ET_id, ET_name
from
    ExerciseType