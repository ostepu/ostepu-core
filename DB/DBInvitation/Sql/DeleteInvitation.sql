<?php
/**
 * @file DeleteInvitation.sql
 * deletes a specified group entry from %Invitation table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Jörg Baumgarten <kuroi.tatsu@freenet.de>
 * @date 2014
 * @param int \$esid a %ExerciceSheet identifier
 * @param int \$userid a %User identifier
 * @param int \$memberid a %Invitation identifier
 * @result -
 */
?>

DELETE FROM Invitation
WHERE
    ES_id = '<?php echo $esid; ?>' and U_id_leader = '<?php echo $memberid; ?>' and U_id_member = '<?php echo $userid; ?>'