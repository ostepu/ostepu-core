<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBCourseGetExistsPlatform`;
CREATE PROCEDURE `DBCourseGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Course';
end;