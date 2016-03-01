<?php
/**
 * @file 2.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

call drop_index_if_exists('File','F_hash_UNIQUE');
call drop_index_if_exists('File','F_address_UNIQUE');
ALTER IGNORE TABLE `File` ADD CONSTRAINT `F_address_displayName_UNIQUE` UNIQUE (`F_address` ASC,`F_displayName` ASC);