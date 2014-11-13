<?php
/**
 * @file FormAddRadio.template.php
 * @author  Till Uhlig
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="form-input-radio" style="margin:5px 0px;">
    <?php if (isset($choice['choiceId'])){ ?>
    <input type="hidden" name="exercises[0][subexercises][0][choiceId][0]" value="<?php echo $choice['choiceId']; ?>" />
    <?php } ?>
    
    <input type="radio" class="choice-input" name="exercises[0][subexercises][0][correct][0]"<?php echo (isset($choice['correct']) && $choice['correct']==1 ? " checked" : ''); ?>/>
    <input class="form-field input-choice-text" name="exercises[0][subexercises][0][choice][0]" value="<?php echo (isset($choice['text']) ? $choice['text'] : ''); ?>" placeholder="Auswahltext"/>    
    <a href="javascript:void(0);" class="critical-color deny-button delete-choice center">
        <div class="left">
            Auswahlmöglichkeit löschen
            <span class="right<?php if (isset($choice['choiceId'])){ ?> warning-simple<?php } else { ?> transparent-simple<?php } ?>"></span>
        </div>
    </a>
</div>