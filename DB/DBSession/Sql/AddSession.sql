<?php
/**
 * @file AddSession.sql
 * inserts a session into %Session table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Lisa Dietrich <Lisa.Dietrich@student.uni-halle.de>
 * @date 2014
 *
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

INSERT INTO `Session<?php echo $profile;?>` (U_id, SE_sessionID)
VALUES ('<?php echo $userid; ?>', '<?php echo $sessionid; ?>')
ON DUPLICATE KEY UPDATE SE_sessionID = '<?php echo $sessionid; ?>'