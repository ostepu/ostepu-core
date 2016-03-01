<?php
/**
 * @file BackupSegment.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

#region BackupSegment
class BackupSegment
{
    private static $initialized=false;
    public static $name = 'backupSegment';
    public static $installed = false;
    public static $page = 8;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate='BackupSegment';

    public static $onEvents = array(
                                    'createImage'=>array(
                                        'name'=>'createImage',
                                        'event'=>array('actionCreateImage'),
                                        'procedure'=>'installCreateImage',
                                        'enabledInstall'=>true
                                        ),
                                    'listImage'=>array(
                                        'name'=>'listImage',
                                        'event'=>array('actionListImage'),
                                        'procedure'=>'installListImage',
                                        'enabledInstall'=>true
                                        ),
                                    'installImage'=>array(
                                        'name'=>'installImage',
                                        'event'=>array('actionInstallImage'),
                                        'procedure'=>'installInstallImage',
                                        'enabledInstall'=>true
                                        )
                                    );

    public static function getDefaults()
    {
        $res = array(
                     'path' => array('data[BACK][path]', '/var/www/backup'),
                     'database' => array('data[BACK][database]', 'enabled'),
                     'files' => array('data[BACK][files]', 'enabled')
                     );
        return $res;
    }

    public static function checkExecutability($data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array(['name'=>'mysqldump','exec'=>'mysqldump --version','desc'=>'mysqldump --version']);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults();

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['BACK']['path'], 'data[BACK][path]', $def['path'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['BACK']['database'], 'data[BACK][database]', $def['database'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['BACK']['files'], 'data[BACK][files]', $def['files'][1], true);
        echo $text;

        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('main','description',self::$langTemplate));

        $text .= Design::erstelleZeile($console, Installation::Get('createImage','imagePath',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['BACK']['path'], 'data[BACK][path]', $data['BACK']['path'], true), 'v');
        $text .= Design::erstelleZeile($console, Installation::Get('createImage','packDatabase',self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['BACK']['database'], 'data[BACK][database]', 'enabled', null, true), 'v_c');
        $text .= Design::erstelleZeile($console, Installation::Get('createImage','packFiles',self::$langTemplate), 'e', Design::erstelleAuswahl($console, $data['BACK']['files'], 'data[BACK][files]', 'enabled', null, true), 'v_c');
        if (self::$onEvents['createImage']['enabledInstall']){
            $text .= Design::erstelleZeile($console, Installation::Get('createImage','createImageDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['createImage']['event'][0],Installation::Get('createImage','createImage',self::$langTemplate)), 'h');
        }

        $createBackup=false;
        if (isset($result[self::$onEvents['createImage']['name']])){
            $content = $result[self::$onEvents['createImage']['name']]['content'];
            if (!isset($content['databaseOutput'])) $content['databaseOutput'] = '';
            if (!isset($content['databaseOutputStatus'])) $content['databaseOutputStatus'] = 1;
            if (!isset($content['databaseOutputSize'])) $content['databaseOutputSize'] = 0;
            if (!isset($content['filesOutputSize'])) $content['filesOutputSize'] = 0;
            if (!isset($content['filesOutputAmount'])) $content['filesOutputAmount'] = 0;
            if (!isset($content['file'])) $content['file'] = '???';

            $createBackup=true;
            if (!empty($content['output'])){
                $text .= Design::erstelleZeile($console, Installation::Get('createImage','databaseMessage',self::$langTemplate) , 'e', $content['databaseOutput'], 'v error_light break');
            }

            if (isset($data['BACK']['database']) && $data['BACK']['database'] == 'enabled'){
                $text .= Design::erstelleZeile($console, Installation::Get('createImage','databaseStatus',self::$langTemplate) , 'e', ($content['databaseOutputStatus'] == 0 ? Installation::Get('main','ok') : Installation::Get('main','fail')), 'v_c');
                $text .= Design::erstelleZeile($console, Installation::Get('createImage','databaseSize',self::$langTemplate) , 'e', Design::formatBytes($content['databaseOutputSize']), 'v');
            }

            if (isset($data['BACK']['files']) && $data['BACK']['files'] == 'enabled'){
                $text .= Design::erstelleZeile($console, Installation::Get('createImage','filesAmount',self::$langTemplate) , 'e', $content['filesOutputAmount'], 'v');
                $text .= Design::erstelleZeile($console, Installation::Get('createImage','filesSize',self::$langTemplate) , 'e', Design::formatBytes($content['filesOutputSize']), 'v');
            }

            $text .= Design::erstelleZeile($console, Installation::Get('createImage','filePath',self::$langTemplate) , 'e', $content['outputFile'], 'v');
            $text .= Design::erstelleZeile($console, Installation::Get('createImage','fileSize',self::$langTemplate) , 'e', Design::formatBytes($content['outputSize']), 'v');
        }

        if (self::$onEvents['listImage']['enabledInstall']){
            $text .= Design::erstelleZeile($console, Installation::Get('listImage','listImageDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['listImage']['event'][0],Installation::Get('listImage','listImage',self::$langTemplate)), 'h');
        }

        if (isset($result[self::$onEvents['listImage']['name']])){
            $content = $result[self::$onEvents['listImage']['name']]['content'];
            $content['backups'] = array_reverse($content['backups']);
            foreach($content['backups'] as $key => $backup){
                if ($key == 0){
                    $text .= Design::erstelleZeile($console, '','','','' );
                }

                $text .= Design::erstelleZeile($console, Installation::Get('listImage','file',self::$langTemplate) , 'e', $backup['file'], 'v');
                if (isset($backup['size'])){
                    $text .= Design::erstelleZeile($console, Installation::Get('listImage','imageSize',self::$langTemplate) , 'e', Design::formatBytes($backup['size']), 'v');
                }
                if (isset($backup['conf']['date'])){
                    $text .= Design::erstelleZeile($console, Installation::Get('listImage','date',self::$langTemplate) , 'e', date('d.m.Y - H:i:s',$backup['conf']['date']), 'v');
                }
                if (isset($backup['conf']['database']['size'])){
                    $text .= Design::erstelleZeile($console, Installation::Get('listImage','databaseSize',self::$langTemplate) , 'e', Design::formatBytes($backup['conf']['database']['size']), 'v');
                }
                if (isset($backup['conf']['files']['size'])){
                    $text .= Design::erstelleZeile($console, Installation::Get('listImage','filesSize',self::$langTemplate) , 'e', Design::formatBytes($backup['conf']['files']['size']), 'v');
                }

                if (self::$onEvents['installImage']['enabledInstall']){
                    $text .= Design::erstelleZeile($console, Installation::Get('installImage','installImage',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['installImage']['event'][0],Installation::Get('installImage','execute',self::$langTemplate)), 'h');
                }

                if ($key != count($content['backups'])-1){
                    $text .= Design::erstelleZeile($console, '','','','' );
                }
            }

            if (empty($content['backups'])){
                $text .= Design::erstelleZeile($console, '','e',Installation::Get('listImage','noImages',self::$langTemplate),'v_c' );
            }
        }

        if (isset($result[self::$onEvents['installImage']['name']])){
            $text .= Design::erstelleZeile($console, '','e',Installation::Get('installImage','notSupported',self::$langTemplate),'v_c error_light' );
        }

        echo Design::erstelleBlock($console, Installation::Get('main','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function installInstallImage($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        /// ausfüllen
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function installListImage($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();
        $backups = Installation::read_all_files($data['BACK']['path']);
        $res['backups'] = array();
        foreach($backups['files'] as $backup){
            $data = array();
            $data['file'] = $backup;
            $zip = new ZipArchive;
            if ( $zip->open($backup) === TRUE ){
                $conf = $zip->getFromName('backup.conf');
                $conf = json_decode($conf,true);
                $data['conf'] = $conf;
                $data['size'] = filesize($backup);
                $zip->close();
            } else {
                return $res;
            }

            $res['backups'][] = $data;
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function installCreateImage($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();

        $mainPath = realpath($data['PL']['localPath']);
        $mainPath = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $mainPath);
        $location = $data['BACK']['path'];
        Einstellungen::generatepath($location);
        $location = realpath($location);

        $fileName = $data['DB']['db_name'].'_'.date('Ymd_His');
        $sqlFilePath = $location. DIRECTORY_SEPARATOR .'database.sql';

        $definition = array();
        $definition['date'] = time();

        $zip = new ZipArchive;
        if ( $zip->open($location. DIRECTORY_SEPARATOR .$fileName.'.zip',ZIPARCHIVE::CREATE) === TRUE ){
            // Ok
        } else {
            return $res;
        }
        $displayName = $location. DIRECTORY_SEPARATOR .$fileName.'.zip';

        // erzeuge ein Abbild der Datenbank
        if (isset($data['BACK']['database']) && $data['BACK']['database'] == 'enabled'){
            $output = null;
            $return = null;
            $pathOld = getcwd();

            if (@chdir($location)){
                exec('(mysqldump --user '.$data['DB']['db_user_operator'].' --password="'.$data['DB']['db_passwd_operator'].'" --opt --result-file '.$sqlFilePath.' --skip-triggers --no-create-db '.$data['DB']['db_name'].') 2>&1', $output, $return);
                @chdir($pathOld);
            } else {
                $output = Installation::Get('main','errorOnChangeDir',self::$langTemplate, array('path'=>$location));
                $return = 1;
            }

            $res['databaseOutput'] = $output;
            $res['databaseOutputStatus'] = $return;

            $zip->addFile($sqlFilePath,basename($sqlFilePath));
            $definition['database'] = array();
            $definition['database']['file'] = basename($sqlFilePath);
            $definition['database']['size'] = filesize($sqlFilePath);
            $res['databaseOutputSize'] = $definition['database']['size'];
        }

        // erzeuge ein Abbild der Dateien
        if (isset($data['BACK']['files']) && $data['BACK']['files'] == 'enabled'){
            $files = Installation::read_all_files($data['PL']['files']);
            $filesSize = 0;
            foreach($files['files'] as $file){
                $fileBase = substr($file,strlen($data['PL']['files'])+1);
                $filesSize+=filesize($file);
                $zip->addFile($file,'files/'.$fileBase);
            }
            $definition['files'] = array();
            $definition['files']['folder'] = 'files';
            $definition['files']['size'] = $filesSize;
            $definition['files']['amount'] = count($files['files']);
            $res['filesOutputSize'] = $filesSize;
            $res['filesOutputAmount'] = count($files['files']);
        }

        $zip->addFromString('backup.conf',json_encode($definition));

        $zip->close();
        @unlink( $sqlFilePath );

        $res['outputFile'] = $displayName;
        $res['outputSize'] = filesize($displayName);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion BackupSegment