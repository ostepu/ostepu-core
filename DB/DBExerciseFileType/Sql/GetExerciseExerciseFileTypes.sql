<?php
/**
 * @file GetExerciseExerciseFileType.sql
 * gets specified exercise file types from %ExerciseFileType table
 * @author Till Uhlig
 * @param int \$eid a %Exercise identifier
 * @result EFT_id, EFT_text, E_id
 */
?>
 
select 
    EFT_id, EFT_text, E_id
from
    ExerciseFileType
where
    E_id = '<?php echo $eid; ?>'