<?php
/**
 * @file AddCourse.sql
 * @author  Till Uhlig
 * @result -
 */
?>
SET @a = <?php if ($in->getId()!==null){echo "'".$in->getId()."';";} ?>
call execute_if_table_exists(concat('Setting_',@a),concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type, SET_category) VALUES ('GenerateDummyCorrectionsForTutorArchives', '0' ,'BOOL', 'markings');"));
select @a;