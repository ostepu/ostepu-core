<?php
/**
 * @file test.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.6
 *
 * @author Florian Lücke <florian.luecke2@gmail.com>
 * @date 2015
 */


require_once(dirname(__FILE__).'/phplatex.php');

echo texify('$\(x = {-b \pm \sqrt{b^2-4ac} \over 2a}\)$');
?>
<img src="texImages/A1.png"><img src="texImages/a.svg">
