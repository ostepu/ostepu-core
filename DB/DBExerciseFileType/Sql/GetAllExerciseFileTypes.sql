<?php
/**
 * @file GetAllExerciseFileTypes.sql
 * gets all exercise file types from %ExerciseFileType table
 * @author Till Uhlig
 * @result EFT_id, EFT_text, E_id
 */
?>
 
select 
    EFT_id, EFT_text, E_id
from
    ExerciseFileType