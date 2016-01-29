<?php
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
                                        )
                                    );

    public static function getDefaults()
    {
        $res = array(
                     'path' => array('data[BACK][path]', '/var/www/backup')
                     );
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
        if (self::$onEvents['createImage']['enabledInstall']){
            $text .= Design::erstelleZeile($console, Installation::Get('createImage','createImageDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['createImage']['event'][0],Installation::Get('createImage','createImage',self::$langTemplate)), 'h');
        }
        
        $createBackup=false;
        if (isset($result[self::$onEvents['createImage']['name']])){
            $content = $result[self::$onEvents['createImage']['name']]['content'];
            $createBackup=true;
            if (!empty($content['output'])){
                $text .= Design::erstelleZeile($console, Installation::Get('createImage','message',self::$langTemplate) , 'e', $content['output'], 'v error_light break');
            }
            $text .= Design::erstelleZeile($console, Installation::Get('createImage','status',self::$langTemplate) , 'e', ($content['outputStatus'] == 0 ? Installation::Get('main','ok') : Installation::Get('main','fail')), 'v_c');
            $text .= Design::erstelleZeile($console, Installation::Get('createImage','filePath',self::$langTemplate) , 'e', $content['file'], 'v');
        }

        echo Design::erstelleBlock($console, Installation::Get('main','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function installCreateImage($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();
        
        $mainPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..');
        $mainPath = str_replace(array("\\","/"), array(DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR), $mainPath);
        $location = $data['BACK']['path'];
        Einstellungen::generatepath($location);
        $location = realpath($location);
        
        $fileName = $data['DB']['db_name'].'_'.date('Ymd_His').'.sql';
        $filePath = $location. DIRECTORY_SEPARATOR .$fileName;
        
        $output = null;
        $return = null;
        $pathOld = getcwd();
        chdir($location);                            
        exec('(mysqldump --user '.$data['DB']['db_user_operator'].' --password="'.$data['DB']['db_passwd_operator'].'" --opt --result-file '.$filePath.' --skip-triggers --no-create-db '.$data['DB']['db_name'].') 2>&1', $output, $return);  
        chdir($pathOld);
        
        $res['file'] = $filePath;
        $res['output'] = $output;
        $res['outputStatus'] = $return;
        
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion BackupSegment