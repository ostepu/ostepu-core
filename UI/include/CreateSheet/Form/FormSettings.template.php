<?php
/**
 * @file FormSettings.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
        <div class="content-element form" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
                        
            <div class="content-header">
                <div class="content-title left uppercase">Eingabemaske</div>
                <div class="critical-color right">
                    <a href="javascript:void(0);" class="delete-form">Eingabemaske löschen<?php if (isset($forms[0]['formId'])){ ?><span class="right warning-simple"></span><?php } ?></a>
                </div>
            </div>
            

            <div class="content-body-wrapper">
            <?php if (!isset($forms)){ ?>
                <div class="content-body left">
                    <a href="javascript:void(0);" class="body-option-color left deny-button use-input">Eingabezeile</a><br><br>
                    <a href="javascript:void(0);" class="body-option-color left deny-button use-radio">Einfachauswahl</a><br><br>
                    <a href="javascript:void(0);" class="body-option-color left deny-button use-checkbox">Mehrfachauswahl</a>
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