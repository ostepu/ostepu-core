<?php
/**
 * @file EditSession.sql
 * updates an specified session from %Session table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param string $seid a %Session identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

UPDATE `Session<?php echo $profile;?>`
SET <?php echo $values; ?>
WHERE SE_sessionID = '<?php echo $seid; ?>'