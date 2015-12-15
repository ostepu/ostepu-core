<?php
#region GitAktualisierung
class GitAktualisierung
{
    public static $name = 'GitAktualisierung';
    public static $installed = false;
    public static $enabledShow = true;
    public static $rank = 25;
    public static $page = 0;
    private static $initialized=false;

    public static $onEvents = array('collect'=>array('procedure'=>'collect','name'=>'collectGitUpdates','event'=>array('actionCollectGitUpdates')),
                                    'install'=>array('procedure'=>'install','name'=>'installGitUpdates','event'=>array('actionInstallGitUpdates')));

    public static function show($console, $result, $data)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $text='';
        if (!$console)
            $text .= Design::erstelleBeschreibung($console,Language::Get('gitUpdate','description'));

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
                    $text .= Design::erstelleZeileShort($console, Language::Get('gitUpdate','collectGitUpdatesDesc'), 'e', Design::erstelleSubmitButton(self::$onEvents['collect']['event'][0], Language::Get('gitUpdate','collectGitUpdates')), 'h');
                }
            //}


            if (isset($collected['content']['modified']) && $collected['content']['modified'] !== null){
                $t = '';
                if (isset($collected['content']['modified'][0])){
                    $t = $collected['content']['modified'][0];
                } else {
                    $t = Language::Get('gitUpdate','noUpdates');
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
                        $text .= Design::erstelleZeile($console, Language::Get('gitUpdate','additionalCommits',array('additionalCommits'=>count($collected['content']['commits'])-20)), 'v');
                    } else  {

                    }
                }

                if (count($collected['content']['commits'])>0){
                    if (!$console){
                        $text .= Design::erstelleZeileShort($console, Language::Get('gitUpdate','installGitUpdatesDesc'), 'e', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0], Language::Get('gitUpdate','installGitUpdates')), 'h');
                    }
                }
            }

            if (self::$installed){
                $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error, Language::Get('gitUpdate','executeGitUpdatesDesc'));
            }
        }

        echo Design::erstelleBlock($console, Language::Get('gitUpdate','title'), $text);
        Installation::log(array('text'=>'beende Funktion'));
        return null;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        self::$initialized = true;
        Installation::log(array('text'=>'beende Funktion'));
    }

    public static function collect($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $result = array('commits'=>null, 'modified'=>null);
        $pathOld = getcwd();
        $output=null;

        Installation::log(array('text'=>'exec git fetch'));
        chdir(dirname(__FILE__).'/../../');
        exec('(git fetch) 2>&1', $output, $return);
        chdir($pathOld);

        if ($return == 0){
            $pathOld = getcwd();
            $output=null;

            Installation::log(array('text'=>'exec git diff --shortstat HEAD...FETCH_HEAD'));
            chdir(dirname(__FILE__).'/../../');
            exec('(git diff --shortstat HEAD...FETCH_HEAD) 2>&1', $output, $return);
            chdir($pathOld);

            if ($return == 0){
                $result['modified'] = $output;

                $pathOld = getcwd();
                $output=null;

                Installation::log(array('text'=>'exec git log --pretty=format:\'%s,%cr\' --abbrev-commit --date=relative HEAD...FETCH_HEAD'));
                chdir(dirname(__FILE__).'/../../');
                exec('(git log --pretty=format:\'%s,%cr\' --abbrev-commit --date=relative HEAD...FETCH_HEAD) 2>&1', $output, $return);
                chdir($pathOld);

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
                        Installation::log(array('text'=>'keine Änderungen gefunden'));
                    } else {
                        Installation::log(array('text'=>'Änderungen gefunden ('.count($result['commits']).')'));
                    }
                } else {
                    $fail = true;
                    $error = Language::Get('gitUpdate','errorGitLog');
                    Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERRROR));
                }
            } else {
                $fail = true;
                $error = Language::Get('gitUpdate','errorGitDiff');
                Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERRROR));
            }

        } else {
            $fail = true;
            $error = Language::Get('gitUpdate','errorGitFetch');
            Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERRROR));
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $result;
    }

    public static function install($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>'starte Funktion'));
        $result = array();
        $pathOld = getcwd();
        $output=null;

        Installation::log(array('text'=>'exec git reset --hard'));
        chdir(dirname(__FILE__).'/../../');
        exec('(git reset --hard) 2>&1', $output, $return);
        chdir($pathOld);

        if ($return == 0){
            $pathOld = getcwd();
            $output=null;

            Installation::log(array('text'=>'exec git pull'));
            chdir(dirname(__FILE__).'/../../');
            exec('(git pull) 2>&1', $output, $return);
            chdir($pathOld);

            if ($return == 0){
                // OK
            } else {
                $fail = true;
                $error = Language::Get('gitUpdate','errorGitPull');
                Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERRROR));
            }

        } else {
            $fail = true;
            $error = Language::Get('gitUpdate','errorGitReset');
            Installation::log(array('text'=>$error, 'logLevel'=>LogLevel::ERRROR));
        }

        Installation::log(array('text'=>'beende Funktion'));
        return $result;
    }
}
#endregion GitAktualisierung