<?php
/**
 * @file GetSheetSelected.sql
 * gets an specified selected from %SelectedSubmission table
 * @author Till Uhlig
 * @param int \$esid a %ExerciseSheet identifier
 * @result U_id_leader, S_id_selected, E_id
 */
?>

select 
    U_id_leader, S_id_selected, E_id
from
    SelectedSubmission
where
    ES_id = '<?php echo $esid; ?>'