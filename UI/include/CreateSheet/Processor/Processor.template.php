<?php
/**
 * @file Processor.template.php
 * @author  Till Uhlig
 */

include_once '../../../../Assistants/Structures.php';
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
    
    if ( $result['status'] >= 200 && 
         $result['status'] <= 299 ){
         
        $processors = Process::decodeProcess($result['content']);
        if (!is_array($processors)) $processors = array($processors);
        $components = array();
        foreach ($processors as $processor){
            $components[] = $processor->getTarget();
        }
?>

<tr><td>
        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                        
        <div class="content-header">
            <div class="content-title left uppercase">Verarbeitung</div>
        <div class="critical-color right">
                <a href="javascript:void(0);" class="delete-processor">Verarbeitung löschen</a>
            </div>
        </div>
        

        <div class="content-body-wrapper">
        
            <div class="content-body left"> </div>
            <label class="short left label bold" for="exerciseType">Modul:</label>
               <select class="form-field text-input processor-type" style="width:auto" name="exercises[0][subexercises][0][processorId][0]" value="Modul">
        <?php                   
                foreach ($components as $link){
                    if ($link->getId() === null || $link->getName() === null) continue;
        ?>
           <option value="<?php echo $link->getId(); ?>"><?php echo $link->getName(); ?></option>
        <?php
               }
        ?>
          
    </select>
    <br><br>
    <label class="short left label bold new-line" for="attachment">Anhang:</label><br><br>
   <a href="javascript:void(0);" class="body-option-color add-attachment left">Anhang hinzufügen</a>
              <div class="form-field processor-parameter-area" style="width:100%"></div>
            </div></div>
</td></tr>

<?php
} else {
?>
<tr><td>
        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                        
        <div class="content-header">
            <div class="content-title left uppercase">Verarbeitung</div>
        <div class="critical-color right">
                <a href="javascript:void(0);" class="delete-processor">Verarbeitung löschen</a>
            </div>
        </div>
                <div class="content-body-wrapper">
        
            <div class="content-body left"> </div>
            
        keine Module
      
        </div></div>
          </td></tr>
<?php
}
?>