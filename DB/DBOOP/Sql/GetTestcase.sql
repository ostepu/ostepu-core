<?php
/**
 * @file insertTestcase.sql
 * inserts a testcase into %Testcase table
 * @author  Ralf Busch
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */

?>

SELECT *
FROM `Testcase_<?php echo trim($courseid); ?>`
WHERE OOP_submission = '<?php echo trim($submissionid); ?>'