<?php
/**
 * @file Navigation.css.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since -
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 */

  header('Content-type: text/css');
  include_once (dirname(__FILE__).'/../../../../include/Config.php');
  $commonImagePath = $externalURI.'/UI/CContent/content/common/img';
?>

.header {
    overflow: auto;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    /* color 1 */
    background-color: #FFF;
    color: #000;
    border: 3px solid #81AB2E;
    padding: 20px 40px;
    display: block;
    background-repeat: no-repeat;

    <?php 
        $month = date('m');
        $day = date('d');
     if ($month == date("m", easter_date(date('Y'))) && $day == date("d", easter_date(date('Y')))) { 
        echo "background-size: 168px 100px;";
        echo "background-image: url('".$commonImagePath ."/events/easter.jpg');";
        echo "background-position: 99% 5px;";
     } elseif ($month == 2 && $day == 14) {
        echo "background-size: 168px 100px;";
        echo "background-image: url('".$commonImagePath ."/events/heart.jpg');";
        echo "background-position: 99% 5px;";
     } elseif ($month == 10 && $day == 31) {
        echo "background-size: 168px 100px;";
        echo "background-image: url('".$commonImagePath ."/events/halloween3.jpg');";
        echo "background-position: 99% 5px;";
     } elseif ($month == 12 && $day >= 10 && $day <= 26) { 
        echo "background-size: 168px 100px;";
        echo "background-image: url('".$commonImagePath ."/events/christmas.jpg');";
        echo "background-position: 99% 5px;";
     } else { 
        if (file_exists($commonImagePath .'/head_logo_hover.png')) { 
            echo "background-image: url('".$commonImagePath ."/head_logo_hover.png');";
         } elseif (file_exists($commonImagePath .'/head_logo_hover.jpg')) { 
            echo "background-image: url('".$commonImagePath ."/head_logo_hover.jpg');";
         } else { 
            echo "background-image: url('".$commonImagePath ."/head_logo_hover_default.jpg');";
         } 
        echo "background-size: 168px 88px;";
        echo "background-position: 99% 10%;";
    }
    ?>