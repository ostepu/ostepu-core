<?php
/**
 * @file DeleteSubmissionSelectedSubmission.sql
 * deletes a specified selected submission row from %SelectedSubmission table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$suid a %Submission identifier
 * @result -
 */
?>

DELETE FROM SelectedSubmission
WHERE
    S_id_selected = '<?php echo $suid; ?>'