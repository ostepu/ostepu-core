<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_Processor';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file Processor.template.php
 * @author  Till Uhlig
 */
header('Content-Type: text/html; charset=utf-8');

include_once dirname(__FILE__) . '/../../../../Assistants/Structures.php';
include_once dirname(__FILE__) . '/../../../../Assistants/Request.php';
if (file_exists(dirname(__FILE__) . '/../../Config.php')) include dirname(__FILE__) . '/../../Config.php';

if (!isset($cid)){
    session_start();
    $cid = null;
    if (isset($_SESSION['JSCACHE'])) {
        $cache = json_decode($_SESSION['JSCACHE'], true);

        foreach ($cache as $excercisetype) {
            $cid = $excercisetype['courseId'];
            break;
        }
    }
}

if (!isset($processors)){
    if ($cid!==null){           
        $result = Request::get($serverURI.'/DB/DBProcess/processList/process/course/' . $cid,array(),'');
    } else 
        $result['status'] = 409;
        
    if ( $result['status'] >= 200 && 
         $result['status'] <= 299 ){
         
        $processors = Process::decodeProcess($result['content']);
        if (!is_array($processors)) $processors = array($processors);
        $components = array();
        foreach ($processors as $processor){
            $components[] = $processor->getTarget();
        }
    } else {
       
    }
} else {
    if (!is_array($processors)) $processors = array($processors);
    $components = $processors;
}

if (isset($processors)){
?>

<tr><td>
        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
        <div class="content-header">
            <div class="content-title left uppercase"><?php echo Language::Get('main','title', $langTemplate); ?></div>
        <div class="critical-color right">
                <a href="javascript:void(0);" class="delete-processor"><?php echo Language::Get('main','removeProcessor', $langTemplate); ?><?php if (isset($process['processId'])){ ?><span class="right warning-simple"></span><?php } ?></a>
            </div>
        </div>
        

        <div class="content-body-wrapper" style="margin-top: 0px; padding: 10px;">
            <div class="content-body left"> </div>
            <label class="short left label bold" for="exerciseType"><?php echo Language::Get('main','selectProcessor', $langTemplate); ?>:</label>
            <?php if (isset($process['processId'])){?>
            <input type="hidden" class="processor-id" name="exercises[0][subexercises][0][processorId][0]" value="<?php echo $process['processId']; ?>" />
            <?php } ?>  
               <select class="form-field text-input processor-type" style="width:auto" name="exercises[0][subexercises][0][processorType][0]" value="Modul">
        <?php                 
                $selectedComponent=null;
                foreach ($components as $link){
                    if ($link->getId() === null || $link->getName() === null) continue;
        ?>
                    <option value="<?php echo $link->getId(); ?>"<?php echo (isset($process['target']['id']) && $process['target']['id']==$link->getId() ? " selected=\"selected\"" : '' ); ?>><?php echo $link->getName(); ?></option>
        <?php       if ($selectedComponent===NULL || (isset($process['target']['id']) && $process['target']['id']==$link->getId()))
                        $selectedComponent=$link;
               }
        ?>
          
    </select>
    <br><br>
    <?php
        if (isset($process) && isset($selectedComponent)){
            $pro = Template::WithTemplateFile('include/CreateSheet/Processor/'.$selectedComponent->getName().'.template.php');
            if (isset($cid))
                $pro->bind(array('cid'=>$cid));
            if (isset($uid))
                $pro->bind(array('uid'=>$uid));
            if (isset($sid))
                $pro->bind(array('sid'=>$sid));
            $pro->bind(array('process'=>$process));
            $pro->bind(array('component'=>$selectedComponent));
            $pro->show();
        }
    ?>
              <div class="form-field processor-parameter-area" style="width:100%; display: none;"></div>
            </div></div>
</td></tr>

<?php
} else {
?>

<tr><td>
        <div class="content-element processor" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                        
        <div class="content-header">
            <div class="content-title left uppercase"><?php echo Language::Get('main','title', $langTemplate); ?></div>
        <div class="critical-color right">
                <a href="javascript:void(0);" class="delete-processor"><?php echo Language::Get('main','removeProcessor', $langTemplate); ?></a>
            </div>
        </div>
                <div class="content-body-wrapper">
        
            <div class="content-body left"> </div>
            
        <?php echo Language::Get('main','noData', $langTemplate); ?>
      
        </div></div>
          </td></tr>
<?php
}
?>
