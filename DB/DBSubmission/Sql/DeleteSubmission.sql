<?php
/**
 * @file DeleteSubmission.sql
 * deletes a specified submission from %Submission table
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

DELETE FROM Submission
WHERE
    S_id = '<?php echo $suid; ?>'