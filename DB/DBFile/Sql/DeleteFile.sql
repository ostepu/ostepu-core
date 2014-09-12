<?php
/** 
 * @file DeleteFile.sql
 * deletes a specified file from %File table
 * @author  Till Uhlig
 * @param int \$fileid a %File identifier
 * @result -
 */
?>
set @a = (select F_address from `File` where F_id = '<?php echo $fileid; ?>' limit 1);

Delete from File
    where F_id = '<?php echo $fileid; ?>';
 
SELECT @a as F_address;



