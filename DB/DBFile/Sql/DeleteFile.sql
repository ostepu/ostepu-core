<?php
/**
 * @file DeleteFile.sql
 * deletes a specified file from %File table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$fileid a %File identifier
 * @result -
 */
?>
set @a = (select F_address from `File` where F_id = '<?php echo $fileid; ?>' limit 1);

Delete from File
    where F_id = '<?php echo $fileid; ?>';

select A.`F_address` from (SELECT @a as F_address, count(*) as 'count' from `File` where F_address = @a) A where A.`count` = 0;



