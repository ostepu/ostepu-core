<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `ExternalId` A limit 1;