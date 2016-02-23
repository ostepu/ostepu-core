<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../../../Assistants/Structures.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file LOOPparamcount.template.php
 * @author  Ralf Busch
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<span class="fileArea">
    <input type="checkbox" class="parameter-choice-test" style="float:right;" name="exercises[0][subexercises][0][checkboxParameter][0][0][]" value="1" />
    <input class="parameter-choice-test fileButton" style="width:104px; display:none;" type="file" name="exercises[0][subexercises][0][fileParameter][0][0][]" value="<?php echo Language::Get('main','selectFile', $langTemplate); ?> ..." disabled="disabled"/> 
    <div style="margin-right:20px;">
        <select class="parameter-choice-test" style="min-width:160px; width:100%;" name="exercises[0][subexercises][0][inputParameter][0][0][]">
            <option value=""<?php echo (isset($input[1]) && $input[1] == '' ? ' selected="selected"':'');?>><?php echo Language::Get('main','noData', $langTemplate); ?></option>
            <?php
            if(isset($files) && is_array($files) && !empty($files)) 
            foreach ($files as $key => $value) {?>
                <option value="<?php echo (isset($value) && !empty($value) ? 'ID_'.$value->getFileId() : '') ;?>"<?php echo (isset($input[1]) && !empty($input[1]) && $input[1]->fileId == $value->getFileId() ? ' selected="selected"':'');?>><?php echo $value->getDisplayName(); ?></option>
            <?php } ?>
            <option value="Add"><?php echo Language::Get('main','addData', $langTemplate); ?></option>
        </select>
    </div>
</span>