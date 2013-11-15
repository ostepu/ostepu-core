<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "211221492", 
                "75%");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

// construct a content element for managing groups
$manageGroup = '<div class="content">
    <div class="content-header">
        <div class="content-title">Gruppe verwalten</div>
        <div class="exercise-sheet-end">
            <a href="#" class="body-option">verlassen</a>
        </div>   
    </div>

    <div class="content-body-wrapper">
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
    </div> <!-- end: content-body-wrapper -->
</div> <!-- end: content-wrapper -->';

// construct a content element for creating groups
$createGroup = '<div class="content">
    <div class="content-header">
        <div class="content-title">Erstellen</div>
        <div class="exercise-sheet-end">
            <a href="#" class="body-option">einladen</a>
        </div>     
    </div>

    <div class="content-body-wrapper">
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
    </div> <!-- end: content-body-wrapper -->
</div> <!-- end: content-wrapper -->';

// construct a content element for joining groups
$invitations = '<div class="content">
    <div class="content-header">
        <div class="content-title">Einladungen</div>     
    </div>

    <div class="content-body-wrapper">
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
    </div> <!-- end: content-body-wrapper -->
</div> <!-- end: content-wrapper -->';

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $manageGroup, $createGroup, $invitations);
$w->show();
?>

