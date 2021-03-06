<?php
/**
 * @file EditCourse.sql
 * updates a specified course from %Course table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 *
 * @param int $courseid a %Course identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

UPDATE `Course<?php echo $profile;?>`
SET <?php echo $values; ?>
WHERE C_id = '<?php echo $courseid; ?>';