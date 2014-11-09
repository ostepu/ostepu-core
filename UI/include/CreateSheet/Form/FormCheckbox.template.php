<?php
/**
 * @file FormCheckbox.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<input type="hidden" class="input-choice" name="exercises[0][subexercises][0][type]" value="2">
<?php if (isset($form['formId'])){ ?>
<input type="hidden" name="exercises[0][subexercises][0][formId]" value="<?php echo $form['formId']; ?>" />
<?php } ?>
<label class="short label bold" for="task">Aufgabenstellung:</label>
<textarea name="exercises[0][subexercises][0][task]"
                              class="form-field task-field ckeditor"
                              rows="5"
                              style="width:100%"
                              maxlength="2500">
<?php echo (isset($form['task']) ? $form['task'] : ''); ?>
</textarea>
 
<?php 
if (isset($form['choices'])){ 
    foreach ($form['choices'] as $choice){
        $checkbox = Template::WithTemplateFile('include/CreateSheet/Form/FormAddCheckbox.template.php');
        if (isset($cid))
            $checkbox->bind(array('cid'=>$cid));
        if (isset($uid))
            $checkbox->bind(array('uid'=>$uid));
        if (isset($sid))
            $checkbox->bind(array('sid'=>$sid));
        $checkbox->bind(array('choice'=>$choice));
        $checkbox->show();
    }
}
?>
 
<a href="javascript:void(0);" class="body-option-color add-choice left">Auswahl hinzufügen</a>

<br><br>
<label class="short label bold" for="solution">Lösungsbegründung:</label>
<textarea name="exercises[0][subexercises][0][solution]"
          class="form-field ckeditor"
          rows="5"
          style="width:100%"
          maxlength="2500">
<?php echo (isset($form['solution']) ? $form['solution'] : ''); ?>
</textarea>
