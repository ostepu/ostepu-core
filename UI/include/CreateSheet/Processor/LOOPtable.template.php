<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file LOOPparamcount.template.php
 * @author  Ralf Busch
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<table border="0" style="width:100%;" class="testcase-table">
    <tr>
        <td style="width: 70%; padding: 3px 10px 3px 0px; background-color: #B9B8B8;" colspan="<?php echo(isset($paramcount) ? $paramcount : 1);?>" class="input-parameters">
            <label class="label bold">&nbsp;<?php echo Language::Get('main','parameters', $langTemplate); ?>: </label>
        </td>
        <td style="width: 30%; padding: 3px 0px 3px 0px; border-left-style: solid; border-left-width: 1px; border-left-color: #999; background-color: #B9B8B8;">
            <label class="label bold">&nbsp;<?php echo Language::Get('main','output', $langTemplate); ?>: </label>
        </td>
    </tr>
    <tr>
        <?php if (!isset($testcases)) { ?>
        <td style="padding-right: 10px; min-width:150px; padding:4px;" class="input-parameter-choice">
            <select class="parameter-choice-test" style="min-width:150px; width:100%;" name="exercises[0][subexercises][0][inputDatatype][0][]">
                <option value="Text">Text</option>
                <option value="Datei">Datei</option>
            </select>
        </td>
        <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;" class="output-parameter-choice">
            <select class="parameter-choice-test" style="min-width:150px; width:100%;" name="exercises[0][subexercises][0][outputDatatype][0]">
                <option value="Text">Text</option>
                <option value="Regex">regulärer Ausdruck</option>
                <option value="Datei">Datei</option>
            </select>                                
        </td>
        <?php } else if (isset($testcases[0]) && is_object($testcases[0])) {
        $inputs = $testcases[0]->getInput();
        if (isset($inputs) && is_array($inputs)) {
            foreach($inputs as $key => $input){
        ?>
        <td style="padding-right: 10px; min-width:150px; padding:4px;" class="input-parameter-choice">
            <select class="parameter-choice-test" style="min-width:150px; width:100%;" name="exercises[0][subexercises][0][inputDatatype][0][]">
                <option value="Text" <?php echo (isset($input[0]) && $input[0] == 'Text' ? ' selected="selected"':'');?>>Text</option>
                <option value="Datei" <?php echo (isset($input[0]) && $input[0] == 'Datei' ? ' selected="selected"':'');?>>Datei</option>
            </select>
        </td>
        <?php }}
            $output = $testcases[0]->getOutput();
            if (isset($output) && is_array($output)) {?> 
        <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;" class="output-parameter-choice">
            <select class="parameter-choice-test" style="min-width:150px; width:100%;" name="exercises[0][subexercises][0][outputDatatype][0]">
                <option value="Text" <?php echo (isset($output[0]) && $output[0] == 'Text' ? ' selected="selected"':'');?>>Text</option>
                <option value="Regex" <?php echo (isset($output[0]) && $output[0] == 'Regex' ? ' selected="selected"':'');?>>regulärer Ausdruck</option>
                <option value="Datei" <?php echo (isset($output[0]) && $output[0] == 'Datei' ? ' selected="selected"':'');?>>Datei</option>
            </select>                                
        </td>
        <?php }
        } ?> 
    </tr>
    
    <?php if (!isset($testcases)) { ?>
    <tr>
        <td style="padding-right: 10px; min-width:150px; padding:4px;  border-width: 1px 0px 1px 0px; border-style: solid none solid none;" class="input-parameter">
            <input type="text" class="parameter-choice-test" style="min-width:150px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][inputParameter][0][0][]" value=""/>
        </td>
        <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;  border-width: 1px 0px 1px 1px; border-style: solid none solid solid;" class="output-parameter">
            <input type="text" class="parameter-choice-test" style="min-width:150px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][ouputParameter][0][0]" value=""/>                              
        </td>
    </tr>
    <?php } else if (is_array($testcases)) {
        foreach($testcases as $key => $testcase){?>
        <tr>
        <?php
            $inputs = $testcase->getInput();
            $output = $testcase->getOutput();
            if (is_array($inputs)){
                foreach ($inputs as $key2 => $input) {?>
                <td style="padding-right: 10px; min-width:150px; padding:4px;  border-width: 1px 0px 1px 0px; border-style: solid none solid none;" class="input-parameter">
                    <input type="text" class="parameter-choice-test" style="min-width:150px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][inputParameter][0][0][]" value="<?php echo(isset($input[1]) ? $input[1] : '' ); ?>"/>
                </td>
                <?php }
            } ?>
            <td style="width: 30%;border-left-style: solid; border-left-width: 1px; border-left-color: #999; padding:4px;  border-width: 1px 0px 1px 1px; border-style: solid none solid solid;" class="output-parameter">
                <input type="text" class="parameter-choice-test" style="min-width:150px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][ouputParameter][0][0]" value="<?php echo(isset($output[1]) ? $output[1] : '' ); ?>"/>                              
            </td>
        </tr>
        <?php }
    } ?>    
</table>
<a href="javascript:void(0);" class="body-option-color very-short delete-test"><?php echo Language::Get('main','deletetest', $langTemplate); ?></a>