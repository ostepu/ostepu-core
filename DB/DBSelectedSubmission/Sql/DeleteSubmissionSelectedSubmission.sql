<?php
/**
 * @file DeleteSubmissionSelectedSubmission.sql
 * deletes a specified selected submission row from %SelectedSubmission table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$suid a %Submission identifier
 * @result -
 */
?>

DELETE FROM SelectedSubmission
WHERE
    S_id_selected = '<?php echo $suid; ?>'