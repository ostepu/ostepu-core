<?php
include_once 'include/Header/Header.php';
include_once 'ExerciseSheet.php';
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="CSSReset.css"> 
    <link rel="stylesheet" type="text/css" href="Uebungsplattform.css">
    <title>Übungsplattform</title>
</head>
<body>
    <div id="body-wrapper" class="body-wrapper">

        <?php
        $h = new Header("Datenstrukturen",
                        "",
                        "Florian Lücke",
                        "211221492", 
                        "75%");
        $h->show();
        ?>
        <div id="content-wrapper" class="content-wrapper">
            <?php 
            $s = new ExerciseSheet("Serie 2", array(
                                   array("exerciseType" => "Normal",
                                         "points" => "10",
                                         "maxPoints" => "10"
                                         ),
                                   array("exerciseType" => "Bonus",
                                         "points" => "10",
                                         "maxPoints" => "10"
                                         ),
                                   ));
            $s->show();
            ?>
        </div> <!-- end: content-wrapper -->
    </div>
</body>
</html>
