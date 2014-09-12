<?php
/**
 * @file GetExerciseFileType.sql
 * gets an specified file tyoe type from %ExerciseFileType table
 * @author Till Uhlig
 * @param int \$eftid a %ExerciseFileType identifier
 * @result EFT_id, EFT_text, E_id
 */
?>
 
select 
    EFT_id, EFT_text, E_id
from
    ExerciseFileType
where
    EFT_id = '<?php echo $eftid; ?>'