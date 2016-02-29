<?php
/**
 * @file DeleteUserSession.sql
 * deletes a specified session from %Session table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$userid a %User identifier
 * @result -
 */
?>

DELETE FROM `Session`
WHERE
    U_id = '<?php echo $userid; ?>'