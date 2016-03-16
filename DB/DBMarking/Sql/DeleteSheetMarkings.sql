<?php
/**
 * @file DeleteSheetMarkings.sql
 * deletes all specified markings from %Marking table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>

DELETE FROM Marking
WHERE
    ES_id = '<?php echo $esid; ?>'