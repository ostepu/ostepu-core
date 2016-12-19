<?php
/**
 * @file LOOPaddtest.template.php
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

<a href="javascript:void(0);" class="body-option-color very-short add-test"<?php echo(isset($invisible) && $invisible ? ' style="display:none"' : ''); ?>><?php echo Language::Get('main','addtest', $langTemplate); ?></a>