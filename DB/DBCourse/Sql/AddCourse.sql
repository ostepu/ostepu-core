<?php
/**
 * @file AddCourse.sql
 * inserts a course into %Course table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT IGNORE INTO Course 
SET <?php echo $values; ?> ON DUPLICATE KEY UPDATE <?php echo $values; ?>;
SET @a = <?php if ($in->getId()!==null){echo "'".$in->getId()."';";} else echo "LAST_INSERT_ID();"; ?>

<?php if ($in->getId()===null){ ?>
SET @statement = 
concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type)
VALUES ('RegistrationPeriodEnd', '0' ,'TIMESTAMP');");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
DROP PREPARE stmt1;

SET @statement = 
concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type)
VALUES ('AllowLateSubmissions', '1' ,'BOOL');");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
DROP PREPARE stmt1;
<?php } ?>

select @a;