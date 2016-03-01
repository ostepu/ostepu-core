<?php
/**
 * @file DeleteProcess.sql
 * deletes a specified process from %Process table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$processid a %Process identifier
 * @result -
 */
?>

DELETE FROM `Process<?php echo $pre; ?>_<?php echo Process::getCourseFromProcessId($processid); ?>`
WHERE
    PRO_id = '<?php echo Process::getIdFromProcessId($processid); ?>'