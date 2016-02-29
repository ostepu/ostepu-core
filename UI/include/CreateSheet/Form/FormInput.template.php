<?php
/**
 * @file FormInput.template.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>

<?php $langTemplate='Form';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');  
 ?>
<span class="right">
<?php echo MakeInfoButton('extension/LForm','formInput.md'); ?>
</span>

<input type="hidden" class="input-choice" name="exercises[0][subexercises][0][type]" value="0">
<?php if (isset($form['formId'])){ ?>
<input type="hidden" name="exercises[0][subexercises][0][formId]" value="<?php echo $form['formId']; ?>" />
<?php } ?>
<label class="short label bold" for="task"><?php echo Language::Get('main','task', $langTemplate); ?>:</label>
<!--ckeditor--><textarea name="exercises[0][subexercises][0][task]"
                              class="form-field task-field"
                              rows="5"
                              style="width:100%;"
                              maxlength="2500">
<?php echo (isset($form['task']) ? $form['task'] : ''); ?>
</textarea>


<div class="form-input-input" style="margin:5px 0px;">
<?php if (isset($form['choices'][0]['choiceId'])){ ?>
<input type="hidden" class="choice-id" name="exercises[0][subexercises][0][choiceId][0]" value="<?php echo $form['choices'][0]['choiceId']; ?>" />
<?php } ?>
<input type="hidden" class="choice-input" name="exercises[0][subexercises][0][correct][0]" value="">
<input class="form-field input-choice-text" style="width:100%" name="exercises[0][subexercises][0][choice][0]" value="<?php echo (isset($form['choices'][0]['text']) ? $form['choices'][0]['text'] : ''); ?>" placeholder="Musterlösung"/>    
</div>

<label class="short label bold" for="solution"><?php echo Language::Get('main','solution', $langTemplate); ?>:</label>
<!--ckeditor--><textarea name="exercises[0][subexercises][0][solution]"
                              class="form-field solution-field"
                              rows="5"
                              style="width:100%"
                              maxlength="2500">
<?php echo (isset($form['solution']) ? $form['solution'] : ''); ?>
</textarea>
                              
<?php /*if (count($components)>0){?>                              

                        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                                        
                        <div class="content-header">
                            <div class="content-title left uppercase">Verarbeitung</div>
                        </div>                        

                        <div class="content-body-wrapper">
                        
                            <div class="content-body left"></div>
                            <label class="short left label bold" for="exerciseType">Modul:</label>
                               <select class="form-field text-input processor-type" style="width:auto" name="exercises[0][subexercises][0][processorId][0]" value="Modul">
                        
                        <?php                   
                                foreach ($components as $link){
                                    if ($link->getId() === null || $link->getName() === null) continue;
                        ?>
                           <option value="<?php echo $link->getId(); ?>" <?php echo($processor==$link->getId() ? 'selected' : ''); ?>><?php echo $link->getName(); ?></option>
                        <?php
                               }
                        ?>
                          
                    </select>
                    <div class="form-field processor-parameter-area" style="width:100%"></div>
                            </div>

<?php } */?>