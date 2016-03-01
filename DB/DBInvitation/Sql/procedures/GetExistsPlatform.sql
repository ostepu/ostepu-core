<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBInvitationGetExistsPlatform`;
CREATE PROCEDURE `DBInvitationGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Invitation';
end;