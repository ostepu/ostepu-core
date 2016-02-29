<?php
/**
 * @file DeleteUserPermanent.sql
 * deletes a specified user from %User table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param string \$userid a %User identifier or username
 * @result -
 */
?>

DELETE FROM User
WHERE
    U_id like '<?php echo $userid; ?>' or U_username = '<?php echo $userid; ?>' or U_externalId = '<?php echo $userid; ?>'