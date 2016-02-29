<?php
/**
 * @file AddSession.sql
 * inserts a session into %Session table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Lisa Dietrich <Lisa.Dietrich@student.uni-halle.de>
 * @date 2014
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO Session (U_id, SE_sessionID)
VALUES ('<?php echo $userid; ?>', '<?php echo $sessionid; ?>')
ON DUPLICATE KEY UPDATE SE_sessionID = '<?php echo $sessionid; ?>'