<?php
/**
 * @file DeleteChoice.sql
 * deletes a specified choice from %Choice table
 * @author  Till Uhlig
 * @param int \$choiceid a %Choice identifier
 * @result -
 */
?>

DELETE FROM `Choice<?php echo $preChoice; ?>_<?php echo Choice::getCourseFromChoiceId($choiceid); ?>`
WHERE
    CH_id = '<?php echo Choice::getIdFromChoiceId($choiceid); ?>'