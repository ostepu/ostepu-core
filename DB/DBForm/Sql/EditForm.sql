<?php
/**
 * @file EditForm.sql
 * updates a specified form from %Form table
 * @author  Till Uhlig
 * @param int \$formid a %Form identifier
 * @result -
 */
?>

UPDATE `Form_<?php echo Form::getCourseFromFormId($formid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE FO_id = '<?php echo Form::getIdFromFormId($formid); ?>'