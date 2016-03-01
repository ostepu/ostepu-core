<?php
/**
 * @file DeleteLink.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */
?>

DELETE FROM ComponentLinkage
WHERE
    CL_id = '<?php echo $linkid; ?>' or CO_id_owner = (select CO_id from Component where CO_name = '<?php echo $linkid; ?>' limit 1);