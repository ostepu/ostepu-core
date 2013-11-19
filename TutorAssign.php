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
            <a href="#" class="body-option">zu ausgewählter Gruppe hinzufügen</a>
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
                    <input type="checkbox" />111<br />
                    <input type="checkbox" />113<br />
                    <input type="checkbox" />115<br />
                    <input type="checkbox" />117<br />
                </div>
            </div>
            <div class="tutor-assign-element">
                <div class="tutor-assign-element-title">
                    <input type="radio" name="tutor-assign-radio" value="Felix" /> Felix
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" />112<br />
                    <input type="checkbox" />114<br />
                    <input type="checkbox" />116<br />
                    <input type="checkbox" />118<br />
                    <input type="checkbox" />119<br />
                    <input type="checkbox" />120<br />
                    <input type="checkbox" />121<br />
                </div>
            </div>
            <div class="tutor-assign-element">
                <div class="tutor-assign-element-title">
                    <input type="radio" name="tutor-assign-radio" value="Florian" /> Florian
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" />122<br />
                    <input type="checkbox" />123<br />
                    <input type="checkbox" />124<br />
                    <input type="checkbox" />125<br />
                    <input type="checkbox" />126<br />
                    <input type="checkbox" />127<br />
                    <input type="checkbox" />128<br />
                    <input type="checkbox" />129<br />
                    <input type="checkbox" />130<br />
                </div>
            </div>            
        </div>
    </div> <!-- end: content-body -->

    <div class="content-footer">
        <ol>
            <li class="footer-text">TODO: Automatisch zuteilen, Zuteilung aufheben</li>
        </ol>      
    </div> 
</div> <!-- end: content-wrapper -->';

// wrap all the elements in some HTML and show them on the page
$w = new HTMLWrapper($h, $assignStudents);
$w->show();
?>

