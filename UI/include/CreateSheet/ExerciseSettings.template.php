<?php if (!empty($exercises)):
    foreach ($exercises as $key1 => $exercise):?>
    <div class="content-element collapsible">
        <div class="content-header">
            <div class="content-title left uppercase">Aufgabe <?php print $key1+1; ?></div>
            <div class="critical-color bold right">
                <a href="javascript:void(0);" class="delete-exercise">Aufgabe löschen</a>
            </div>
        </div>

        <div class="content-body-wrapper">
            <div class="content-body left">
                <ol class="full-width-list lower-alpha">
                <?php foreach ($exercise['subexercises'] as $key2 => $subexercise):?>
                    <li>
                        <input class="form-field text-input very-short" name="exercises[<?= $key1 ?>][subexercises][<?= $key2 ?>][maxPoints]" value="<?= $subexercise['maxPoints'] ?>" placeholder="Punkte" id="exerciseMaxPoints" />
                        <select class="form-field text-input short" name="exercises[<?= $key1 ?>][subexercises][<?= $key2 ?>][exerciseType]" id="exerciseType">
                            <?php
                            session_start();
                            if (isset($_SESSION['JSCACHE'])) {
                                $cache = json_decode($_SESSION['JSCACHE'], true);
                                foreach ($cache as $excercisetype) {
                                    print "<option value=\"".$excercisetype['exerciseTypeId']."\"";
                                    if ($subexercise['exerciseType'] == $excercisetype['exerciseTypeId']) {print " selected=\"selected\"";}
                                    print ">".$excercisetype['name']."</option>";
                                    print "<option value=\"".$excercisetype['exerciseTypeId']."b\"";
                                    if ($subexercise['exerciseType'] == $excercisetype['exerciseTypeId']."b") {print " selected=\"selected\"";}
                                    print ">".$excercisetype['name']." (Bonus)</option>";
                                }
                            }
                            ?>
                        </select>
                        <input class="form-field text-input short" name="exercises[<?= $key1 ?>][subexercises][<?= $key2 ?>][mime-type]" value="<?= $subexercise['mime-type'] ?>" id="mime-type" placeholder="pdf, zip, html, jpg, gif"/>
                        <input class="button" type="file" name="exercises[<?= $key1 ?>][subexercises][<?= $key2 ?>][attachment]" value="Anhang auswählen ..." />
                        <a href="javascript:void(0);" class="body-option-color deny-button right delete-subtask"<?php if(count($exercise['subexercises'])==1):?> style="display:none;" <?php endif;?>>Teilaufgabe löschen</a>

                    </li>
                <?php endforeach;?>
                    <li class="skip-item">
                        <a href="javascript:void(0);" class="body-option-color right deny-button skip-list-item">Teilaufgabe hinzufügen</a>
                    </li>
                </ol>
            </div>
        </div> <!-- end: content-body -->
    </div> <!-- end: content-wrapper -->
<?php
endforeach;
    else: ?>
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
                        <input class="form-field text-input short" name="exercises[0][subexercises][0][mime-type]" value="pdf" id="mime-type" placeholder="pdf, zip, html, jpg, gif"/>
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
<?php endif; ?>