<?php
#region SyntaxPruefen
class SyntaxPruefen
{
    private static $initialized=false;
    public static $name = 'validateFiles';
    public static $installed = false;
    public static $page = 8;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate='SyntaxPruefen';

    public static $onEvents = array(
                                    'validateFiles'=>array(
                                        'name'=>'validateFiles',
                                        'event'=>array('actionValidateFiles'),
                                        'procedure'=>'installValidateFiles',
                                        'enabledInstall'=>true
                                        )
                                    );

    public static function getDefaults()
    {
        $res = array();
        return $res;
    }

    public static function checkExecutability($data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array(['name'=>'php','exec'=>'php -v','desc'=>'php -v']);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults();
        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('validation','description',self::$langTemplate));

        if (self::$onEvents['validateFiles']['enabledInstall'])
            $text .= Design::erstelleZeile($console, Installation::Get('validation','validateFilesDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['validateFiles']['event'][0],Installation::Get('validation','validateFiles',self::$langTemplate)), 'h');

        $validateFiles=false;
        if (isset($result[self::$onEvents['validateFiles']['name']])){
            $validateFiles=true;
        }

        if ($validateFiles){
            $res = $result[self::$onEvents['validateFiles']['name']]['content'];
            foreach($res['plugins'] as $plug){
                $text .= Design::erstelleZeile($console, $plug['nameText'], 'e');
                $text .= Design::erstelleZeile($console, Installation::Get('validation','files',self::$langTemplate), 'e', $plug['filesAmount'] ,'v');
                foreach($plug['results'] as $file){
                    if ($file[1] === 'json'){
                        $text .= Design::erstelleZeile($console, $file[0] , 'break v', Installation::Get('validation','jsonInvalid',self::$langTemplate), 'v error_light break');
                    } elseif ($file[1] === 'php'){
                        $text .= Design::erstelleZeile($console, $file[0], 'break v', Installation::Get('validation','phpInvalid',self::$langTemplate).'<br>'.implode('<br>',$file[2]), 'v error_light break');
                    } elseif ($file[1] === 'ini'){
                        $text .= Design::erstelleZeile($console, $file[0] , 'break v', Installation::Get('validation','iniInvalid',self::$langTemplate), 'v error_light break');
                    }
                }
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('validation','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function installValidateFiles($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();
        $pluginFiles = PlugInsInstallieren::getSelectedPluginFiles($data);
        $res['plugins'] = array();

        // hier die möglichen Erweiterungen ausgeben, zudem noch die Daten dieser Erweiterungen
        foreach ($pluginFiles as $plug){
            $dat = PlugInsInstallieren::gibPluginInhalt($data,$plug);
            $name = isset($dat['name']) ? $dat['name'] : '???';
            
            $res['plugins'][$plug] = array();
            $res['plugins'][$plug]['results'] = array();
            
            $version = isset($dat['version']) ? $dat['version'] : null;

            $versionText = isset($version) ? ' v'.$version : '';
            $res['plugins'][$plug]['nameText'] = $name.$versionText;

            $fileCount=0;
            $fileSize=0;
            $componentCount=0;
            
            $fileList = array();
            $fileListAddress = array();
            $componentFiles = array();
            PlugInsInstallieren::gibPluginDateien($dat, $fileList, $fileListAddress, $componentFiles);
            unset($fileListAddress);
            unset($componentFiles);
            $res['plugins'][$plug]['filesAmount'] = count($fileList);

            foreach($fileList as $f){
                if (is_readable($f)){
                    $fileSize = filesize($f);
                    if ($fileSize>0 && strtolower(substr($f,-5))==='.json'){
                        // validiere die json Datei
                        $cont = file_get_contents($f);
                        if (trim($cont) != ''){
                            $val = @json_decode(file_get_contents($f));
                            if ($val===null){
                                $res['plugins'][$plug]['results'][] = array(realpath($f),'json');
                            }
                        }
                    }

                    if ($fileSize>0 && strtolower(substr($f,-4))==='.php'){
                        // validiere die php Datei
                        $output=null;
                        $result=null;
                        exec('(php -l -d error_reporting=E_ALL -d display_errors=on -d log_errors=off -f '.realpath($f).') 2>&1',$output,$result);
                        if ($result!=0){
                                $res['plugins'][$plug]['results'][] = array(realpath($f),'php',$output);
                        }
                    }

                    if ($fileSize>0 && strtolower(substr($f,-4))==='.ini'){
                        // validiere die ini Datei
                        $cont = file_get_contents($f);
                        $val = @parse_ini_file($f);
                        if ($val===false){
                                $res['plugins'][$plug]['results'][] = array(realpath($f),'ini');
                        }
                    }
                }
            }
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }
}
#endregion SyntaxPruefen