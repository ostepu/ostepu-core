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

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <?php echo MakeInfoButton('extension','LOOP.md'); ?>
    <div class="content-body-wrapper" style="">
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
                    if (isset($process['parameter']))
                        $params = explode(' ',$process['parameter']);
                    ?>
                    <td colspan="6">
                        <label class="label bold" for=""><?php echo Language::Get('main','executable', $langTemplate); ?>:</label>
                    </td>
                    <td>
                        <select class="parameter-choice" style="width:auto" name="exercises[0][subexercises][0][processorParameterList][0][]">
                            <?php foreach($liste as $key => $value){ ?>
                            <option value="<?php echo $key; ?>"<?php echo (in_array($key, $params) ? ' selected=\"selected\"':'');?>><?php echo $value; ?></option>
                            <?php if (in_array($key, $params))unset($params[array_search($key, $params)]); ?>
                            <?php } ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td colspan="6">
                        <label class="label bold"><?php echo Language::Get('main','parameters', $langTemplate); ?>: </label>
                    </td>
                    <td>
                        <input type="text" class="parameter-choice wider" name="exercises[0][subexercises][0][processorParameterList][0][]" value="<?php echo (count($params)>0 ? implode(' ',$params) : '$file'); ?>"/>
                    </td>
                </tr>
                <!--<tr><td> <label class="short left label bold new-line" for="attachment">Anhang:</label><br><br></td><td></td><td></td></tr>-->
            </table>
            <!--<a style="color:#b9b8b8"><s><a href="javascript:void(0);" class="body-option-color add-attachment right">Anhang hinzufügen</s></a>-->

        </div>

    </div>
</div>