<?php
/**
 * @file GetTableReferences.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

SELECT distinct table_name, referenced_table_name
  FROM information_schema.key_column_usage
 WHERE table_schema   = '<?php echo $conf['DB']['db_name']; ?>' and referenced_table_name is not null;