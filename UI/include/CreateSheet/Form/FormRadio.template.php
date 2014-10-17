<?php
/**
 * @file FormRadio.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<input type="hidden" class="input-choice" name="exercises[0][subexercises][0][type]" value="1">
<label class="short label bold" for="task">Aufgabenstellung:</label>
<textarea name="exercises[0][subexercises][0][task]"
                              class="form-field task-field ckeditor"
                              rows="5"
                              style="width:100%"
                              maxlength="2500"></textarea>
                              
<a href="javascript:void(0);" class="body-option-color add-choice left">Auswahl hinzufügen</a>
            
<br><br><label class="short label bold" for="solution">Lösungsbegründung:</label>
<textarea name="exercises[0][subexercises][0][solution]"
                              class="form-field ckeditor"
                              rows="5"
                              style="width:100%"
                              maxlength="2500"></textarea>