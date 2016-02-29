<?php
/**
 * @file DeleteUser.sql
 * updates a user from %User table, sets the flag to zero
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @param string $userid a %User identifier or username
 * @result -
 */
?>

update User
set
    U_flag = 0
where
    U_id like '<?php echo $userid; ?>' or U_username = '<?php echo $userid; ?>' or U_externalId = '<?php echo $userid; ?>'