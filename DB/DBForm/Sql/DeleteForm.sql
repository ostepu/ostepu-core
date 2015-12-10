<?php
/**
 * @file DeleteForm.sql
 * deletes a specified form from %Form table
 * @author  Till Uhlig
 * @param int \$formid a %Form identifier
 * @result -
 */
?>

DELETE FROM `Form_<?php echo Form::getCourseFromFormId($formid); ?>`
WHERE
    FO_id = '<?php echo Form::getIdFromFormId($formid); ?>'