<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Attachment` A limit 1;