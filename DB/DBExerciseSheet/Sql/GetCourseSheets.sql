<?php
/**
 * @file GetCourseSheets.sql
 * gets all course exercise sheets
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result 
 * - ES, the exercise sheet data
 * - F, the exercise sheet file
 * - F2, the sample solution file
 */
?>
CALL `DBExerciseSheetGetCourseSheets`('<?php echo $courseid; ?>');