/**
 * @file EditForm.sql
 * updates a specified form from %Form table
 * @author  Till Uhlig
 * @param int \$formid a %Form identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
UPDATE `Form_".Form::getCourseFromFormId($formid)."`
SET {$object->getInsertData()}
WHERE FO_id = '".Form::getIdFromFormId($formid)."'