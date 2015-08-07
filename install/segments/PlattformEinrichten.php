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
    
    public static function getDefaults()
    {
        return array(
                     'pl_details' => array('data[PL][pl_details]', null)
                     );
    }
        
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $def = self::getDefaults();
        
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['pl_details'], 'data[PL][pl_details]', $def['pl_details'][1],true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $isUpdate = (isset($data['action']) && $data['action']=='update') ? true : false;
        
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
            if (!$console && isset($data['PL']['pl_details']) && $data['PL']['pl_details'] === 'details' && !$isUpdate){
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
            $list = Einstellungen::getLinks('postPlatform');
            $platform = Installation::PlattformZusammenstellen($data);
            
            $multiRequestHandle = new Request_MultiRequest();

            for ($i=0;$i<count($list);$i++){
                // inits all components
                $handler = Request_CreateRequest::createPost($list[$i]->getAddress(). '/platform',array(),Platform::encodePlatform($platform));
                $multiRequestHandle->addRequest($handler);
            }
            
            $answer = $multiRequestHandle->run();
            
            for ($i=0;$i<count($list);$i++){
                $url = $list[$i]->getTargetName();            
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