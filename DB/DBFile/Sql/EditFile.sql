<?php
/**
 * @file EditFile.sql
 * updates an specified file from %File table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$fileid a %File identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE File
SET <?php echo $values; ?>
WHERE F_id = '<?php echo $fileid; ?>'