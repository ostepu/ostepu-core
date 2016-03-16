<?php
/**
 * @file LOOPinput.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
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
<input type="text" class="parameter-choice-test" style="min-width:150px; width: 100%; margin-left:-2px;" name="exercises[0][subexercises][0][inputParameter][0][0][]" value="<?php echo(isset($input[1]) ? $input[1] : '' ); ?>"/>