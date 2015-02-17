DROP PROCEDURE IF EXISTS `DBCourseStatusGetExistsPlatform`;
CREATE PROCEDURE `DBCourseStatusGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'CourseStatus';
end;