<?php
/**
 * @file Subtask.template.php
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2014,2016
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013
 */
?>

<?php include_once dirname(__FILE__) . '/../../../Assistants/Language.php'; ?>
<?php $langTemplate='CreateSheet_Subtask';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
header('Content-Type: text/html; charset=utf-8');
//include_once dirname(__FILE__) . '/../../../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../../../Assistants/Request.php';
if (file_exists(dirname(__FILE__) . '/../Config.php')){ include dirname(__FILE__) . '/../Config.php';}
include_once dirname(__FILE__) . '/../Helpers.php';
?>

<li>
    <span style="display:inline-table">
    <input class="form-field text-input very-short" name="exercises[0][subexercises][0][maxPoints]" placeholder="<?php echo Language::Get('main','points', $langTemplate); ?>" id="exerciseMaxPoints" <?php echo isset($maxPoints) ? "value='{$maxPoints}'" : ''?>/>
    <select class="form-field text-input short" name="exercises[0][subexercises][0][exerciseType]" id="exerciseType">
        <?php
        if (!isset($exerciseTypes)){
            session_start();
            $cid = null;
            if (isset($_SESSION['JSCACHE'])) {
                $exerciseTypes = json_decode($_SESSION['JSCACHE'], true);
                if (isset($exerciseTypes[0]['courseId']))
                    $cid = $exerciseTypes[0]['courseId'];
            }
        }
        
        foreach ($exerciseTypes as $exerciseType) {
            print "<option value=\"".$exerciseType['exerciseTypeId']."\" ".(isset($type) && $type==$exerciseType['exerciseTypeId'] && (!isset($bonus) || !$bonus)? "selected=\"selected\"" : '').">".$exerciseType['name']."</option>";
            print "<option value=\"".$exerciseType['exerciseTypeId']."b\" ".(isset($type) && $type==$exerciseType['exerciseTypeId'] && isset($bonus) && $bonus? "selected=\"selected\"" : '').">".$exerciseType['name']." (".Language::Get('main','bonus', $langTemplate).")</option>";
        }
                
        ?>
    </select>
    
    <?php 
        $printTypes = array(); 
        if (isset($fileTypes)) {
            ///var_dump($fileTypes);
            $mimeTypes = FILE_TYPE::$mimeType;
            $tempPrintTypes=array();
            
            foreach ($fileTypes as $type) {
                if (!isset($type['text'])) continue;
                $parts = explode(' ', $type['text']);
                
                foreach ($mimeTypes as $key => $mimes){
                    $found=false;
                    foreach ($mimes as $mime) {
                        if ($mime==$parts[0]){
                            $parts[0] = $key;
                            $found=true;
                            break;
                        }
                    }
                    if ($found) break;
                }
                
                if (isset($parts[1])){
                    $parts[1] = ltrim($parts[1],'*');
                } else 
                    $parts[1]='';
                    
                $tempPrintTypes[] = $parts[0].$parts[1];
            }
            
            
            foreach($tempPrintTypes as $type)
                if (!in_array($type,$printTypes))
                    $printTypes[] = $type;
        }
                
    ?>
    
    <?php if (isset($id)){ ?>
    <input type="hidden" name="exercises[0][subexercises][0][id]" value="<?php echo $id; ?>" />
    <?php } ?>
    
    <input class="form-field text-input short mime-field" name="exercises[0][subexercises][0][mime-type]" id="mime-type" title="<?php echo Language::Get('main','mimeDescription', $langTemplate); ?>,..." placeholder="txt.java, pdf, zip, html, jpg, gif" <?php echo isset($fileTypes) ? "value='".implode(', ',$printTypes)."'" : ''?>/>
        
    <span class="fileArea">
        <input class="fileButton button" type="file" name="exercises[0][subexercises][0][attachment]" value="<?php echo Language::Get('main','selectAttachment', $langTemplate); ?> ..." <?php echo (isset($attachments[0]) ? 'style="display:none";' : '') ;?>/>
        <?php
        if (isset($attachments[0])){
            $fileURL = "../FS/FSBinder/{$attachments[0]['address']}/{$attachments[0]['displayName']}";?>
        <span class='divFile'>
            <?php if (isset($attachments[0]['fileId'])){ ?>
            <input type="hidden" name="exercises[0][subexercises][0][attachment][fileId]" value="<?php echo $attachments[0]['fileId']; ?>" />
            <?php } ?>
            <?php if (isset($attachments[0]['address'])){ ?>
            <input type="hidden" name="exercises[0][subexercises][0][attachment][address]" value="<?php echo $attachments[0]['address']; ?>" />
            <?php } ?>
            <?php if (isset($attachments[0]['displayName'])){ ?>
            <input type="hidden" name="exercises[0][subexercises][0][attachment][displayName]" value="<?php echo $attachments[0]['displayName']; ?>" />
            <?php } ?>
            <div class="exercise-sheet-images">
                <a href="<?php echo $fileURL; ?>" title="<?php echo $attachments[0]['displayName']; ?>" class="plain" target="_blank">
                    <img src="Images/Download.png" />
                </a>
                <a href="javascript:void(0);" title="<?php echo Language::Get('main','removeAttachment', $langTemplate); ?> ..." name="deleteAttachmentFile" class="plain deleteFile">                                      
                    <img src="Images/Delete.png">
                    <?php if (isset($attachments[0])){ ?><span class="right warning-simple" ></span><?php } ?>
                </a>
            </div>
        </span>
        <?php } ?>
    </span>
    
    <?php if (isset($submittable)){ ?>
    <input type="hidden" name="exercises[0][subexercises][0][submittable]" value="<?php echo $submittable; ?>" />
    <?php } ?>
        <input type="checkbox" value="0" name="exercises[0][subexercises][0][submittable]"<?php echo (isset($submittable) && $submittable=='0' ? " checked" : ''); ?>/>
        <?php echo Language::Get('main','notSubmittable', $langTemplate); ?>
    </span>
    
    <a href="javascript:void(0);" class="deny-button delete-subtask critical-color right"><?php echo Language::Get('main','removeSubtask', $langTemplate); ?><?php if (isset($id)){ ?><span class="right warning-simple"></span><?php } ?>  </a>
                      
                        
    <?php
    
        $formsAllowed=false;
        if (!isset($forms)){
            $formsAllowed = false;
            $result = Request::get($serverURI.'/DB/DBForm/link/exists/course/' . $cid,array(),'');
            if ( $result['status'] === 200 )
                $formsAllowed = true;
        } else 
            $formsAllowed=true;
        
        $processesAllowed=false;
        if (!isset($processors)){
            $processesAllowed = false;
            $result = Request::get($serverURI.'/DB/DBProcess/link/exists/course/' . $cid,array(),'');
            if ( $result['status'] === 200 )
                $processesAllowed = true;
        } else 
            $processesAllowed=true;
    ?>
                        
    <?php if ($formsAllowed || $processesAllowed) {?>   
    <div class="content-body-wrapper">
    <table border="0" style="width:100%">
    <?php  ?>
    <?php if ($formsAllowed){ ?>
    <tr><td><a href="javascript:void(0);" class="body-option-color very-short use-form"><?php echo Language::Get('main','useForm', $langTemplate); ?></a>
    <?php
        if (isset($forms)){
            foreach($forms as $form){
                $form = Template::WithTemplateFile('include/CreateSheet/Form/FormSettings.template.php');
                if (isset($cid))
                    $form->bind(array('cid'=>$cid));
                if (isset($uid))
                    $form->bind(array('uid'=>$uid));
                if (isset($sid))
                    $form->bind(array('sid'=>$sid));
                if (isset($forms))
                    $form->bind(array('forms'=>$forms));
                if (isset($processes))
                    $form->bind(array('processes'=>$processes));
                if (isset($processors))
                    $form->bind(array('processors'=>$processors));
                $form->show();
            }
        }
    ?>
    </td></tr> 
    <?php } ?>
    
    <?php if ($processesAllowed){ ?>
    <?php
        if (isset($processes)){
            foreach($processes as $process){
                $pro = Template::WithTemplateFile('include/CreateSheet/Processor/Processor.template.php');
                if (isset($cid))
                    $pro->bind(array('cid'=>$cid));
                if (isset($uid))
                    $pro->bind(array('uid'=>$uid));
                if (isset($sid))
                    $pro->bind(array('sid'=>$sid));
                $pro->bind(array('process'=>$process));
                $pro->bind(array('processors'=>$processors));
                $pro->show();
            }
        }
    ?>
    <tr><td>
    <a href="javascript:void(0);" class="body-option-color very-short use-processor"><?php echo Language::Get('main','useProcessor', $langTemplate); ?></a>
    </td></tr>   
    <?php } ?>
    
    </table>
        </div>
    <?php } ?>
    <hr noshade='noshade' size='5' style="border-radius: 5px;color: #b9b8b8;">
</li>