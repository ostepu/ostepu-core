<?php
/**
 * @file Notification.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/ostepu-core)
 * @since 0.4.4
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 */

?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php $langTemplate='Notification';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
<div class="notificationElement left new-line">
    <a style="padding-left:0px" class="text-button error-color right removeNotificationElement" href="javascript:void(0);">löschen</a>
    <input type="hidden" class="notificationName" name="data[0][id]" value="<?php echo (isset($id) ? $id : null); ?>">

    <span class="left element-block">
    <!--<label class="short label bold new-line"
           for="message"><?php echo Language::Get('main','message', $langTemplate); ?>:</label>-->
    <textarea name="data[0][message]"
              class="form-field wider notificationName"
              rows="3" maxlength="2500"><?php if (isset($text)){echo $text;} ?></textarea>
    </span>

    <span class="left element-block">
    <label class="label bold" for="startDate"><?php echo Language::Get('main','startDate', $langTemplate); ?>:</label>
    <span id="datetimepicker1" class="input-append date dtpicker">
        <input class="notificationName text-input" data-format="dd.MM.yyyy - hh:mm"
        value="<?php echo (isset($begin) && ctype_digit($begin) && $begin > 0 ? date('d.m.Y - H:i', $begin) : (isset($begin) && $begin == 0 ? '' : date('d.m.Y - H:i', time()))); ?>"
        type="text" id="startDate" name="data[0][startDate]" class="dtDate"/>
        <span class="add-on">
          ...
        </span>
    </span>
    </span>

    <div class="left element-block">
    <label class="label bold" for="endDate"><?php echo Language::Get('main','endDate', $langTemplate); ?>:</label>
    <span id="datetimepicker2" class="input-append date dtpicker">
        <input class="notificationName text-input" data-format="dd.MM.yyyy - hh:mm"
        value="<?php echo (isset($end) && ctype_digit($end) && $end > 0 ? date('d.m.Y - H:i', $end) : (isset($begin) && $begin == 0 ? '' : date('d.m.Y - H:i', time()+(7*24*60*60)))); ?>"
        type="text" id="endDate" name="data[0][endDate]" class="dtDate"/>
        <span class="add-on">
          ...
        </span>
    </span>
    </div>

    <span class="left element-block">
    <label class="label bold" for="rights"><?php echo Language::Get('main','rights', $langTemplate); ?>:</label>
        <?php
            $requiredStatus = (isset($requiredStatus) ? $requiredStatus : null);
            $setRights = explode(',',$requiredStatus);
        ?>
        <input class="notificationName2" type="checkbox" name="data[0][rights][]" id="student" <?php echo (in_array('0',$setRights) || $requiredStatus === null ? 'checked="true"' : ''); ?> value="0"/>
        <label for="grantStudentRights"> <?php echo Language::Get('main','student', $langTemplate); ?></label>
        <input class="notificationName2" type="checkbox" name="data[0][rights][]" id="tutor" <?php echo (in_array('1',$setRights) ? 'checked="true"' : ''); ?> value="1"/>
        <label for="grantTutorRights"> <?php echo Language::Get('main','tutor', $langTemplate); ?></label>
        <input class="notificationName2" type="checkbox" name="data[0][rights][]" id="lecturer" <?php echo (in_array('2',$setRights) ? 'checked="true"' : ''); ?> value="2"/>
        <label for="grantLecturerRights"> <?php echo Language::Get('main','lecturer', $langTemplate); ?></label>
        <input class="notificationName2" type="checkbox" name="data[0][rights][]" id="admin" <?php echo (in_array('3',$setRights) ? 'checked="true"' : ''); ?> value="3"/>
        <label for="grantAdminRights"> <?php echo Language::Get('main','administrator', $langTemplate); ?></label>
    </span>

    <hr class="new-line">
</div>