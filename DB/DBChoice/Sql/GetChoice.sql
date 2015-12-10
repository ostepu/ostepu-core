<?php
/**
 * @file GetChoice.sql
 * gets a choice from %Choice table
 * @author Till Uhlig
 * @param int \$choiceid an %Choice identifier
 * @result
 * - CH, the choice data
 */
?>

SET @course = '<?php echo Choice::getCourseFromChoiceId($choiceid); ?>';
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
    `Choice<?php echo $preChoice; ?>_", @course, "` CH
where
    CH.CH_id = '<?php echo Choice::getIdFromChoiceId($choiceid); ?>'");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;