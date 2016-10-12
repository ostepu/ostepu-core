<?php
/**
 * @file AddFile.sql
 * inserts an file into %File table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

INSERT INTO `File<?php echo $profile;?>` SET <?php echo $values; ?>
ON DUPLICATE KEY UPDATE `F_id`=LAST_INSERT_ID(`F_id`),<?php echo $values; ?>;
SELECT LAST_INSERT_ID() as 'ID';