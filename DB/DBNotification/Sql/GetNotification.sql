<?php
/**
 * @file GetNotification.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

/**
 * @file GetNotification.sql
 * gets a setting from %Notification table
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
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