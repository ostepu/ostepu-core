<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';

$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492", 
                "75%");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

$manageGroup = '<div class="content">
    <div class="exercise-sheet-header">
        <div class="exercise-sheet-title">Gruppe verwalten</div>
        <div class="exercise-sheet-end">
            <a href="#" class="body-option">verlassen</a>
        </div>   
    </div>

    <div class="exercise-sheet-body-wrapper">
        <div class="exercise-sheet-body-left exercise-sheet-body">
            <ol class="exercise-list invitation-list">
                <li >
                    Jörg Baumgarten
                    <a href="#" class="body-option deny-button">
                        hinauswerfen
                    </a>
                </li>
                <li >
                    Lisa Dietrich
                    <a href="#" class="body-option deny-button">
                        hinauswerfen
                    </a>
                </li>
            </ol>
        </div>
    </div> <!-- end: exercise-sheet-body-wrapper -->
</div> <!-- end: exercise-sheet-wrapper -->';

$createGroup = '<div class="content">
    <div class="exercise-sheet-header">
        <div class="exercise-sheet-title">Erstellen</div>
        <div class="exercise-sheet-end">
            <a href="#" class="body-option">einladen</a>
        </div>     
    </div>

    <div class="exercise-sheet-body-wrapper">
        <div class="exercise-sheet-body-left exercise-sheet-body">
            <ol class="exercise-list invitation-list">
                <li>
                    Matrikel: <input type="text" name="ids[]" size="50">
                </li>
                <li>
                    Matrikel: <input type="text" name="ids[]" size="50">
                </li>
                <li>
                    Matrikel: <input type="text" name="ids[]" size="50">
                </li>
            </ol>
        </div>
    </div> <!-- end: exercise-sheet-body-wrapper -->
</div> <!-- end: exercise-sheet-wrapper -->';

$invitations = '<div class="content">
    <div class="exercise-sheet-header">
        <div class="exercise-sheet-title">Einladungen</div>     
    </div>

    <div class="exercise-sheet-body-wrapper">
        <div class="exercise-sheet-body-left exercise-sheet-body">
            <ol class="exercise-list invitation-list">
                <li >
                    Peter König, Felix Schmidt
                    <a href="#" class="body-option deny-button">
                        ablehnen
                    </a>
                    <a href="#" class="body-option accept-button">
                        annehmen
                    </a>
                </li>
                <li >
                    Till Uhlig, Ralf Busch
                    <a href="#" class="body-option deny-button">
                        ablehnen
                    </a>
                    <a href="#" class="body-option accept-button">
                        annehmen
                    </a>
                </li>
                <li >
                    Martin Daute, Christian Elze
                    <a href="#" class="body-option deny-button">
                        ablehnen
                    </a>
                    <a href="#" class="body-option accept-button">
                        annehmen
                    </a>
                </li>
            </ol>
        </div>
    </div> <!-- end: exercise-sheet-body-wrapper -->
</div> <!-- end: exercise-sheet-wrapper -->';

$w = new HTMLWrapper($h, $manageGroup, $createGroup, $invitations);
$w->show();
?>

