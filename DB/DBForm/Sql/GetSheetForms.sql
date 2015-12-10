<?php
/**
 * @file GetSheetForms.sql
 * gets forms from %Form table
 * @author Till Uhlig
 * @param int \$esid an %Sheet identifier
 * @result
 * - FO, the form data
 * - CH, the choice data
 */
?>

SET @course = (select E.C_id from `Exercise` E where E.ES_id = <?php echo $esid; ?> limit 1);
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
    FO.ES_id = '<?php echo $esid; ?>'");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;