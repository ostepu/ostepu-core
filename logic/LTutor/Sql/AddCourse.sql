<?php
/**
 * @file AddCourse.sql
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 * @result -
 */
?>
SET @a = <?php if ($in->getId()!==null){echo "'".$in->getId()."';";} ?>
call execute_if_table_exists(concat('Setting_',@a),concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type, SET_category) VALUES ('GenerateDummyCorrectionsForTutorArchives', '0' ,'BOOL', 'markings');"));
call execute_if_table_exists(concat('Setting_',@a),concat("INSERT IGNORE INTO Setting_",@a," (SET_name, SET_state, SET_type, SET_category) VALUES ('InsertStudentNamesIntoTutorArchives', '0' ,'BOOL', 'markings');"));
select @a;