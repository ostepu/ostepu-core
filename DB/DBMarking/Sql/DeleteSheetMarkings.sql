<?php
/**
 * @file DeleteSheetMarkings.sql
 * deletes all specified markings from %Marking table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>

DELETE FROM Marking
WHERE
    ES_id = '<?php echo $esid; ?>'