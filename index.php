<?php
     include 'Header.php';
?>

<!DOCTYPE HTML>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="CSSReset.css"> 
    <link rel="stylesheet" type="text/css" href="Uebungsplattform.css">
    <title>Übungsplattform</title>
    </head>
    <body>
    <div id="body-wrapper">

<?php
    $h = new Header("Datenstrukturen",
                    "",
                    "Florian Lücke",
                    "211221492", 
                    "75%");
    $h->show();
?>
        <div id="content-wrapper">
        <div class="exercise-sheet-wrapper">
            <div class="exercise-sheet-header">
            <div class="exercise-sheet-title">Serie 2</div>
            <div class="exercise-sheet-end">23.11.2013 20:003</div>
            <div class="exercise-sheet-percent">100%</div>          
            </div>

            <div class="exercise-sheet-body-wrapper">
            <div class="exercise-sheet-body-left exercise-sheet-body">
                <ol class="exercise-list">
                    <li><div class="exercise-type">Normal</div> <div class="exercise-points">0/10</div> </li>
                    <li><div class="exercise-type">Bonus</div> <div class="exercise-points">0/10</div> </li>                    
                </ol>
            </div>

            <div class="exercise-sheet-body-extras exercise-sheet-body">
                <ol class="body-options">
                <li><a class="body-option" href="#">Serie ansehen</a></li>
                <li><a class="body-option" href="#">Musterlösung</a></li>
                <li><a class="body-option" href="Gruppensystem.php">Gruppe</a></li>
                <li><a class="body-option" href="#">Gruppe verlassen</a></li>
                </ol>
            </div>
            </div> <!-- end: exercise-sheet-body-wrapper -->
        </div> <!-- end: exercise-sheet-wrapper -->
        </div> <!-- end: content-wrapper -->
    </div>
    </body>
</html>

