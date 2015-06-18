SELECT distinct table_name, referenced_table_name
  FROM information_schema.key_column_usage
 WHERE table_schema   = '<?php echo $conf['DB']['db_name']; ?>' and referenced_table_name is not null;