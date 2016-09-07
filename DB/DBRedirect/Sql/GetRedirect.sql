<?php
/**
 * @file GetRedirect.sql
 * gets a redirect from %Redirect table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 * @param int \$redid an %Redirect identifier
 * @result
 * - S, the Redirect data
 */
?>

select
    S.*,
    concat('<?php echo Redirect::getCourseFromRedirectId($redid); ?>','_',S.RED_id) as RED_id
from
    `Redirect<?php echo $pre; ?>_<?php echo Redirect::getCourseFromRedirectId($redid); ?>` S
WHERE RED_id = '<?php echo Redirect::getIdFromRedirectId($redid); ?>'