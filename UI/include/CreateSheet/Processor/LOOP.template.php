<?php
/**
 * @file LOOP.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2015-2016
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

<?php 
    ob_start();
    include_once dirname(__FILE__) . '/../../Template.php';
    include_once dirname(__FILE__) . '/../../../../Assistants/Language.php';
    include_once dirname(__FILE__) . '/../../../../Assistants/Structures/File.php';
    include_once dirname(__FILE__) . '/../../../../Assistants/Structures/Submission.php';
    include_once dirname(__FILE__) . '/../../../../Assistants/Structures/Testcase.php';
    include_once dirname(__FILE__) . '/../../../../Assistants/Structures/Process.php';
    include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <?php echo MakeInfoButton('extension','LOOP.md'); ?>
    <div class="content-body-wrapper" style="padding: 10px; margin-top: 0px;">
        <div class="content-body left" style="width:100%;">
            <table border="1" style="width:100%;"> 
                <tr>
                    <?php
                    $liste = array(
                                    'java' => 'Java',
                                    'cx' => 'Cx',
                                    'custom' => Language::Get('main','custom', $langTemplate));
                    
                    $i=0;
                    $params = array();
                    $errorsEnabled = true;

                    if (isset($process['parameter'])){
                        //check if processparameter is json
                        $data = Testcase::decodeTestcase($process['parameter']);
                        if (is_object($data) || is_array($data))
                        {
                            if ($data[0]->getTestcaseType()=="compile")
                            {
                                $params = explode(' ',$data[0]->getInput()[0]);
                            }
                            if (!is_null($data[0]->getErrorsEnabled()))
                            {
                                if ($data[0]->getErrorsEnabled() == "1")
                                {
                                    $errorsEnabled = true;
                                }
                                else
                                {
                                    $errorsEnabled = false;
                                }
                            }
                        }
                    }
                    ?>
                    <td style="width: 24%;">
                        <label class="label bold" for=""><?php echo Language::Get('main','executable', $langTemplate); ?>:</label>
                    </td>
                    <td>
                        <select class="parameter-choice exe-type" style="width:auto" name="exercises[0][subexercises][0][processorParameterList][0][]">
                            <?php foreach($liste as $key => $value){ ?>
                            <option value="<?php echo $key; ?>"<?php echo (in_array($key, $params) ? ' selected=\"selected\"':'');?>><?php echo $value; ?></option>
                            <?php if (in_array($key, $params))unset($params[array_search($key, $params)]); ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 24%;">
                        <label class="label bold" for=""><?php echo Language::Get('main','showErrors', $langTemplate); ?>:</label>
                    </td>
                    <td style="width: 76%;">
                        <input type="checkbox" class="parameter-choice" style="width: 100%;" name="exercises[0][subexercises][0][showErrorsParameter][0][]" value="1" <?php echo($errorsEnabled == true ? 'checked="checked"' : ''); ?>/>
                    </td>
                </tr>
                <tr>
                    <td style="width: 24%;">
                        <label class="label bold" for=""><?php echo Language::Get('main','compileparameters', $langTemplate); ?>:</label>
                    </td>
                    <td style="width: 76%;">
                        <input type="text" class="parameter-choice" style="width: 100%;" name="exercises[0][subexercises][0][processorParameterList][0][]" value="<?php echo (count($params)>0 ? implode(' ',$params) : '$file'); ?>"/>
                        <input class="compileFile fileButton" <?php echo(isset($data[0]) && isset($data[0]->getInput()[1]) ? "style='width:104px;'" : "style='width:104px; display:none;'");?> type="file" name="exercises[0][subexercises][0][compileFileParameter][0]" value="<?php echo Language::Get('main','selectFile', $langTemplate); ?> ..." <?php echo(isset($data[0]) && isset($data[0]->getInput()[1]) ? "" : "disabled='disabled'"); ?>/>
                        <span class="compileFile" <?php echo(isset($data[0]) && isset($data[0]->getInput()[1]) ? "" : "style='display:none'");?>><?php echo (isset($data[0]) && isset($data[0]->getInput()[1]) && !empty($data[0]->getInput()[1]) ? $data[0]->getInput()[1]->displayName : Language::Get('main','noScript', $langTemplate)); ?></span>
                        <input type="hidden" name="exercises[0][subexercises][0][compileFileParameter][0]" value="<?php echo (isset($data[0]) && isset($data[0]->getInput()[1]) && !empty($data[0]->getInput()[1]) ? $data[0]->getInput()[1]->fileId : ''); ?>"/>
                    </td>
                    </td>
                </tr>
                <tr <?php echo(isset($data[0]) && isset($data[0]->getInput()[2]) ? "" : "style='display:none'");?>>
                    <td style="width: 24%;">
                        <label class="label bold" for=""><?php echo Language::Get('main','runparameters', $langTemplate); ?>:</label>
                    </td>
                    <td style="width: 76%;">
                        <input type="text" class="parameter-choice runParameter" style="width: 100%;" name="exercises[0][subexercises][0][runParameter][0]" value="<?php echo(isset($data[0]) && isset($data[0]->getInput()[2]) ? $data[0]->getInput()[2] : "\$script \$parameters");?>" <?php echo(isset($data[0]) && isset($data[0]->getInput()[2]) ? "" : "disabled='disabled'"); ?>/>
                        <input class="runFile fileButton" style="width:104px;" type="file" name="exercises[0][subexercises][0][runFileParameter][0]" value="<?php echo Language::Get('main','selectFile', $langTemplate); ?> ..." <?php echo(isset($data[0]) && isset($data[0]->getInput()[3]) ? "" : "disabled='disabled'"); ?>/>
                        <span class="runFile"><?php echo (isset($data[0]) && isset($data[0]->getInput()[3]) && !empty($data[0]->getInput()[3]) ? $data[0]->getInput()[3]->displayName : Language::Get('main','noScript', $langTemplate)); ?></span>
                        <input type="hidden" name="exercises[0][subexercises][0][runFileParameter][0]" value="<?php echo (isset($data[0]) && isset($data[0]->getInput()[3]) && !empty($data[0]->getInput()[3]) ? $data[0]->getInput()[3]->fileId : ''); ?>"/>
                    </td>
                </tr>
                <!--<tr>
                    <td style="width: 18.5%;">
                        <label class="label bold" for=""><?php echo Language::Get('main','testcount', $langTemplate); ?>:</label>
                    </td>
                    <td>
                        <input type="text" class="testcount" name="exercises[0][subexercises][0][processorTestcaseCount][0]" value="0"/> <a href="javascript:void(0);" class="body-option-color update-loop"><?php echo Language::Get('main','update', $langTemplate); ?></a>
                    </td>
                </tr>-->
                <?php
                    $paramcount = 1;
                    if (isset($process['parameter'])){
                        $data = Testcase::decodeTestcase($process['parameter']);
                        if (is_array($data) || is_object($data))
                        {   
                            if (count($data) > 1)
                            {
                                $pro2 = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPparamcount.template.php');
                                $paramcount = array_slice($data, 1)[0]->getInput();
                                if(is_array($paramcount))
                                {
                                    $paramcount = count($paramcount);
                                }
                                $pro2->bind(array('paramcount'=> $paramcount));
                                $pro2->show();
                                $pro3 = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPtestcount.template.php');
                                $pro3->bind(array('testcases'=> count(array_slice($data, 1))));
                                $pro3->show();
                            }
                        }
                    }
                ?>
                <tr>
                    <td colspan="2" style="padding-bottom: 0px;">
                        <div style="width: 760px; overflow:scroll; margin-right: -100px; line-height: 18px;">
                            <?php
                                if (isset($process['parameter'])){
                                    $data = Testcase::decodeTestcase($process['parameter']);
                                    if (is_array($data) || is_object($data))
                                    {
                                        if (count($data) <= 1)
                                        {
                                            $pro = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPaddtest.template.php');
                                            $pro->show();
                                        }
                                        else
                                        {
                                            /*$pro = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPparamcount.template.php');
                                            $pro->bind(array('testcases'=> array_slice($data, 1)));
                                            $pro->show();*/
                                            $pro = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPtable.template.php');
                                            $pro->bind(array('files'=>$data[0]->getFile()));
                                            $pro->bind(array('testcases'=> array_slice($data, 1)));
                                            $pro->bind(array('paramcount'=> $paramcount));
                                            $pro->show();
                                            $pro2 = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPaddtest.template.php');
                                            $pro2->bind(array('invisible'=> true));
                                            $pro2->show();

                                            /*$pro3 = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPtable.template.php');
                                            $pro3->bind(array('testcases'=> array_slice($data, 1)));

                                            $pro3->show();*/

                                        }
                                    }
                                    
                                } else {
                                    $pro = Template::WithTemplateFile(dirname(__FILE__) . '/LOOPaddtest.template.php');
                                    $pro->show();
                                }
                            ?>
                        </div>
                    </td>
                </tr>
                <!--<tr><td> <label class="short left label bold new-line" for="attachment">Anhang:</label><br><br></td><td></td><td></td></tr>-->
            </table>
            <!--<a style="color:#b9b8b8"><s><a href="javascript:void(0);" class="body-option-color add-attachment right">Anhang hinzufügen</s></a>-->

        </div>

    </div>
</div>
<?php ob_end_flush(); ?>