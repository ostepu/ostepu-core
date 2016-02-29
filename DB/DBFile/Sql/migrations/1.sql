<?php
/**
 * @file 1.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

ALTER IGNORE TABLE `File` MODIFY COLUMN F_mimeType VARCHAR(255) NULL;
ALTER IGNORE TABLE `File` MODIFY COLUMN F_timeStamp INT UNSIGNED NULL DEFAULT 0;