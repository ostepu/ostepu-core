/**
 * @file EditForm.sql
 * updates a specified form from %Form table
 * @author  Till Uhlig
 * @param int \$formid a %Form identifier
 * @result -
 */
 
UPDATE `Form_".Form::getCourseFromFormId($formid)."`
SET {$object->getInsertData()}
WHERE FO_id = '".Form::getIdFromFormId($formid)."'