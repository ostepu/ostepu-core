<?php
/**
 * @file LOOP.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <div class="content-body-wrapper" style="">
        <div class="content-body left" style="width:100%;">
            <table border="1" style="width:100%;"> 
                <tr>
                    <?php
                    $liste = array(
                                    'java' => 'Java',
                                    'cx' => 'Cx',
                                    'custom' => 'Benutzerdefiniert');
                    
                    $i=0;
                    $params = array();
                    if (isset($process['parameter']))
                        $params = explode(' ',$process['parameter']);
                    ?>
                    <td colspan="6">
                        <label class="label bold" for="">Anwendung:</label>
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
                        <label class="label bold">Parameter: </label>
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