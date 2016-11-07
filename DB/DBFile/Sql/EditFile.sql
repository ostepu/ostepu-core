<?php
/**
 * @file EditFile.sql
 * updates an specified file from %File table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$fileid a %File identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

UPDATE `File<?php echo $profile;?>`
SET <?php echo $values; ?>
WHERE F_id = '<?php echo $fileid; ?>'