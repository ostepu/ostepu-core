<?php
/** 
 * @file DeleteUserSheetSubmission.sql
 * deletes a specified selected submission row from %SelectedSubmission and %Submission table
 * @author  Till Uhlig
 * @param int \$userid a %User identifier
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>
 
DELETE SS FROM SelectedSubmission SS, Submission S
WHERE
    S.U_id = '<?php echo $userid; ?>' and S.ES_id = '<?php echo $esid; ?>' and SS.ES_id = S.ES_id and SS.S_id_selected = S.S_id