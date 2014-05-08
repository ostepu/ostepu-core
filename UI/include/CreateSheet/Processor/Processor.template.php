<?php
include_once '../../../../Assistants/Structures.php';
include_once '../../../../Assistants/Request.php';
include_once '../../Config.php';
$result = Request::get($databaseURI.'/definition/LProcessor',array(),'');
    if ( $result['status'] >= 200 && 
         $result['status'] <= 299 ){
         
        $processors = Component::decodeComponent($result['content']);
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
                $links = $processors->getLinks();
                foreach ($links as $link){
                    if ($link->getName()!=='process') continue;
                    if ($link->getTarget() === null || $link->getTargetName() === null) continue;
        ?>
           <option value="<?php echo $link->getTarget(); ?>"><?php echo $link->getTargetName(); ?></option>
        <?php
               }
        ?>
          
    </select>
    <div class="form-field processor-parameter-area" style="width:100%"></div>

    <!--<br><br>
    <label class="short left label bold new-line" for="attachment">Anhang:</label><br><br>
    
    
            <a href="javascript:void(0);" class="body-option-color add-attachment left">Anhang hinzufügen</a>-->
          
            </div>
</td></tr>

<?php
} else {
?>
        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                        
        <div class="content-header">
            <div class="content-title left uppercase">Verarbeitung</div>
        <div class="critical-color right">
                <a href="javascript:void(0);" class="delete-processor">Verarbeitung löschen</a>
            </div>
        </div>
        <tr><td>
        keine Module
        </td></tr>
        </div>
<?php
}
?>