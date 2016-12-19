<?php
/**
 * @file DeleteFile.sql
 * deletes a specified file from %File table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$fileid a %File identifier
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

set @a = (select F_address from `File<?php echo $profile;?>` where F_id = '<?php echo $fileid; ?>' limit 1);

Delete from `File<?php echo $profile;?>`
    where F_id = '<?php echo $fileid; ?>';

select A.`F_address` from (SELECT @a as F_address, count(*) as 'count' from `File<?php echo $profile;?>` where F_address = @a) A where A.`count` = 0;



