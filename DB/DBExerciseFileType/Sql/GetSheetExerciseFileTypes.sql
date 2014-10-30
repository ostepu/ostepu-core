<?php
/**
 * @file GetSheetExerciseFileType.sql
 * gets specified exercise file types from %ExerciseFileType table
 * @author Till Uhlig
 * @param int \$esid a %Exercise-Sheet identifier
 * @result EFT_id, EFT_text, E_id
 */
?>
 
select
    EX.EFT_id, EX.EFT_text, EX.E_id
from
    ExerciseFileType EX
    join Exercise E on (EX.E_id = E.E_id)
where
    E.ES_id = '<?php echo $esid; ?>';