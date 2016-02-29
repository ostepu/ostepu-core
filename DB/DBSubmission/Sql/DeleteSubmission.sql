<?php
/**
 * @file DeleteSubmission.sql
 * deletes a specified submission from %Submission table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$suid a %Submission identifier
 * @result -
 */
?>

DELETE FROM Submission
WHERE
    S_id = '<?php echo $suid; ?>'