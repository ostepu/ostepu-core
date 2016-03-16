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

 $myProcess = Process::decodeProcess($object->getProcess(),false);
 $object->setProcess($myProcess);

?>

UPDATE `Testcase_<?php echo trim($object->getProcess()->getObjectCourseFromProcessIdId()); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE OOP_id = '<?php echo trim($testcaseid); ?>'


