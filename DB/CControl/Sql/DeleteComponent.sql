<?php
/**
 * @file DeleteComponent.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 */
?>

DELETE FROM Component
WHERE
    CO_id = '<?php echo $componentid; ?>'
    or CO_name = '<?php echo $componentid; ?>'