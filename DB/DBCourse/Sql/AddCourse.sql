<?php
/**
 * @file AddCourse.sql
 * inserts a course into %Course table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

INSERT IGNORE INTO `Course<?php echo $profile;?>`
SET <?php echo $values; ?> ON DUPLICATE KEY UPDATE <?php echo $values; ?>;
SET @a = <?php if ($in->getId()!==null){echo "'".$in->getId()."';";} else echo "LAST_INSERT_ID();"; ?>

call execute_if_table_exists(concat('Setting_',@a),concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type) VALUES ('RegistrationPeriodEnd', '0' ,'TIMESTAMP');"));
call execute_if_table_exists(concat('Setting_',@a),concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type) VALUES ('AllowLateSubmissions', '1' ,'BOOL');"));
call execute_if_table_exists(concat('Setting_',@a),concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type) VALUES ('MaxStudentUploadSize', '2097152' ,'INT');"));

call execute_if_column_exists(concat('Setting_',@a),'SET_category',concat("UPDATE IGNORE Setting_",@a," SET SET_category = 'userManagement' WHERE SET_name = 'RegistrationPeriodEnd';"));
call execute_if_column_exists(concat('Setting_',@a),'SET_category',concat("UPDATE IGNORE Setting_",@a," SET SET_category = 'submissions' WHERE SET_name = 'AllowLateSubmissions';"));
call execute_if_column_exists(concat('Setting_',@a),'SET_category',concat("UPDATE IGNORE Setting_",@a," SET SET_category = 'submissions' WHERE SET_name = 'MaxStudentUploadSize';"));
select @a;