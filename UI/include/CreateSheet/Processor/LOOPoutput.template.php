<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file LOOPparamcount.template.php
 * @author  Ralf Busch
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>
<input type="text" class="parameter-choice-test" style="min-width:160px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][outputParameter][0][0]" value="<?php echo(isset($output[1]) ? $output[1] : '' ); ?>"/>