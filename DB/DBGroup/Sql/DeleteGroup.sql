<?php
/** 
 * @file DeleteGroup.sql
 * deletes a specified group entry from %Group table
 * @author  Till Uhlig
 * @param int \$esid a %ExerciseSheet identifier
 * @param int \$userid a %User identifier
 * @result -
 */
?>
 
DELETE FROM `Group` 
WHERE
    ES_id = '<?php echo $esid; ?>' and U_id_leader = '<?php echo $userid; ?>'