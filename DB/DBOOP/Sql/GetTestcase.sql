<?php
/**
 * @file insertTestcase.sql
 * inserts a testcase into %Testcase table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2016
 *
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

SELECT *
FROM `Testcase_<?php echo trim($courseid); ?>`
WHERE OOP_submission = '<?php echo trim($submissionid); ?>'