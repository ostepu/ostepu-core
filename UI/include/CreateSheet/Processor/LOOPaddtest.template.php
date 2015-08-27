<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Processor_LOOP';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
/**
 * @file LOOPparamcount.template.php
 * @author  Ralf Busch
 */
 header('Content-Type: text/html; charset=utf-8');
 ?>

<a href="javascript:void(0);" class="body-option-color very-short add-test"<?php echo(isset($invisible) && $invisible ? ' style="display:none"' : ''); ?>><?php echo Language::Get('main','addtest', $langTemplate); ?></a>