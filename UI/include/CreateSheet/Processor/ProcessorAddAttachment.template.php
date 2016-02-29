<?php
?>
<?php $langTemplate='Processor_AddAttachment';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>
<?php
/**
 * @file ProcessorAddAttachment.template.php
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 */

 header('Content-Type: text/html; charset=utf-8');
 ?>
 <div class="processor-attachment new-line" style="margin:5px 0px; width:100%;">
 <tr><td></td><td>


    <input class="button processor-attachment-file" type="file" name="exercises[0][subexercises][0][processAttachment][0][]" value="<?php echo Language::Get('main','selectAttachment', $langTemplate); ?> ..." />
    </td><td>
<a href="javascript:void(0);" class="body-option-color deny-button delete-attachment center"><?php echo Language::Get('main','removeAttachment', $langTemplate); ?></a>
</td></tr>
            </div>