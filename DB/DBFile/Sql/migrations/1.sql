<?php
/**
 * @file 1.sql
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

ALTER IGNORE TABLE `File` MODIFY COLUMN F_mimeType VARCHAR(255) NULL;
ALTER IGNORE TABLE `File` MODIFY COLUMN F_timeStamp INT UNSIGNED NULL DEFAULT 0;