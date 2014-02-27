<li>
    <input class="form-field text-input very-short" name="exercises[0][subexercises][0][maxPoints]" placeholder="Punkte" id="exerciseMaxPoints" />
    <select class="form-field text-input short" name="exercises[0][subexercises][0][exerciseType]" id="exerciseType">
        <?php
        session_start();
        if (isset($_SESSION['JSCACHE'])) {
            $cache = json_decode($_SESSION['JSCACHE']);

            foreach ($cache as $excercisetype) {
                print "<option value=\"".$excercisetype->exerciseTypeId."\">".$excercisetype->name."</option>";
            }
        }
        ?>
    </select>
    <input class="form-field text-input short" name="exercises[0][subexercises][0][mime-type]" value="pdf" id="exerciseType" placeholder="pdf, zip, html, jpg, gif"/>
    <input class="button" type="file" name="exercises[0][subexercises][0][attachment]" value="Anhang auswählen ..." />
    <a href="javascript:void(0);" class="body-option-color deny-button right delete-subtask">Teilaufgabe löschen</a>

</li>