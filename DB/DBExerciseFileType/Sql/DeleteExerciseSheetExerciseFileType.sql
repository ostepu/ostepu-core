<?php
/** 
 * @file DeleteExerciseSheetExerciseFileType.sql
 * deletes specified exercise file types from %ExerciseFileType table
 * @author  Till Uhlig
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>

DELETE EFT FROM ExerciseFileType EFT, Exercise E
WHERE
    E.ES_id = '<?php echo $esid; ?>' and E.E_id = EFT.E_id;