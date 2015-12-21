<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>

<?php $langTemplate='Form';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file FormRadio.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<span class="right">
<?php echo MakeInfoButton('extension/LForm','formRadio.md'); ?>
</span>

<input type="hidden" class="input-choice" name="exercises[0][subexercises][0][type]" value="1">
<?php if (isset($form['formId'])){ ?>
<input type="hidden" name="exercises[0][subexercises][0][formId]" value="<?php echo $form['formId']; ?>" />
<?php } ?>
<label class="short label bold" for="task"><?php echo Language::Get('main','task', $langTemplate); ?>:</label>
<!--ckeditor--><textarea name="exercises[0][subexercises][0][task]"
                              class="reset form-field task-field"
                              rows="5"
                              style="width:100%;"
                              maxlength="2500">
<?php echo (isset($form['task']) ? $form['task'] : ''); ?>
</textarea>

<?php 
if (isset($form['choices'])){ 
    foreach ($form['choices'] as $choice){
        $radio = Template::WithTemplateFile('include/CreateSheet/Form/FormAddRadio.template.php');
        if (isset($cid))
            $radio->bind(array('cid'=>$cid));
        if (isset($uid))
            $radio->bind(array('uid'=>$uid));
        if (isset($sid))
            $radio->bind(array('sid'=>$sid));
        $radio->bind(array('choice'=>$choice));
        $radio->show();
    }
}
?>
      
<a href="javascript:void(0);" class="body-option-color add-choice left"><?php echo Language::Get('main','addChoice', $langTemplate); ?></a>
            
<br><br><label class="short label bold" for="solution"><?php echo Language::Get('main','solution', $langTemplate); ?>:</label>
<!--ckeditor--><textarea name="exercises[0][subexercises][0][solution]"
                              class="form-field"
                              rows="5"
                              style="width:100%"
                              maxlength="2500">
<?php echo (isset($form['solution']) ? $form['solution'] : ''); ?>
</textarea>