<?php
/**
 * @file GetNotification.sql
 * gets a setting from %Notification table
 * @author Till Uhlig
 * @param int \$notid an %Notification identifier
 * @result
 * - S, the Notification data
 */
?>
<?php   $courseId = Notification::getCourseFromNotificationId($notid);
        $tableName = 'Notification'.$pre.($courseId!==''?'_'.$courseId:''); ?>

select
    concat('<?php echo Notification::getCourseFromNotificationId($notid); ?>','_',N.NOT_id) as NOT_id,
    N.*
from
    `<?php echo $tableName;?>` N
WHERE NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'
and N.NOT_begin >= UNIX_TIMESTAMP() and N.NOT_end <= UNIX_TIMESTAMP();;