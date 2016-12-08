<?php
/**
 * @file LOOPtestcount.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.4.4
 *
 * @author Ralf Busch <ralfbusch92@gmail.com>
 * @date 2015
 */
?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>

<tr>
    <td style="width: 18.5%;">
        <label class="label bold testcount" for=""><?php echo Language::Get('main','testcount', $langTemplate); ?>:</label>
    </td>
    <td style="width: 81.5%;">
        <input type="text" class="testcount" style="width: 70%;" name="exercises[0][subexercises][0][testcount][]" value="<?php echo (isset($testcases) ? $testcases : '1'); ?>"/><a href="javascript:void(0);" class="body-option-color very-short update-test" style="margin-left: 47px;"><?php echo Language::Get('main','update', $langTemplate); ?></a>
    </td>
</tr>