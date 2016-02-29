<?php
/**
 * @file DeleteSession.sql
 * deletes an specified session from %Session table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param string $seid a %Session identifier
 * @result -
 */
?>

DELETE FROM `Session`
WHERE
    SE_sessionID = '<?php echo $seid; ?>'