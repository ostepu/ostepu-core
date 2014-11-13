<?php
/**
 * @file FormInput.template.php
 * @author  Till Uhlig
 */
 
 header('Content-Type: text/html; charset=utf-8');  
 ?>
<input type="hidden" class="input-choice" name="exercises[0][subexercises][0][type]" value="0">
<?php if (isset($form['formId'])){ ?>
<input type="hidden" name="exercises[0][subexercises][0][formId]" value="<?php echo $form['formId']; ?>" />
<?php } ?>
<label class="short label bold" for="task">Aufgabenstellung:</label>
<textarea name="exercises[0][subexercises][0][task]"
                              class="form-field task-field ckeditor"
                              rows="5"
                              style="width:100%;"
                              maxlength="2500">
<?php echo (isset($form['task']) ? $form['task'] : ''); ?>
</textarea>


<div class="form-input-input" style="margin:5px 0px;">
<input type="hidden" class="choice-input" name="exercises[0][subexercises][0][correct][0]" value="1">
<input class="form-field input-choice-text" style="width:100%" name="exercises[0][subexercises][0][choice][0]" value="<?php echo (isset($form['choices'][0]['text']) ? $form['choices'][0]['text'] : ''); ?>" placeholder="Musterlösung"/>    
</div>

<label class="short label bold" for="solution">Lösungsbegründung:</label>
<textarea name="exercises[0][subexercises][0][solution]"
                              class="form-field solution-field ckeditor"
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