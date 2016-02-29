<?php
/**
 * @file EditSubmission.sql
 * updates an specified submission from %Submission table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$suid a %Submission identifier
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE Submission
SET <?php echo $values; ?>
WHERE S_id = '<?php echo $suid; ?>'