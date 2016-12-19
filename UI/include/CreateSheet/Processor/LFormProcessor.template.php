<?php
/**
 * @file LFormProcessor.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>
<?php $langTemplate='Processor_LFormProcessor';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <?php echo MakeInfoButton('extension/LFormProcessor','LFormProcessor.md'); ?>
    <div class="content-body-wrapper">
        <div class="content-body left">
            <table border="0" style="width:100%">          
                <tr>
                    <?php
                    $liste = array(
                                    '' => Language::Get('main','normal', $langTemplate),
                                    'distance1' => Language::Get('main','distance', $langTemplate),
                                    'regularExpression' => Language::Get('main','regularExpression', $langTemplate));
                    
                    $i=0;
                    $params = array();
                    if (isset($process['parameter']))
                        $params = explode(' ',$process['parameter']);
                    ?>
                    <td>
                        <label class="label bold" for=""><?php echo Language::Get('main','compare', $langTemplate); ?>:</label>
                    </td>
                    <td>
                        <select class="parameter-choice" style="width:auto" name="exercises[0][subexercises][0][processorParameterList][0][]">
                            <?php foreach($liste as $key => $value){ ?>
                            <option value="<?php echo $key; ?>"<?php echo (in_array($key, $params) ? ' selected=\"selected\"':'');?>><?php echo $value; ?></option>
                            <?php if (in_array($key, $params))unset($params[array_search($key, $params)]); ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" style="width:100%" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="<?php echo (count($params)>0 ? implode(' ',$params) : ''); ?>"/>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>