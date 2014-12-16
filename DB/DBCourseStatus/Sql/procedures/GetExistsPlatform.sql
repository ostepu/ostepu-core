DROP PROCEDURE IF EXISTS `DBCourseStatusGetExistsPlatform`;
CREATE PROCEDURE `DBCourseStatusGetExistsPlatform` ()
begin
show tables like 'CourseStatus';
end;