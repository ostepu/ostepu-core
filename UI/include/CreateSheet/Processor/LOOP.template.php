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
        <td colspan="6">
            <label class="label bold" for="">Compiler:</label>
        </td>
        <td>
            <select class="parameter-choice" style="width:auto" name="exercises[0][subexercises][0][processorParameterList][0][]">
               <option value="java">Java</option>
               <option value="cx">Cx</option>
           </select>
        </td>
        </tr>
        
        <tr>
        <td colspan="6">
            <label class="label bold">Parameter: </label>
        </td>
        <td>
            <input type="text" class="parameter-choice wider" name="exercises[0][subexercises][0][processorParameterList][0][]" value="$file"/>
        </td>
        </tr>
            <!--<tr><td> <label class="short left label bold new-line" for="attachment">Anhang:</label><br><br></td><td></td><td></td></tr>-->
            </table>
            <!--<a style="color:#b9b8b8"><s><a href="javascript:void(0);" class="body-option-color add-attachment right">Anhang hinzufügen</s></a>-->
 
            </div>
  
   </div>
            </div>