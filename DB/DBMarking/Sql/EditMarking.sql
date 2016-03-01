<?php
/**
 * @file EditMarking.sql
 * updates a specified marking from %Marking table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$mid a %Marking identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE Marking
SET <?php echo $values; ?>
WHERE M_id = '<?php echo $mid; ?>'