<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file LOOPparamcount.template.php
 * @author  Ralf Busch
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>

<tr>
    <td style="width: 18.5%;">
        <label class="label bold parameter-count" for=""><?php echo Language::Get('main','paramcount', $langTemplate); ?>:</label>
    </td>
    <td>
        <select class="parameter-count" name="exercises[0][subexercises][0][parameter-count][]">
            <?php for ($i = 1; $i <= 10; $i++) { ?>
            <option value="<?php echo $i; ?>"<?php echo (isset($paramcount) && $i == $paramcount ? ' selected=\"selected\"':'');?>><?php echo $i; ?></option>
            <?php } ?>
        </select>
    </td>
</tr>