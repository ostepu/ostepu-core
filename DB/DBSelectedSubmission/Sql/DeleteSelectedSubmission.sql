<?php
/**
 * @file DeleteSelectedSubmission.sql
 * deletes a specified selected submission row from %SelectedSubmission table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$userid a %User identifier
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM SelectedSubmission
WHERE
    U_id_leader = '<?php echo $userid; ?>' and E_id = '<?php echo $eid; ?>'