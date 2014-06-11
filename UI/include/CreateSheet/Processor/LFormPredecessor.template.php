<?php
/**
 * @file LFormProcessor.template.php
 * @author  Till Uhlig
 */
 ?>
        <div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
        <div class="content-body-wrapper">
        
            <div class="content-body left">
            <table border="0" style="width:100%">          
                    
       <tr>
       <td><input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="isprintable"/></td><td><label class="label bold">druckbare Zeichen</label></td>
       <td><input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="isalpha"/></td><td><label class="label bold">Buchstaben (A-Z, a-z)</label></td>
       <td><input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="isalphanum"/></td><td><label class="label bold">Buchstaben+Ziffern</label></td>
       </tr>
        <tr>
        <td><input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="isnumeric"/></td><td><label class="label bold">Zahl (0-9,.)</label></td>
        <td><input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="ishex"/></td><td><label class="label bold">Hexadezimalziffern (A-F, a-f, 0-9)</label></td>
        <td><input type="checkbox" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value="isdigit"/></td><td><label class="label bold">Ziffern (0-9)</label></td>
        </tr>   
        <tr><td colspan="6"><label class="label bold">regulärer Ausdruck: </label> <input type="text" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value=""/> (ohne Leerzeichen)</td></tr>
        </table>
            </div>
            </div>