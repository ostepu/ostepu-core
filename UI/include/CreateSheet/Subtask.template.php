<?php
include_once '../../../Assistants/Structures.php';
include_once '../../../Assistants/Request.php';
include_once '../Config.php';
?>
<li>
    <input class="form-field text-input very-short" name="exercises[0][subexercises][0][maxPoints]" placeholder="Punkte" id="exerciseMaxPoints" />
    <select class="form-field text-input short" name="exercises[0][subexercises][0][exerciseType]" id="exerciseType">
        <?php
        session_start();
        $courseid = null;
        if (isset($_SESSION['JSCACHE'])) {
            $cache = json_decode($_SESSION['JSCACHE'], true);

            foreach ($cache as $excercisetype) {
                $courseid = $excercisetype['courseId'];
                print "<option value=\"".$excercisetype['exerciseTypeId']."\">".$excercisetype['name']."</option>";
                print "<option value=\"".$excercisetype['exerciseTypeId']."b\">".$excercisetype['name']." (Bonus)</option>";
            }
        }
        
        $forms = false;
        $processes = false;
        if ($courseid !== null){
            $result = Request::get($serverURI.'/DB/DBForm/link/exists/course/' . $courseid,array(),'');

            if ( $result['status'] === 200 )
                $forms = true;
                
            $result = Request::get($serverURI.'/DB/DBProcess/link/exists/course/' . $courseid,array(),'');         
            if ( $result['status'] === 200 )
                $processes = true;
        }
        
        ?>
    </select>
    <input class="form-field text-input very-short mime-field" name="exercises[0][subexercises][0][mime-type]" value="pdf" id="mime-type" placeholder="pdf, zip, html, jpg, gif"/>
    <input class="button" type="file" name="exercises[0][subexercises][0][attachment]" value="Anhang auswählen ..." />
                        <a href="javascript:void(0);" class="body-option-color deny-button delete-subtask right">Teilaufgabe löschen</a>
                        
                        
                        
                        
                    <?php if ($forms || $processes) {?>   
                    <div class="content-body-wrapper">
                    <table border="0" style="width:100%">
                    <?php if ($forms){ ?>
                    <tr><td><a href="javascript:void(0);" class="body-option-color very-short use-form">Eingabemaske verwenden</a></td></tr> 
                    <?php } ?>
                    
                    <?php if ($processes){ ?>
                    <tr><td><a href="javascript:void(0);" class="body-option-color very-short use-processor">Verarbeitung hinzufügen</a></td></tr>   
                    <?php } ?>
                    
                    </table>
                        </div>
                    <?php } ?>
    
</li>