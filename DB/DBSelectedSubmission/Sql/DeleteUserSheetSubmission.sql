<?php
/**
 * @file DeleteUserSheetSubmission.sql
 * deletes a specified selected submission row from %SelectedSubmission and %Submission table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$userid a %User identifier
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>

DELETE SS FROM SelectedSubmission SS, Submission S
WHERE
    S.U_id = '<?php echo $userid; ?>' and S.ES_id = '<?php echo $esid; ?>' and SS.ES_id = S.ES_id and SS.S_id_selected = S.S_id