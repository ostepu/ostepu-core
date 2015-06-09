<?php
#region PlattformEinrichten
class PlattformEinrichten
{
    private static $initialized=false;
    public static $name = 'initPlatform';
    public static $installed = false;
    public static $page = 4;
    public static $rank = 50;
    public static $enabledShow = true;
    
    public static $onEvents = array('install'=>array('name'=>'initPlatform','event'=>array('actionInstallPlatform','install', 'update')));
    
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['pl_details'], 'data[PL][pl_details]', null,true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text='';
        $text .= Design::erstelleBeschreibung($console,Language::Get('platform','description'));    

        if (!$console){
            $text .= Design::erstelleZeile($console, Language::Get('platform','createTables'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            $text .= Design::erstelleZeile($console, Language::Get('platform','details'), 'e', Design::erstelleAuswahl($console, $data['PL']['pl_details'], 'data[PL][pl_details]', 'details', null), 'v');
        }
        
        if (isset($result[self::$onEvents['install']['name']]) && $result[self::$onEvents['install']['name']]!=null){
           $result =  $result[self::$onEvents['install']['name']];
        } else 
            $result = array('content'=>null,'fail'=>false,'errno'=>null,'error'=>null);
        
        $fail = $result['fail'];
        $error = $result['error'];
        $errno = $result['errno'];
        $content = $result['content'];
        
        if (self::$installed){
            if (!$console && isset($data['PL']['pl_details']) && $data['PL']['pl_details'] === 'details'){
                foreach ($content as $component => $dat){
                    $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Language::Get('main','ok') : "<font color='red'>".Language::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                }
            } else {
                $text .= Design::erstelleZeile($console, Language::Get('platform','countComponents'), 'e', count($content), 'v_c');
            }
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Language::Get('platform','title'), $text);
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        $res = array();
    
        if (!$fail){
            // die /platform Befehle auslösen
            $list = array('DB/DBApprovalCondition','DB/DBAttachment','DB/DBCourse','DB/DBCourseStatus','DB/DBExercise','DB/DBExerciseFileType','DB/DBExerciseSheet','DB/DBExerciseType','DB/DBExternalId','DB/DBFile','DB/DBGroup','DB/DBInvitation','DB/DBMarking','DB/DBSelectedSubmission','DB/DBSession','DB/DBSubmission','DB/DBUser','FS/FSFile','FS/FSCsv','FS/FSPdf','FS/FSZip','FS/FSBinder','logic/LTutor');
            
            $platform = Installation::PlattformZusammenstellen($data);
            
            $multiRequestHandle = new Request_MultiRequest();

            for ($i=0;$i<count($list);$i++){
                $url = $list[$i];//$data['PL']['init'];
                // inits all components
                $handler = Request_CreateRequest::createPost($data['PL']['url'].'/'.$url. '/platform',array(),Platform::encodePlatform($platform));
                $multiRequestHandle->addRequest($handler);
            }
            
            $answer = $multiRequestHandle->run();
            
            for ($i=0;$i<count($list);$i++){
                $url = $list[$i];            
                $result = $answer[$i];
                $res[$url] = array();
                if (isset($result['content']) && isset($result['status']) && $result['status'] === 201){
                    $res[$url]['status'] = 201;
                } else {
                    $res[$url]['status'] = 409;
                    $fail = true;
                    if (isset($result['status'])){
                        $errno = $result['status'];
                        $res[$url]['status'] = $result['status'];
                    }
                }
            }
        }
        
        return $res;
    }
}
#endregion PlattformEinrichten