<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExternalIdGetExistsPlatform`;
CREATE PROCEDURE `DBExternalIdGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExternalId';
end;