<?php
/**
 * @file EditSubmissionSelectedSubmission.sql
 * updates a specified selected submission row from %SelectedSubmission table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$suid a %Submission identifier
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE SelectedSubmission
SET <?php echo $values; ?>
WHERE S_id_selected = '<?php echo $suid; ?>'