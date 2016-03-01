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

INSERT
IGNORE INTO `SelectedSubmission`
SELECT NULL,
       S.S_id,
       NULL,
       NULL
FROM Submission S
JOIN `Group` G ON (S.U_id = G.U_id_leader
                   AND S.ES_id = G.ES_id);