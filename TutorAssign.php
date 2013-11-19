<?php
include 'include/Header/Header.php';
include 'include/HTMLWrapper.php';

// construct a new Header
$h = new Header("Datenstrukturen",
                "",
                "Florian Lücke",
                "Kontrolleur", 
                "75%");

$h->setBackURL("index.php")
->setBackTitle("zur Veranstaltung");

// construct a content element for managing groups
$assignStudents = '<div class="content">
    <div class="content-header">
        <div class="content-title uppercase">Kontrolleure zuweisen</div>
        <div class="exercise-sheet-end">
            <a href="#" class="body-option">zu ausgewähltem Kontrolleur hinzufügen</a>
        </div>   
    </div>

    <div class="content-body">
        <div class="exercise-sheet-body-left exercise-sheet-body">
            <div class="tutor-assign-element">
                <div class="tutor-assign-element-title">
                    <input type="radio" name="tutor-assign-radio" value="Unzugeordnet" checked="true" /> Unzugeordnet
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" />102<br />
                    <input type="checkbox" />103<br />
                    <input type="checkbox" />104<br />
                    <input type="checkbox" />105<br />
                    <input type="checkbox" />106<br />
                    <input type="checkbox" />107<br />
                    <input type="checkbox" />108<br />
                    <input type="checkbox" />109<br />
                    <input type="checkbox" />110<br />
                </div>
            </div>
            <div class="tutor-assign-element">
                <div class="tutor-assign-element-title">
                    <input type="radio" name="tutor-assign-radio" value="Admin" /> Admin
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" />102<br />
                    <input type="checkbox" />103<br />
                    <input type="checkbox" />104<br />
                    <input type="checkbox" />105<br />
                    <input type="checkbox" />106<br />
                    <input type="checkbox" />107<br />
                    <input type="checkbox" />108<br />
                    <input type="checkbox" />109<br />
                    <input type="checkbox" />110<br />
                </div>
            </div>
            <div class="tutor-assign-element">
                <div class="tutor-assign-element-title">
                    <input type="radio" name="tutor-assign-radio" value="Felix" /> Felix
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" />102<br />
                    <input type="checkbox" />103<br />
                    <input type="checkbox" />104<br />
                    <input type="checkbox" />105<br />
                    <input type="checkbox" />106<br />
                    <input type="checkbox" />107<br />
                    <input type="checkbox" />108<br />
                    <input type="checkbox" />109<br />
                    <input type="checkbox" />110<br />
                </div>
            </div>
            <div class="tutor-assign-element">
                <div class="tutor-assign-element-title">
                    <input type="radio" name="tutor-assign-radio" value="Florian" /> Florian
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" />102<br />
                    <input type="checkbox" />103<br />
                    <input type="checkbox" />104<br />
                    <input type="checkbox" />105<br />
                    <input type="checkbox" />106<br />
                    <input type="checkbox" />107<br />
                    <input type="checkbox" />108<br />
                    <input type="checkbox" />109<br />
                    <input type="checkbox" />110<br />
                </div>
            </div>
        </div>
    </div> <!-- end: content-body -->
</div> <!-- end: content-wrapper -->';

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignStudents);
$w->show();
?>

