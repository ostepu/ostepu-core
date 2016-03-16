<?php
/**
 * @file DeleteExternalId.sql
 * deletes an specified external id from %ExternalId table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$aid a %ExternalId identifier
 * @result -
 */
?>

DELETE FROM ExternalId
WHERE
    EX_id = '<?php echo $exid; ?>'