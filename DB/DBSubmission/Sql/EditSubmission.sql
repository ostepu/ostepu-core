<?php
/**
 * @file EditSubmission.sql
 * updates an specified submission from %Submission table
 * @author  Till Uhlig
 * @param int \$suid a %Submission identifier
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE Submission
SET <?php echo $values; ?>
WHERE S_id = '<?php echo $suid; ?>'