<?php
/**
 * @file EditProcess.sql
 * updates a specified process from %Process table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$processid a %Process identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE `Process<?php echo $pre; ?>_<?php echo Process::getCourseFromProcessId($processid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE PRO_id = '<?php echo Process::getIdFromProcessId($processid); ?>'