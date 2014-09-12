<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Group` A limit 1;