<?php
/**
 * @file GetCourseSelected.sql
 * gets an specified selected from %SelectedSubmission table
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result U_id_leader, S_id_selected, E_id
 */
?>
CALL `DBSelectedSubmissionGetCourseSelected`('<?php echo $userid; ?>');