<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Marking` A limit 1;