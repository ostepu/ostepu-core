<?php
/**
 * @file ExerciseSettings.template.php
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2013-2014
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2013
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013
 */
?>

<?php include_once dirname(__FILE__) . '/../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../Boilerplate.php'; ?>
<?php $langTemplate='CreateSheet_ExerciseSettings';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php 
include_once dirname(__FILE__) . '/../../../Assistants/LArraySorter.php';
header('Content-Type: text/html; charset=utf-8'); 
$choiceId = 0; // obsolete???
?>
        
<div class="content-element collapsible">
    <div class="content-header">
        <?php echo MakeInfoButton('page/admin/createSheet','exercise.md'); ?>
        <div class="content-title left uppercase"><?php echo Language::Get('main','exercise', $langTemplate); ?> <?php echo isset($exercises[0]['link']) ? $exercises[0]['link'] : '???' ?></div>
        <div class="critical-color bold right">
            <a href="javascript:void(0);" class="delete-exercise"><?php echo Language::Get('main','removeExercise', $langTemplate); ?><?php if (isset($exercises)){ ?><span class="right warning-simple"></span><?php } ?></a>
        </div>
    </div>

    <div class="content-body-wrapper">
        <div class="content-body left">
            <ol class="full-width-list lower-alpha">
            
            <?php 
                if (isset($exercises)) {
                    foreach ($exercises as $key => $exercise){
                        if (!isset($exercise['id'])){
                            $exercises[$key]['id']=null;
                        }
                    }
                
                    $exercises = LArraySorter::orderBy($exercises,'linkName',SORT_ASC);
                    foreach ($exercises as $key => $exercise){
                        $subtask = Template::WithTemplateFile('include/CreateSheet/Subtask.template.php');
                        $subtask->bind($exercise);
                        
                        if (isset($forms)){
                            $exerciseForms=array();
                            foreach ($forms as $form){
                                if (!isset($form['exerciseId'])) continue;
                                if ($form['exerciseId']==$exercise['id'])
                                    $exerciseForms[] = $form;
                            }
                            if (!empty($exerciseForms))
                                $subtask->bind(array('forms'=>$exerciseForms));
                        }
                        
                        if (isset($processes)){
                            $exerciseProcesses=array();
                            foreach ($processes as $process){
                                if (!isset($process['exercise']['id'])) continue;
                                if ($process['exercise']['id']==$exercise['id'])
                                    $exerciseProcesses[] = $process;
                            }
                            if (!empty($exerciseProcesses))
                                $subtask->bind(array('processes'=>$exerciseProcesses));
                        }
                        
                        if (isset($cid))
                            $subtask->bind(array('cid'=>$cid));
                        if (isset($uid))
                            $subtask->bind(array('uid'=>$uid));
                        if (isset($sid))
                            $subtask->bind(array('sid'=>$sid));
                        $subtask->bind(array('exerciseTypes'=>$exerciseTypes));
                        $subtask->bind(array('processors'=>$processors));
                        $subtask->show();
                    } 
                }
            ?>
            
                <li class="skip-item">
                    <a href="javascript:void(0);" class="body-option-color right deny-button skip-list-item"><?php echo Language::Get('main','addSubtask', $langTemplate); ?></a>
                </li>
            </ol>
        </div>
    </div> <!-- end: content-body -->
</div> <!-- end: content-wrapper -->