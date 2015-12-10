<?php
/**
 * @file EditChoice.sql
 * updates a specified choice from %Choice table
 * @author  Till Uhlig
 * @param int \$choiceid a %Choice identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE `Choice<?php echo $preChoice; ?>_<?php echo Choice::getCourseFromChoiceId($choiceid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE CH_id = '<?php echo Choice::getIdFromChoiceId($choiceid); ?>'