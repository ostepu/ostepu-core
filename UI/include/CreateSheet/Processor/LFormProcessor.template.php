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
        <td>
            <label class="label bold" for="">Vergleich:</label>
        </td>
        <td>
            <select class="parameter-choice" style="width:auto" name="exercises[0][subexercises][0][processorParameterList][0][]">
               <option value="">Normal</option>
               <option value="distance1">Ähnlichkeit in %</option>
               <option value="regularExpression">regulärer Ausdruck</option>
           </select>
        </td>
        <td>
            <input type="text" style="width:100%" class="parameter-choice" name="exercises[0][subexercises][0][processorParameterList][0][]" value=""/>
        </td>
        </tr>
       
        </table>
            </div>
            </div>