<?php
/**
 * @file AddSetting.sql
 * inserts an Setting into %Setting table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

INSERT INTO `Setting<?php echo $profile; ?>_<?php echo $courseid; ?>` SET <?php echo $in->getInsertData(); ?>
ON DUPLICATE KEY UPDATE <?php echo $in->getInsertData(); ?>;
select '<?php echo $courseid; ?>' as 'C_id';