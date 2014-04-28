<li>
    <input class="form-field text-input very-short" name="exercises[0][subexercises][0][maxPoints]" placeholder="Punkte" id="exerciseMaxPoints" />
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
    <input class="form-field text-input very-short mime-field" name="exercises[0][subexercises][0][mime-type]" value="pdf" id="mime-type" placeholder="pdf, zip, html, jpg, gif"/>
    <input class="button" type="file" name="exercises[0][subexercises][0][attachment]" value="Anhang auswählen ..." />
                        <a href="javascript:void(0);" class="body-option-color deny-button delete-subtask right">Teilaufgabe löschen</a>
        <div class="content-body-wrapper">
                    <table border="0" style="width:100%">
                    <tr><td><a href="javascript:void(0);" class="body-option-color very-short use-form">Eingabemaske verwenden</a></td></tr> 
                    
                    <tr><td><a href="javascript:void(0);" class="body-option-color very-short use-processor">Verarbeitung hinzufügen</a></td></tr>                        
                    </table>
                        </div>
    
</li>