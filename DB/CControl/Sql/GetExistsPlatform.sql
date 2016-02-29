<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
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