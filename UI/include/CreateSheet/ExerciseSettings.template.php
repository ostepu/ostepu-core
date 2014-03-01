<div class="content-element collapsible">
    <div class="content-header">
        <div class="content-title left uppercase">Aufgabe 1</div>
        <div class="critical-color bold right">
            <a href="javascript:void(0);" class="delete-exercise">Aufgabe löschen</a>
        </div>
    </div>

    <div class="content-body-wrapper">
        <div class="content-body left">
            <ol class="full-width-list lower-alpha">
                <li>
                    <input class="form-field text-input very-short" name="exercises[0][subexercises][0][maxPoints]" value="1" placeholder="Punkte" id="exerciseMaxPoints" />
                    <select class="form-field text-input short" name="exercises[0][subexercises][0][exerciseType]" id="exerciseType">
                        <?php
                        session_start();
                        if (isset($_SESSION['JSCACHE'])) {
                            $cache = json_decode($_SESSION['JSCACHE'], true);
                            foreach ($cache as $excercisetype) {
                                print "<option value=\"".$excercisetype['exerciseTypeId']."\">".$excercisetype['name']."</option>";
                                print "<option value=\"".$excercisetype['exerciseTypeId']."b\">".$excercisetype['name']." (Bonus)</option>";
                            }
                        }
                        ?>
                    </select>
                    <input class="form-field text-input short" name="exercises[0][subexercises][0][mime-type]" value="pdf" id="exerciseType" placeholder="pdf, zip, html, jpg, gif"/>
                    <input class="button" type="file" name="exercises[0][subexercises][0][attachment]" value="Anhang auswählen ..." />
                    <a href="javascript:void(0);" class="body-option-color deny-button right delete-subtask" style="display:none;">Teilaufgabe löschen</a>

                </li>
                <li class="skip-item">
                    <a href="javascript:void(0);" class="body-option-color right deny-button skip-list-item">Teilaufgabe hinzufügen</a>
                </li>
            </ol>
        </div>
    </div> <!-- end: content-body -->
</div> <!-- end: content-wrapper -->