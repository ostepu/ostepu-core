<?php
/**
 * @file Samples.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

SET @row = 0;
SET @a = (SELECT count(*) FROM File)-1;

INSERT
IGNORE INTO `Submission<?php echo $profile;?>`
SELECT C.U_id as A, @row := @row + 1,
                       FLOOR(1 + (RAND() * @a)) as B,
                       NULL as C,
                       UNIX_TIMESTAMP(now())-FLOOR(0 + (RAND() * 60*60*24*60)) as D,
                       1 as E,
                       E.E_id as F,
                       E.ES_id as G,
                       NULL as H,
                       NULL as I,
                       NULL as J
FROM `Exercise<?php echo $exerciseProfile;?>` E
JOIN `CourseStatus<?php echo $courseStatusProfile;?>` C ON (E.C_id=C.C_id
                        AND C.CS_status=0);