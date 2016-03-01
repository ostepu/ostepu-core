<?php
/**
 * @file CleanTransactions.sql
 * removes expired transactions from %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @result -
 */
?>

delete from `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>`
where T_durability < UNIX_TIMESTAMP();

