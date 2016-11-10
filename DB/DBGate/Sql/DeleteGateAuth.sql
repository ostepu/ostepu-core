<?php
/**
 * @file DeleteGateAuth.sql
 * deletes a specified auth method from %GateAuth table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

DELETE FROM `GateAuth<?php echo $profile;?>`
WHERE
    GA_id = '<?php echo $gaid; ?>'