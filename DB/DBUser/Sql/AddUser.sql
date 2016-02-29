<?php
/**
 * @file AddUser.sql
 * inserts a user into %User table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2015
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

insert into User SET <?php echo $values; ?>;

