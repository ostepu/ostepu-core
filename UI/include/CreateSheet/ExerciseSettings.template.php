<?php 
    header('Content-Type: text/html; charset=utf-8');
    if (!empty($exercises)):

    $choiceId = 0;
    //var_dump($exercises);
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
                        <input class="form-field text-input very-short" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][maxPoints]" value="<?php echo $subexercise['maxPoints']; ?>" placeholder="Punkte" id="exerciseMaxPoints" />
                        <select class="form-field text-input short" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][exerciseType]" id="exerciseType">
                            <?php
                            ///session_start();
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
                        <input class="form-field text-input very-short mime-field" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][mime-type]" value="<?php echo (isset($subexercise['mime-type']) ? $subexercise['mime-type'] : ''); ?>" id="mime-type" placeholder="pdf, zip, html, jpg, gif" disabled="<?php echo (isset($subexercise['type']) ? 'disabled' : ''); ?>"/>
                        
                        <input class="button" type="file" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][attachment]" value="Anhang auswählen ..." />
                        <a href="javascript:void(0);" class="body-option-color deny-button right delete-subtask"<?php if(count($exercise['subexercises'])==1):?> style="display:none;" <?php endif;?>>Teilaufgabe löschen</a>
        <div class="content-body-wrapper">
                    <table border="0" style="width:100%">
                    <tr><td>
                                        <a href="javascript:void(0);" class="body-option-color very-short use-form">Eingabemaske verwenden</a>
                                        
                                        
                                        
                                        
                                        
                    <?php if (isset($subexercise['type'])){ ?>
                    <div class="content-element form" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                                    
                    <div class="content-header">
                        <div class="content-title left uppercase"><?php $arr = array("Eingabezeile","Einfachauswahl","Mehrfachauswahl");echo $arr[$subexercise['type']]; ?></div>
                        <div class="critical-color right">
                            <a href="javascript:void(0);" class="delete-form">Eingabemaske löschen</a>
                        </div>
                    </div>
                    
                    <div class="content-body-wrapper">
                    
                        <div class="content-body left">
                    </div>
                        <input type="hidden" class="input-choice" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][type]" value="<?php echo $subexercise['type']; ?>">
                        
                        <label class="short label bold" for="task">Aufgabenstellung:</label>
                        <textarea id="task" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][task]"
                                                      class="form-field task-field"
                                                      rows="5"
                                                      style="width:100%"
                                                      maxlength="255"><?php echo (isset($subexercise['task']) ? $subexercise['task'] : ''); ?></textarea>
                                                      
                    <?php if ($subexercise['type'] == 0) { ?>
                   
                        <div class="form-input-input" style="margin:5px 0px;">
                        <input type="hidden" class="choice-input" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][correct][<?php echo $choiceId; ?>]" value="1"><input class="form-field input-choice-text" style="width:100%" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][choice][<?php echo $choiceId; ?>]" value="<?php echo (isset($subexercise['choice'][0]) ? $subexercise['choice'][0] : ''); ?>" placeholder="Musterlösung"/>    
                        </div>
                        
                    <?php $choiceId++; } elseif ($subexercise['type'] == 1) { ?>
                    
                    <?php foreach ($subexercise['choice'] as $key3 => $choice): ?>
                        <div class="form-input-radio" style="margin:5px 0px;">
                        <input type="radio" class="choice-input" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][correct][<?php echo $choiceId; ?>]" value="" <?php echo (isset($subexercise['correct'][$key3]) ? 'checked' : '') ?>/><input class="form-field input-choice-text" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][choice][<?php echo $choiceId; ?>]" value="<?php echo $choice;?>" placeholder="Auswahltext"/>    
                        <a href="javascript:void(0);" class="body-option-color deny-button delete-choice center">Auswahlmöglichkeit löschen</a>
                        </div>
                    <?php $choiceId++;endforeach;?>
                    
                    <a href="javascript:void(0);" class="body-option-color add-choice left">Auswahl hinzufügen</a><br><br>

                    <?php } elseif ($subexercise['type'] == 2) { ?>
                    
                        <?php foreach ($subexercise['choice'] as $key3 => $choice): ?>
                        <div class="form-input-checkbox" style="margin:5px 0px;">
                        <input type="checkbox" class="choice-input" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][correct][<?php echo $choiceId; ?>]" value="" <?php echo (isset($subexercise['correct'][$key3]) ? 'checked' : ''); ?>/><input class="form-field input-choice-text" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][choice][<?php echo $choiceId; ?>]" value="<?php echo $choice;?>" placeholder="Auswahltext"/>    
                        <a href="javascript:void(0);" class="body-option-color deny-button delete-choice center">Auswahlmöglichkeit löschen</a>
                        </div>
                    <?php $choiceId++;endforeach;?>

                    <a href="javascript:void(0);" class="body-option-color add-choice left">Auswahl hinzufügen</a><br><br>
                    
                    <?php }?>
                        <label class="short label bold" for="solution">Lösungsbegründung:</label>
                        <textarea name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][solution]"
                                                      class="form-field solution-field"
                                                      rows="5"
                                                      style="width:100%"
                                                      maxlength="255"><?php echo (isset($subexercise['solution']) ? $subexercise['solution'] : ''); ?></textarea>
                       </div> 

                    <?php }?>
                    </td></tr> 
                    
                    
                    
                    
                    <?php?>
                     <?php if (isset($subexercise['processorId'])){ ?>
                     <?php foreach ($subexercise['processorId'] as $key4 => $processor): ?>
                     <?php if ($processors!==null){ ?>
                        <tr><td>
                        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                                        
                        <div class="content-header">
                            <div class="content-title left uppercase">Verarbeitung</div>
                        <div class="critical-color right">
                                <a href="javascript:void(0);" class="delete-processor">Verarbeitung löschen</a>
                            </div>
                        </div>
                        

                        <div class="content-body-wrapper">
                        
                            <div class="content-body left"></div>
                            <label class="short left label bold" for="exerciseType">Modul:</label>
                               <select class="form-field text-input processor-type" style="width:auto" name="exercises[<?php echo $key1; ?>][subexercises][<?php echo $key2; ?>][processorId][<?php echo $choiceId; ?>]" value="Modul">
                        <?php                   
                                foreach ($processors as $link){
                                    if ($link->getId() === null || $link->getName() === null) continue;
                        ?>
                           <option value="<?php echo $link->getId(); ?>" <?php echo($processor==$link->getId() ? 'selected' : '') ?>><?php echo $link->getName(); ?></option>
                        <?php
                               }
                        ?>
                          
                    </select>
                    <div class="form-field processor-parameter-area" style="width:100%"></div>
                            </div>
                    </td></tr>
                    <?php } else { ?>
                        <tr><td>
                        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                                        
                        <div class="content-header">
                            <div class="content-title left uppercase">Verarbeitung</div>
                        <div class="critical-color right">
                                <a href="javascript:void(0);" class="delete-processor">Verarbeitung löschen</a>
                            </div>
                        </div>
                         <div class="content-body-wrapper">
                        
                            <div class="content-body left">
                        keine Module
                        </div></div></div>
                        </td></tr>
                    <?php } ?> 
                    <?php $choiceId++;endforeach;?>
                     <?php }?>
                     
                     
                     
                     
                     
                    <tr><td><a href="javascript:void(0);" class="body-option-color very-short use-processor">Verarbeitung hinzufügen</a></td></tr>                        
                    </table>
                        </div>
                        
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
                
                    </li>
                    <li class="skip-item">
                        <a href="javascript:void(0);" class="body-option-color right deny-button skip-list-item">Teilaufgabe hinzufügen</a>
                    </li>
                </ol>
            </div>
        </div> <!-- end: content-body -->
    </div> <!-- end: content-wrapper -->
<?php endif; ?>