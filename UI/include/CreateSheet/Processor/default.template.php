<?php
/**
 * @file default.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
?>

<?php 
    ob_start();
?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
 <div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <div class="content-body-wrapper" style="padding: 10px; margin-top: 0px;">
        <div class="content-body left" style="width:100%;">
           <!-- default.template.php -->
        </div>
    </div>
</div>
<?php ob_end_flush(); ?>