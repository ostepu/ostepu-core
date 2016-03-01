<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

select
    count(CO_id)
from
    `Component` A,
    `ComponentLinkage` B
limit 1