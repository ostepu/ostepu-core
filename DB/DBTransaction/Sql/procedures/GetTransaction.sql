<?php
/**
 * @file GetTransaction.sql
 * gets an specified transaction from %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param string \$tid a %Transaction identifier
 * @result
 * - T, the transaction data
 */
?>

DROP PROCEDURE IF EXISTS `DBTransactionGetTransaction`;
CREATE PROCEDURE `DBTransactionGetTransaction` (IN profile varchar(30), IN courseid INT, IN auid varchar(30), IN tid INT, IN random varchar(32))
READS SQL DATA
begin
SET @s = concat("
select
    concat('",courseid,"','_',T.T_id,'_',T.T_random) as T_id,
    T.T_durability,
    T.T_authentication,
    T.T_content
from
    `Transaction",profile,"_",courseid,"` T
where
    T.T_id = '",tid,"'
    and T.T_authentication = '",auid,"'
    and T.T_random = '",random,"'
    and UNIX_TIMESTAMP() <= T.T_durability;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;



    