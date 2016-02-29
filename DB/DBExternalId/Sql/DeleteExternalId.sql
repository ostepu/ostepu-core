<?php
/**
 * @file DeleteExternalId.sql
 * deletes an specified external id from %ExternalId table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$aid a %ExternalId identifier
 * @result -
 */
?>

DELETE FROM ExternalId
WHERE
    EX_id = '<?php echo $exid; ?>'