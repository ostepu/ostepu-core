<?php
#region GrundeinstellungenAusgeben
class GrundeinstellungenAusgeben
{
    private static $initialized=false;
    public static $name = 'installInit';
    public static $installed = false;
    public static $page = 2;
    public static $rank = 75;
    public static $enabledShow = true;
    
    public static $onEvents = array('install'=>array('name'=>'installInit','event'=>array('actionInstallInit','install','update')));
    
    public static function getDefaults()
    {
        return array(
                     'db_ignore' => array('data[DB][db_ignore]', null),
                     'db_override' => array('data[DB][db_override]', null),
                     'pl_main_details' => array('data[PL][pl_main_details]', null)
                     );
    }
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $def = self::getDefaults();
        
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_ignore'], 'data[DB][db_ignore]', $def['db_ignore'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['DB']['db_override'], 'data[DB][db_override]', $def['db_override'][1], true);
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', $def['pl_main_details'][1], true);
        echo $text;
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $text = '';
        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Language::Get('general_settings','description'));   
            
            $text .= Design::erstelleZeile($console, Language::Get('general_settings','init'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallInit'), 'h');
            $text .= Design::erstelleZeile($console, Language::Get('database','db_override'), 'e', Design::erstelleAuswahl($console, $data['DB']['db_override'], 'data[DB][db_override]', 'override', null, true), 'v');
            $text .= Design::erstelleZeile($console, Language::Get('database','db_ignore'), 'e', Design::erstelleAuswahl($console, $data['DB']['db_ignore'], 'data[DB][db_ignore]', 'ignore', null, true), 'v');

            $text .= Design::erstelleZeile($console, Language::Get('general_settings','details'), 'e', Design::erstelleAuswahl($console, $data['PL']['pl_main_details'], 'data[PL][pl_main_details]', 'details', null, true), 'v');
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
            if ($data['PL']['pl_main_details'] == 'details'){
                foreach ($content as $component => $dat){
                    if (!$console){
                        $text .= "<tr><td class='e' rowspan='1'>{$component}</td><td class='v'></td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Language::Get('main','ok') : "<font color='red'>".Language::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
                    } else 
                        $text .= "{$component}: ".((isset($dat['status']) && $dat['status']===201) ? Language::Get('main','ok') : Language::Get('main','fail')." ({$dat['status']})");
                }
            }
            
            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Language::Get('general_settings','title'), $text);
        return null;
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        // Datenbank einrichten
        if (!isset($data['action']) || $data['action']!='update'){
            if (!$fail && (isset($data['DB']['db_override']) && $data['DB']['db_override'] === 'override')){
               $sql = "DROP SCHEMA IF EXISTS `".$data['DB']['db_name']."`;";
               $oldName = $data['DB']['db_name'];
               $data['DB']['db_name'] = null;
               $result = DBRequest::request($sql, false, $data);
               if ($result["errno"] !== 0){
                    $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
               }
               $data['DB']['db_name'] = $oldName;
            }
        }
       
        if (!$fail){ 
            $add = (((isset($data['DB']['db_ignore']) && $data['DB']['db_ignore'] === 'ignore') || (isset($data['action']) && $data['action']=='update')) ? 'IF NOT EXISTS ' : '');
            $sql = "CREATE SCHEMA {$add}`".$data['DB']['db_name']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;";
            $oldName = $data['DB']['db_name'];
            $data['DB']['db_name'] = null;
            $result = DBRequest::request($sql, false, $data);
            if ($result["errno"] !== 0){
                $fail = true; $errno = $result["errno"];$error = isset($result["error"]) ? $result["error"] : '';
            }
            $data['DB']['db_name'] = $oldName;
        }
        
        
        // CControl+DBQuery2 einrichten
        $res = array();
        if (!$fail){
            $list = array('DB/CControl','DB/DBQuery2');
            $platform = Installation::PlattformZusammenstellen($data);

            for ($i=0;$i<count($list);$i++){
                $url = $list[$i];//$data['PL']['init'];
                // inits all components
                $result = Request::post($data['PL']['url'].'/'.$url. '/platform',array(),Platform::encodePlatform($platform));

                $res[$url] = array();
                if (isset($result['content']) && isset($result['status']) && $result['status'] === 201){
                    $res[$url]['status'] = 201;
                } else {
                    $res[$url]['status'] = 409;
                    $fail = true;
                    if (isset($result['status'])){
                        $errno = $result['status'];
                        $res[$url]['status'] = $result['status'];
                    };
                    ///if (isset($result['content'])) echo $result['content'];
                }
            }
        }
        
        return $res;
    }
}
#endregion Grundeinstellungen_ausgeben