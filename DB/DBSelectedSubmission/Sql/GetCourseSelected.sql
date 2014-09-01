<?php
/**
 * @file GetCourseSelected.sql
 * gets an specified selected from %SelectedSubmission table
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result U_id_leader, S_id_selected, E_id
 */
?>

select 
    SS.U_id_leader, SS.S_id_selected, SS.E_id
from
    SelectedSubmission SS
    join 
    ExerciseSheet ES ON ES.ES_id = SS.ES_id
where
    ES.C_id = '<?php echo $courseid; ?>'