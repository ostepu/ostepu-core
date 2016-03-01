<?php
/**
 * @file EditUser.sql
 * updates an specified user from %User table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 *
 * @param string \$userid a %User identifier or username
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE User
SET <?php echo $values; ?>
WHERE U_id like '<?php echo $userid; ?>' or U_username = '<?php echo $userid; ?>' or U_externalId = '<?php echo $userid; ?>'