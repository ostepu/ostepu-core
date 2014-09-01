<?php
/**
 * @file GetExistsPlatform.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `SelectedSubmission` A limit 1;