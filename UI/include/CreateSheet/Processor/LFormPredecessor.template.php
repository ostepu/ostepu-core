<?php
/**
 * @file LFormProcessor.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <div class="content-body-wrapper">
        <div class="content-body left">
            <table border="0" style="width:100%">          
                <?php
                $liste = array(
                                'isprintable' => 'druckbare Zeichen',
                                'isalpha' => 'Buchstaben (A-Z, a-z)',
                                'isalphanum' => 'Buchstaben+Ziffern',
                                'isnumeric' => 'Zahl (0-9,)',
                                'ishex' => 'Hexadezimalziffern (A-F, a-f, 0-9)',
                                'isdigit' => 'Ziffern (0-9)');
                
                $i=0;
                $params = array();
                if (isset($process['parameter']))
                    $params = explode(' ',$process['parameter']);
                foreach ($liste as $key => $value) {
                ?>
                
                <?php if ($i%3==0){ ?>
                <tr>
                <?php } ?>
                    <td>
                        <input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="<?php echo $key; ?>"<?php echo (in_array($key, $params) ? ' checked':'');?>/>
                        <?php if (in_array($key, $params))unset($params[array_search($key, $params)]); ?>
                    </td>
                    <td>
                        <label class="label bold"><?php echo $value; ?></label>
                    </td>
                <?php if ($i%3==2){ ?>
                </tr> 
                <?php } ?>
                
                <?php
                    $i++;
                } 
                ?>
                
                <tr>
                    <td colspan="6">
                        <label class="label bold">regulärer Ausdruck: </label>
                        <input type="text" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="<?php echo implode(' ',$params); ?>" placeholder="%^([0-9]{5})$%" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>