<?php
/**
 * @file default.template.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
?>

<?php include_once dirname(__FILE__) . '/../../../../Assistants/Language.php'; ?>
<?php include_once dirname(__FILE__) . '/../../Boilerplate.php'; ?>
<?php $langTemplate='Processor_default';Language::loadLanguageFile('de', $langTemplate, 'json', dirname(__FILE__).'/'); ?>

<?php
 header('Content-Type: text/html; charset=utf-8');
 ?>
 <div class="content-element ProcessorParameterArea" style="outline:2px solid #b9b8b8;border-radius: 0px;margin: 0px;">
    <?php $helpName = (isset($_GET['name']) ? $_GET['name'] : null);
    if (isset($helpName)){echo MakeInfoButton('extension/'.$helpName,$helpName.'.md');}?>
    <div class="content-body-wrapper" style="padding: 10px; margin-top: 0px;">
        <div class="content-body left" style="width:100%;">
           <!-- default.template.php -->
            <table border="0" style="width:100%">
                <tr>
                    <td colspan="6">
                        <label class="label bold"><?php echo Language::Get('main','params', $langTemplate); ?>: </label>
                        <input type="text" class="parameter-choice wider" name="exercises[0][subexercises][0][processorParameterList][0][]" value="<?php echo (isset($process['parameter']) ? $process['parameter'] : ""); ?>" />
                    </td>
                </tr>
            
            <?php $processAttachments = isset($process['attachment']) ? $process['attachment'] : array();?>
            
            <?php for ($i=0;$i<4;$i++){
                $file = null;
                if (isset($processAttachments[$i])){
                    $attachment = $processAttachments[$i];
                    $file = $attachment['file'];
                }?>
                <tr>
                <td colspan="6">
               <label class="label left bold"><?php echo Language::Get('main','processAttachment', $langTemplate); ?>: </label>
                <span class="fileArea">
                    <input class="fileButton button" type="file" name="exercises[0][subexercises][0][processAttachment][0][]" value="<?php echo Language::Get('main','selectFile', $langTemplate); ?> ..." <?php echo (isset($file['address']) && isset($file['displayName']) ? 'style="display:none";' : '') ;?>/>
                    <?php
                    if (isset($file['address']) && isset($file['displayName'])){
                        $fileURL = generateDownloadURL($file);?>
                    <div class='content-body left divFile'>
                        <?php if (isset($file['fileId'])){ ?>
                        <input type="hidden" name="sheetSolutionId" value="<?php echo $file['fileId']; ?>" />
                        <?php } ?>
                        <?php if (isset($file['address'])){ ?>
                        <input type="hidden" name="sheetSolutionAddress" value="<?php echo $file['address']; ?>" />
                        <?php } ?>
                        <?php if (isset($file['displayName'])){ ?>
                        <input type="hidden" name="sheetSolutionDisplayName" value="<?php echo $file['displayName']; ?>" />
                        <?php } ?>
                        <div class="exercise-sheet-images">
                            <a href="<?php echo $fileURL; ?>" title="<?php echo $file['displayName']; ?>" class="plain" target="_blank">
                                <img src="<?php echo generateCommonFileUrl('img/Download.png');?>" />
                            </a>
                            <a href="javascript:void(0);" title="<?php echo Language::Get('main','removeFile', $langTemplate); ?>" name="deleteAttachmentFile'" class="plain deleteFile">     
                                <img src="img/Delete.png">
                                <?php if (isset($file)){ ?><span class="right warning-simple"></span><?php } ?>
                            </a>
                        </div>
                    </div>
                    <?php } ?>
                </span>
                    </td></tr>
            <?php } ?>
            
                </tr>
            </table>
        </div>
    </div>
</div>