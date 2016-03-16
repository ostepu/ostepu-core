<?php
/**
 * @file FormSettings.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>

<?php $langTemplate='Form_Settings';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
        <div class="content-element form" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                        
            <div class="content-header">
                <?php echo MakeInfoButton('extension/LForm','LForm.md'); ?>
                <div class="content-title left uppercase"><?php echo Language::Get('main','title', $langTemplate); ?></div>
                <div class="critical-color right">
                    <a href="javascript:void(0);" class="delete-form"><?php echo Language::Get('main','execute', $langTemplate); ?><?php if (isset($forms[0]['formId'])){ ?><span class="right warning-simple"></span><?php } ?></a>
                </div>
            </div>
            

            <div class="content-body-wrapper">
            <?php if (!isset($forms)){ ?>
                <div class="content-body left">
                    <a href="javascript:void(0);" class="body-option-color left deny-button use-input"><?php echo Language::Get('main','input', $langTemplate); ?></a><br><br>
                    <a href="javascript:void(0);" class="body-option-color left deny-button use-radio"><?php echo Language::Get('main','radio', $langTemplate); ?></a><br><br>
                    <a href="javascript:void(0);" class="body-option-color left deny-button use-checkbox"><?php echo Language::Get('main','checkbox', $langTemplate); ?></a>
                </div>
            <?php } ?>
            <?php
                if (isset($forms)){
                    foreach ($forms as $form){
                        $input = null;
                        switch ($form['type']){
                            case 0:
                                $input = Template::WithTemplateFile('include/CreateSheet/Form/FormInput.template.php');break;
                            case 1:
                                $input = Template::WithTemplateFile('include/CreateSheet/Form/FormRadio.template.php');break;
                            case 2:
                                $input = Template::WithTemplateFile('include/CreateSheet/Form/FormCheckbox.template.php');break;
                            default:
                                break;
                        }
                        
                        if ($input!==null){
                            if (isset($cid))
                                $input->bind(array('cid'=>$cid));
                            if (isset($uid))
                                $input->bind(array('uid'=>$uid));
                            if (isset($sid))
                                $input->bind(array('sid'=>$sid));
                            if (isset($form))
                                $input->bind(array('form'=>$form));
                            $input->show();
                        }
                    }
                }
            ?>
            </div>
        </div>