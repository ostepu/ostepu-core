<?php
/**
 * @file GetSheetChoices.sql
 * gets choices from %Choice table
 * @author Till Uhlig
 * @param int \$esid an %Sheet identifier
 * @result
 * - CH, the choice data
 */
?>

SET @course = (select E.C_id from `Exercise<?php echo $preExercise; ?>` E where E.ES_id = <?php echo $esid; ?> limit 1);
SET @statement =
concat(
"select
    concat('", @course ,"','_',CH.CH_id) as CH_id,
    CH.FO_id,
    CH.E_id,
    CH.CH_text,
    CH.CH_correct,
    CH.S_id
from
    `Form<?php echo $preForm; ?>_",@course,"` FO
        left join
    `Choice<?php echo $preChoice; ?>_",@course,"` CH ON FO.FO_id = CH.FO_id
where
    FO.ES_id = '<?php echo $esid; ?>'");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;