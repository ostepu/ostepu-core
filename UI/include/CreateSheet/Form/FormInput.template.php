<?php
/**
 * @file FormInput.template.php
 * @author  Till Uhlig
 */
 
/*include_once '../../../../Assistants/Structures.php';
include_once '../../../../Assistants/Request.php';
include_once '../../Config.php';

session_start();
$courseid = null;
if (isset($_SESSION['JSCACHE'])) {
    $cache = json_decode($_SESSION['JSCACHE'], true);

    foreach ($cache as $excercisetype) {
        $courseid = $excercisetype['courseId'];
        break;
    }
}

if ($courseid!==null){           
    $result = Request::get($serverURI.'/DB/DBProcess/processList/process/course/' . $courseid,array(),'');
} else 
    $result['status'] = 409;
  
$components = array();  
if ( $result['status'] >= 200 && 
     $result['status'] <= 299 ){
     
    $processors = Process::decodeProcess($result['content']);
    if (!is_array($processors)) $processors = array($processors);
    
    foreach ($processors as $processor){
        if ($processor->getTarget()!==null && $processor->getTarget()->getName()==='LFormPredecessor')
            $components[] = $processor->getTarget();
    }
}*/
        
 ?>
<input type="hidden" class="input-choice" name="exercises[0][subexercises][0][type]" value="0">
<label class="short label bold" for="task">Aufgabenstellung:</label>
<textarea id="task" name="exercises[0][subexercises][0][task]"
                              class="form-field task-field"
                              rows="5"
                              style="width:100%"
                              maxlength="2500"></textarea>
                              
<div class="form-input-input" style="margin:5px 0px;">
<input type="hidden" class="choice-input" name="exercises[0][subexercises][0][correct][0]" value="1"><input class="form-field input-choice-text" style="width:100%" name="exercises[0][subexercises][0][choice][0]" value="" placeholder="Musterlösung"/>    
</div>

<label class="short label bold" for="solution">Lösungsbegründung:</label>
<textarea name="exercises[0][subexercises][0][solution]"
                              class="form-field solution-field"
                              rows="5"
                              style="width:100%"
                              maxlength="2500"></textarea>
                              
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