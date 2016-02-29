<?php
/**
 * @file AddAttachment.sql
 * inserts an attachment into %Attachment table
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO Attachment SET <?php echo $values; ?>