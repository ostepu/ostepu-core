/**
 * @file GetExerciseSelected.sql
 * gets an specified selected from %SelectedSubmission table
 * @author Till Uhlig
 * @param int \$eid a %Exercise identifier
 * @result U_id_leader, S_id_selected, E_id
 */

select 
    U_id_leader, S_id_selected, E_id
from
    SelectedSubmission
where
    E_id = '$eid'