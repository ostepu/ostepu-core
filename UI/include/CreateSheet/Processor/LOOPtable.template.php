<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file LOOPparamcount.template.php
 * @author  Ralf Busch
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="hiddenFiles" style="height: 0px;"><?php 
if (isset($files) && is_array($files) && !empty($files)){
foreach($files as $key => $file){ 
    $file = File::decodeFile(File::encodeFile($file));
    echo '<input class="hiddenFile" type="hidden" name="exercises[0][subexercises][0][fileParameter][0][ID_'.$file->getFileId().']" value=""/>';
 }} ?>
</div>
<table border="0" style="width:100%;" class="testcase-table">
    <tr>
        <td style="min-width:20px; max-width:20px;"></td>
        <td style="width: 70%; padding: 3px 10px 3px 0px; background-color: #B9B8B8;" colspan="<?php echo(isset($paramcount) ? $paramcount : 1);?>" class="input-parameters">
            <label class="label bold">&nbsp;<?php echo Language::Get('main','parameters', $langTemplate); ?>: </label>
        </td>
        <td style="width: 30%; padding: 3px 0px 3px 0px; border-left-style: solid; border-left-width: 1px; border-left-color: #999; background-color: #B9B8B8;">
            <label class="label bold">&nbsp;<?php echo Language::Get('main','output', $langTemplate); ?>: </label>
        </td>
    </tr>
    <tr>
        <?php if (!isset($testcases)) { ?>
        <td></td>
        <td style="padding-right: 10px; min-width:160px; padding:4px;" class="input-parameter-choice">
            <a href="javascript:void(0);" name="deleteCol" class="plain deleteCol" style="width:17px; height:17px; float:right;">                                      
                <img src="Images/Delete.png" style="width:17px; height:17px;">
                <?php if (isset($sheetFile)){ ?><span class="right warning-simple"></span><?php } ?>
            </a>
            <div style="min-width:140px; margin-right:20px;">
                <select class="parameter-choice-test" style="min-width:140px; width:100%;" name="exercises[0][subexercises][0][inputDatatype][0][]">
                    <option value="Text"><?php echo Language::Get('main','text', $langTemplate); ?></option>
                    <option value="Data"><?php echo Language::Get('main','data', $langTemplate); ?></option>
                </select>
            </div>
        </td>
        <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;" class="output-parameter-choice">
            <select class="parameter-choice-test" style="min-width:160px; width:100%;" name="exercises[0][subexercises][0][outputDatatype][0]">
                <option value="Text"><?php echo Language::Get('main','text', $langTemplate); ?></option>
                <option value="Regex"><?php echo Language::Get('main','regex', $langTemplate); ?></option>
                <option value="Data"><?php echo Language::Get('main','data', $langTemplate); ?></option>
            </select>                                
        </td>
        <?php } else if (isset($testcases[0]) && is_object($testcases[0])) {
        $inputs = $testcases[0]->getInput();
        if (isset($inputs) && is_array($inputs)) {?>
        <td></td>
        <?php foreach($inputs as $key => $input){
        ?>
        
        <td style="padding-right: 10px; min-width:160px; padding:4px;" class="input-parameter-choice">
            <a href="javascript:void(0);" name="deleteCol" class="plain deleteCol" style="width:17px; height:17px; float:right;">                                      
                <img src="Images/Delete.png" style="width:17px; height:17px;">
                <?php if (isset($sheetFile)){ ?><span class="right warning-simple"></span><?php } ?>
            </a>
            <div style="min-width:140px; margin-right:20px;">
                <select class="parameter-choice-test" style="min-width:160px; width:100%;" name="exercises[0][subexercises][0][inputDatatype][0][]">
                    <option value="Text" <?php echo (isset($input[0]) && $input[0] == 'Text' ? ' selected="selected"':'');?>><?php echo Language::Get('main','text', $langTemplate); ?></option>
                    <option value="Data" <?php echo (isset($input[0]) && $input[0] == 'Data' ? ' selected="selected"':'');?>><?php echo Language::Get('main','data', $langTemplate); ?></option>
                </select>
            </div>
        </td>
        <?php }}
            $output = $testcases[0]->getOutput();
            if (isset($output) && is_array($output)) {?> 
        <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;" class="output-parameter-choice">
            <select class="parameter-choice-test" style="min-width:160px; width:100%;" name="exercises[0][subexercises][0][outputDatatype][0]">
                <option value="Text" <?php echo (isset($output[0]) && $output[0] == 'Text' ? ' selected="selected"':'');?>><?php echo Language::Get('main','text', $langTemplate); ?></option>
                <option value="Regex" <?php echo (isset($output[0]) && $output[0] == 'Regex' ? ' selected="selected"':'');?>><?php echo Language::Get('main','regex', $langTemplate); ?></option>
                <option value="Data" <?php echo (isset($output[0]) && $output[0] == 'Data' ? ' selected="selected"':'');?>><?php echo Language::Get('main','data', $langTemplate); ?></option>
            </select>                                
        </td>
        <?php }
        } ?> 
    </tr>
    
    <?php if (!isset($testcases)) { ?>
    <tr>
        <td style="vertical-align: middle; padding: 0;"><a href="javascript:void(0);" name="deleteRow" class="plain deleteRow" style="width:17px; height:17px; float:right;">                                      
                <img src="Images/Delete.png" style="width:17px; height:17px;">
            </a>
        </td>
        <td style="padding-right: 10px; min-width:160px; padding:4px;  border-width: 1px 0px 1px 0px; border-style: solid none solid none;" class="input-parameter">
            <input type="text" class="parameter-choice-test" style="min-width:160px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][inputParameter][0][0][]" value=""/>
        </td>
        <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;  border-width: 1px 0px 1px 1px; border-style: solid none solid solid;" class="output-parameter">
            <input type="text" class="parameter-choice-test" style="min-width:160px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][outputParameter][0][0]" value=""/>                              
        </td>
    </tr>
    <?php } else if (is_array($testcases)) {
        foreach($testcases as $key => $testcase){?>
        <tr>
            <td style="vertical-align: middle; padding: 0;"><a href="javascript:void(0);" name="deleteRow" class="plain deleteRow" style="width:17px; height:17px; float:right;">                                      
                <img src="Images/Delete.png" style="width:17px; height:17px;">
                </a>
            </td>
        <?php
            $inputs = $testcase->getInput();
            $output = $testcase->getOutput();
            if (is_array($inputs)){
                foreach ($inputs as $key2 => $input) {?>
                <td style="padding-right: 10px; min-width:160px; padding:4px;  border-width: 1px 0px 1px 0px; border-style: solid none solid none;" class="input-parameter">
                    <?php
                        $path = dirname(__FILE__) . '/LOOPinput.template.php';

                        if (isset($input[0]) && $input[0] == "Data")
                        {
                            $path = dirname(__FILE__) . '/LOOPfileinput.template.php';
                        }

                        $pro = Template::WithTemplateFile($path);
                        if(isset($files) && !empty($files))
                        {
                            $pro->bind(array('files'=>$files));
                        }
                        
                        $pro->bind(array('input'=> $input));
                        $pro->show();
                    ?>
                </td>
                <?php }
            } ?>
            <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;  border-width: 1px 0px 1px 1px; border-style: solid none solid solid;" class="output-parameter">
                <?php
                    $path = dirname(__FILE__) . '/LOOPoutput.template.php';

                    if (isset($output[0]) && $output[0] == "Data")
                    {
                        $path = dirname(__FILE__) . '/LOOPfileoutput.template.php';
                    }

                    $pro2 = Template::WithTemplateFile($path);
                    if(isset($files) && !empty($files))
                    {
                        $pro2->bind(array('files'=>$files));
                    }
                    
                    $pro2->bind(array('output'=> $output));
                    $pro2->show();
                ?>
                                           
            </td>
        </tr>
        <?php }
    } ?>    
</table>
<a href="javascript:void(0);" class="critical-color very-short delete-test"><?php echo Language::Get('main','deletetest', $langTemplate); ?></a>