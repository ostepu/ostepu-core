<?php
/**
 * @file GetCourseExercises.sql
 * gets all course exercises from %Exercise table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - E, the exercise data
 * - F, the submission file
 * - S, the submission data
 * - SS, the selected submission data
 */
?>
CALL `DBExerciseSheetGetCourseExercises`('<?php echo $courseid; ?>');