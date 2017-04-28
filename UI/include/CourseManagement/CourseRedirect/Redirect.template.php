<?php
/**
 * @file Redirect.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 */

?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Redirect';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
 
<?php
 // soll ein Template verwendet werden?
 $template = 'none';
 if (isset($_GET['template'])){
    $tmp = basename($_GET['template']);
    if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $tmp . '.json')){
        $template = $tmp;
    }
 }

 if ($template !== 'none' && isset($template)){
    $content = json_decode(file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . '.json'), true);
    extract($content, EXTR_SKIP);
 }
?>
 
<div class="RedirectElement left new-line">
    <a style="padding-left:0px" class="text-button error-color right removeRedirectElement" href="javascript:void(0);">löschen</a>
    <input type="hidden" class="RedirectName" name="data[0][id]" value="<?php echo (isset($id) ? $id : null); ?>">

    <span class="left element-block">
        <label class="short label bold new-line"
               for="title"><?php echo Language::Get('main','title', $langTemplate); ?>:</label>
        <input class="form-field text-input RedirectName"
                    <?php if (isset($title)) {?> value="<?php echo $title; ?>" <?php } ?>
                    type="text" id="title" name="data[0][title]"/>
    </span>
    
    <span class="left element-block">
        <label class="short label bold new-line"
               for="url"><?php echo Language::Get('main','url', $langTemplate); ?>:</label>
        <input class="form-field text-input RedirectName"
                    <?php if (isset($url)) {?> value="<?php echo $url; ?>" <?php } ?>
                    type="text" id="url" name="data[0][url]"/>
    </span>
    
    <span class="left element-block">
        <label class="short label bold new-line"
               for="location"><?php echo Language::Get('main','location', $langTemplate); ?>:</label>
        <select class="form-field text-input short RedirectName" name="data[0][location]" id="location">
            <?php
                $locations = array('sheet' => Language::Get('main','sheetLocation', $langTemplate),'course' => Language::Get('main','courseLocation', $langTemplate));
                $currentLocation = isset($location) ? $location : 'sheet';
                
                foreach ($locations as $locName => $loc){
                    echo     '<option value="' . $locName  . '"'.($currentLocation == $locName ? ' selected' : '').'>'.$loc.'</option>';
                }
            ?>
        </select>
    </span>

    <span class="right element-block">
        <input class="RedirectName" type="checkbox" name="data[0][auth]" id="useTransaction" <?php echo (isset($authentication) && $authentication == 'transaction' ? 'checked="true"' : ''); ?> value="transaction"/>
        <label for="useTransaction"> <?php echo Language::Get('main','transaction', $langTemplate); ?></label>
    </span>

    <hr class="new-line">
</div>