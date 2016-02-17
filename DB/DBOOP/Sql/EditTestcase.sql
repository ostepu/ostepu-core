<?php
/**
 * @file insertTestcase.sql
 * inserts a testcase into %Testcase table
 * @author  Ralf Busch
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */

 $myProcess = Process::decodeProcess($object->getProcess(),false);
 $object->setProcess($myProcess);

?>

UPDATE `Testcase_<?php echo trim($object->getProcess()->getObjectCourseFromProcessIdId()); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE OOP_id = '<?php echo trim($testcaseid); ?>'


