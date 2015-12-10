<?php
/**
 * @file GetForm.sql
 * gets a form from %Form table
 * @author Till Uhlig
 * @param int \$formid an %Form identifier
 * @result
 * - FO, the form data
 * - CH, the choice data
 */
?>

SET @course = '<?php echo Form::getCourseFromFormId($formid); ?>';
SET @statement =
concat(
"select
    concat('", @course ,"','_',FO.FO_id) as FO_id,
    FO.FO_type,
    FO.FO_solution,
    FO.FO_task,
    FO.E_id,
    concat('", @course ,"','_',CH.CH_id) as CH_id,
    CH.CH_text,
    CH.CH_correct
from
    `Form_", @course, "` FO
        left join
    `Choice_", @course, "` CH ON FO.FO_id = CH.FO_id
where
    FO.FO_id = '<?php echo Form::getIdFromFormId($formid); ?>'");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;