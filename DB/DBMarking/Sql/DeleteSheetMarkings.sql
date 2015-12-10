<?php
/**
 * @file DeleteSheetMarkings.sql
 * deletes all specified markings from %Marking table
 * @author  Till Uhlig
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>

DELETE FROM Marking
WHERE
    ES_id = '<?php echo $esid; ?>'