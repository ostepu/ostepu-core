<?php
/**
 * @file DeleteRedirect.sql
 * deletes an specified Redirect from %Redirect table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$redid a %Redirect identifier
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

DELETE FROM `Redirect<?php echo $profile; ?>_<?php echo Redirect::getCourseFromRedirectId($redid); ?>`
WHERE
    RED_id = '<?php echo Redirect::getIdFromRedirectId($redid); ?>'

