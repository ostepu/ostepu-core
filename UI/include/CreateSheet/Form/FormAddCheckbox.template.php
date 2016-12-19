<?php
/**
 * @file FormAddCheckbox.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */
?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Form_AddChoice';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="form-input-checkbox" style="margin:5px 0px;">
    <?php if (isset($choice['choiceId'])){ ?>
    <input type="hidden" class="choice-id" name="exercises[0][subexercises][0][choiceId][0]" value="<?php echo $choice['choiceId']; ?>" />
    <?php } ?>
    
    <input type="checkbox" value="" class="choice-input" name="exercises[0][subexercises][0][correct][0]"<?php echo (isset($choice['correct']) && $choice['correct']==1 ? " checked" : ''); ?>/>
    <input class="form-field input-choice-text" name="exercises[0][subexercises][0][choice][0]" value="<?php echo (isset($choice['text']) ? $choice['text'] : ''); ?>" placeholder="<?php echo Language::Get('main','choicePlaceholder', $langTemplate); ?>"/>    
    <a href="javascript:void(0);" class="critical-color deny-button delete-choice center">
        <div class="left">
            <?php echo Language::Get('main','removeChoice', $langTemplate); ?>
            <span class=" right<?php if (isset($choice['choiceId'])){ ?> warning-simple<?php } else { ?> transparent-simple<?php } ?>"></span>
        </div>
    </a>   
</div>