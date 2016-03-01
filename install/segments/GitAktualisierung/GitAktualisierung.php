<?php
/**
 * @file GitAktualisierung.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 */

#region GitAktualisierung
class GitAktualisierung
{
    public static $name = 'GitAktualisierung';
    public static $installed = false;
    public static $enabledShow = true;
    public static $rank = 25;
    public static $page = 0;
    private static $initialized=false;
    private static $langTemplate='GitAktualisierung';

    public static $onEvents = array('collect'=>array('procedure'=>'collect','name'=>'collectGitUpdates','event'=>array('actionCollectGitUpdates')),
                                    'install'=>array('procedure'=>'install','name'=>'installGitUpdates','event'=>array('actionInstallGitUpdates')));

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $text='';
        if (!$console)
            $text .= Design::erstelleBeschreibung($console,Installation::Get('gitUpdate','description',self::$langTemplate));

        $collected = array();
        if (isset($result[self::$onEvents['collect']['name']]) && $result[self::$onEvents['collect']['name']]!=null){
           $collected =  $result[self::$onEvents['collect']['name']];
        } elseif (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']]!=null){
           $collected =  $result[self::$onEvents['install']['name']];
        } else
           $collected = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);

        $fail = $collected['fail'];
        $error = $collected['error'];
        $errno = $collected['errno'];

        if (Einstellungen::$accessAllowed){
            //if ($collected['content'] === null){
                if (!$console){
                    $text .= Design::erstelleZeileShort($console, Installation::Get('gitUpdate','collectGitUpdatesDesc',self::$langTemplate), 'e', Design::erstelleSubmitButton(self::$onEvents['collect']['event'][0], Installation::Get('gitUpdate','collectGitUpdates',self::$langTemplate)), 'h');
                }
            //}


            if (isset($collected['content']['modified']) && $collected['content']['modified'] !== null){
                $t = '';
                if (isset($collected['content']['modified'][0])){
                    $t = $collected['content']['modified'][0];
                } else {
                    $t = Installation::Get('gitUpdate','noUpdates',self::$langTemplate);
                }

                if (!$console){
                    $text .= "<tr><td class='v' colspan='3'>{$t}</td></tr>";
                } else  {

                }
            }

            if (isset($collected['content']['commits']) && $collected['content']['commits'] !== null){
                for($i=0;$i<20 && $i<count($collected['content']['commits']);$i++){
                    if (!$console){
                        $text .= "<tr><td class='v' colspan='2'>{$collected['content']['commits'][$i]['desc']}</td><td class='e'>{$collected['content']['commits'][$i]['period']}</td></tr>";
                    } else  {

                    }
                }
                if (count($collected['content']['commits'])>20){
                    if (!$console){
                        $text .= Design::erstelleZeile($console, Installation::Get('gitUpdate','additionalCommits',self::$langTemplate, array('additionalCommits'=>count($collected['content']['commits'])-20)), 'v');
                    } else  {

                    }
                }

                if (count($collected['content']['commits'])>0){
                    if (!$console){
                        $text .= Design::erstelleZeileShort($console, Installation::Get('gitUpdate','installGitUpdatesDesc',self::$langTemplate), 'e', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0], Installation::Get('gitUpdate','installGitUpdates',self::$langTemplate)), 'h');
                    }
                }
            }

            if (self::$installed){
                $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error, Installation::Get('gitUpdate','executeGitUpdatesDesc',self::$langTemplate));
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('gitUpdate','title',self::$langTemplate), $text);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function checkExecutability($data)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array(['name'=>'git','exec'=>'git --version','desc'=>'git --version']);
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    public static function collect($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $result = array('commits'=>null, 'modified'=>null);
        $pathOld = getcwd();
        $output=null;

        Installation::log(array('text'=>Installation::Get('gitUpdate','execGitFetch',self::$langTemplate)));
        if (@chdir($data['PL']['localPath'] . DIRECTORY_SEPARATOR)){
            exec('(git fetch -p) 2>&1', $output, $return);
            @chdir($pathOld);
        } else {
            $return = 1;
            $output = Installation::Get('gitUpdate','errorOnChangeDir',self::$langTemplate, array('path'=>data['PL']['localPath'] . DIRECTORY_SEPARATOR));
        }

        if ($return == 0){
            $pathOld = getcwd();
            $output=null;

            Installation::log(array('text'=>Installation::Get('gitUpdate','execGitDiff',self::$langTemplate)));
            if (@chdir($data['PL']['localPath'] . DIRECTORY_SEPARATOR)){
                exec('(git diff --shortstat HEAD...FETCH_HEAD) 2>&1', $output, $return);
                @chdir($pathOld);
            } else {
                $return = 1;
                $output = Installation::Get('gitUpdate','errorOnChangeDir',self::$langTemplate, array('path'=>data['PL']['localPath'] . DIRECTORY_SEPARATOR));
            }

            if ($return == 0){
                $result['modified'] = $output;

                $pathOld = getcwd();
                $output=null;

                Installation::log(array('text'=>Installation::Get('gitUpdate','execGitLog',self::$langTemplate)));
                if (@chdir($data['PL']['localPath'] . DIRECTORY_SEPARATOR)){
                    exec('(git log --pretty=format:\'%s,%cr\' --abbrev-commit --date=relative HEAD...FETCH_HEAD) 2>&1', $output, $return);
                    @chdir($pathOld);
                } else {
                    $return = 1;
                    $output = Installation::Get('gitUpdate','errorOnChangeDir',self::$langTemplate, array('path'=>data['PL']['localPath'] . DIRECTORY_SEPARATOR));
                }

                if ($return == 0){
                    $result['commits'] = array();
                    foreach($output as $out){
                        $period = substr(strrchr ( $out , ',' ),1);
                        $period = trim($period, "'");
                        $description = substr( $out, 0,strlen($out) - strlen($period) - 1 );
                        $description = trim($description, "'");
                        $element = array('desc'=>$description, 'period'=>$period);
                        $result['commits'][] = $element;
                    }

                    if (empty($result['commits'])){
                        Installation::log(array('text'=>Installation::Get('gitUpdate','noChangesFound',self::$langTemplate)));
                    } else {
                        Installation::log(array('text'=>Installation::Get('gitUpdate','changesFound',self::$langTemplate,array('amount'=>count($result['commits'])))));
                    }
                } else {
                    $fail = true;
                    $error = Installation::Get('gitUpdate','errorGitLog',self::$langTemplate);
                    Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERROR));
                }
            } else {
                $fail = true;
                $error = Installation::Get('gitUpdate','errorGitDiff',self::$langTemplate);
                Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERROR));
            }

        } else {
            $fail = true;
            $error = Installation::Get('gitUpdate','errorGitFetch',self::$langTemplate);
            Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERROR));
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $result;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $result = array();
        $pathOld = getcwd();
        $output=null;

        Installation::log(array('text'=>Installation::Get('gitUpdate','execGitReset',self::$langTemplate)));
        if (@chdir($data['PL']['localPath'] . DIRECTORY_SEPARATOR)){
            exec('(git reset --hard) 2>&1', $output, $return);
            @chdir($pathOld);
        } else {
            $return = 1;
            $output = Installation::Get('gitUpdate','errorOnChangeDir',self::$langTemplate, array('path'=>data['PL']['localPath'] . DIRECTORY_SEPARATOR));
        }

        if ($return == 0){
            $pathOld = getcwd();
            $output=null;

            Installation::log(array('text'=>Installation::Get('gitUpdate','execGitPull',self::$langTemplate)));
            if (@chdir($data['PL']['localPath'] . DIRECTORY_SEPARATOR)){
                exec('(git pull) 2>&1', $output, $return);
                @chdir($pathOld);
            } else {
                $return = 1;
                $output = Installation::Get('gitUpdate','errorOnChangeDir',self::$langTemplate, array('path'=>data['PL']['localPath'] . DIRECTORY_SEPARATOR));
            }

            if ($return == 0){
                // OK
            } else {
                $fail = true;
                $error = Installation::Get('gitUpdate','errorGitPull',self::$langTemplate);
                Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERROR));
            }

        } else {
            $fail = true;
            $error = Installation::Get('gitUpdate','errorGitReset',self::$langTemplate);
            Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERROR));
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $result;
    }
}
#endregion GitAktualisierung