<?php
#region Komponenten
class Komponenten
{
    private static $initialized=false;
    public static $name = 'initComponents';
    public static $installed = false;
    public static $page = 3;
    public static $rank = 75;
    public static $enabledShow = true;
    public static $enabledInstall = true;
    
    public static $onEvents = array('install'=>array('name'=>'initComponents','event'=>array('actionInitComponents','install', 'update')));
    
    public static function getDefaults()
    {
        return array(
                     'co_details' => array('data[CO][co_details]', null)
                     );
    }
    
    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        $def = self::getDefaults();
        
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['CO']['co_details'], 'data[CO][co_details]', $def['co_details'][1],true);
        echo $text; 
        self::$initialized = true;
    }
    
    public static function show($console, $result, $data)
    {
        $isUpdate = (isset($data['action']) && $data['action']=='update') ? true : false;
        
        $text='';
        if (!$console){
            $text .= Design::erstelleBeschreibung($console,Language::Get('components','description'));
            
            $text .= Design::erstelleZeile($console, Language::Get('components','init'), 'e', '', 'v', Design::erstelleSubmitButton(self::$onEvents['install']['event'][0]), 'h');
            $text .= Design::erstelleZeile($console, Language::Get('components','details'), 'e', Design::erstelleAuswahl($console, $data['CO']['co_details'], 'data[CO][co_details]', 'details', null, true), 'v');
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
            // counts installed commands
            $installedCommands = 0;
            
            // counts installed components
            $installedComponents = 0;
            
            // counts installed links
            $installedLinks = 0;

            foreach($content as $componentName => &$component)
            {
                if (isset($component['init']))
                    $component['init'] = Component::decodeComponent(json_encode($component['init']));
                    
                if (isset($component['links']))
                    $component['links'] = Link::decodeLink(json_encode($component['links']));
                    
                if (isset($component['commands'])){
                    $router = new \Slim\Router();
                    foreach($component['commands'] as $command){
                        $route = new \Slim\Route($command['path'],'is_array');
                        $route->via(strtoupper($command['method']));
                        $router->map($route);
                    }
                    $component['router'] = $router;
                }
            }
            
            foreach($content as $componentName => $component)
            {
                $linkNames = array();
                $linkNamesUnique = array();
                $callNames = array();
                
                $links = array();
                if (isset($component['links']))
                    $links = $component['links'];
                foreach($links as $link){
                    $linkNames[] = $link->getName();
                    $linkNamesUnique[$link->getName()] = $link->getName();
                    
                }
                
                $calls=null;
                if (isset($component['call']))
                    $calls = $component['call'];
                if ($calls!==null){
                    foreach($calls as $pos => $callList){
                        if (isset($callList['name']))
                            $callNames[$callList['name']] = $callList['name'];
                    }
                }
                                    
                $countLinks = 1;
                if (isset($component['init']) && $component['init']->getStatus() === 201){
                    $countLinks+=count($linkNames) + count(array_diff($callNames,$linkNamesUnique)) + count($linkNamesUnique) - count(array_diff($linkNamesUnique,$callNames));
                    $countLinks++;
                }
                
                $countCommands = count(isset($component['commands']) ? $component['commands'] : array());
                if (isset($component['init']) && isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate)
                    $text .= "<tr><td class='e' rowspan='{$countLinks}'>{$componentName}</td><td class='v'>{$component['init']->getAddress()}</td><td class='e'><div align ='center'>".($component['init']->getStatus() === 201 ? Language::Get('main','ok') : "<font color='red'>".Language::Get('main','fail')." ({$component['init']->getStatus()})</font>")."</align></td></tr>";
                
                if (isset($component['init']) && $component['init']->getStatus() === 201){
                    $installedComponents++;
                    $installedLinks+=count(isset($component['links']) ? $component['links'] : array());
                    $installedCommands+=$countCommands;
                    
                    if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate)
                        $text .= "<tr><td class='v' colspan='2'>".Language::Get('components','installedCalls').": {$countCommands}</td></tr>";
                
                    $links = array();
                    if (isset($component['links']))
                        $links = $component['links'];
                    $lastLink = null;
                    foreach($links as $link){
                        $calls = null;
                        if (isset($component['call']))
                            $calls = $component['call'];
                        $linkFound=false;
                        if ($calls!==null){
                            foreach($calls as $pos => $callList){
                                if (isset($callList['name']) && $callList['name'] === $link->getName()){
                                    $linkFound=true;
                                    break;
                                }
                            }
                        }

                        if ($lastLink!=$link->getName() && $linkFound){
                            $calls = null;
                            if (isset($component['call']))
                                $calls = $component['call'];

                            $notRoutable = false;
                            if ($calls!==null){
                                foreach($calls as $pos => $callList){                
                                    if ($link->getName() !== $callList['name']) continue;
                                    foreach($callList['links'] as $pos2 => $call){
                                        if (!isset($content[$link->getTargetName()]['router'])){
                                            $notRoutable=true;
                                            break;
                                        }
                                        if ($content[$link->getTargetName()]['router']==null) continue;
                                        if ($call===null) continue;
                                        if (!isset($call['method'])) continue;
                                        if (!isset($call['path'])) continue;
                                        
                                        $routes = count($content[$link->getTargetName()]['router']->getMatchedRoutes(strtoupper($call['method']), $call['path']),true);
                                        if ($routes===0){
                                            $notRoutable=true;
                                            break;
                                        }
                                    }
                                    if ($notRoutable) break;
                                }
                                
                                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate)
                                    $text .= "<tr><td class='v'>{$link->getName()}</td><td class='e'><div align ='center'>".(!$notRoutable ? Language::Get('main','ok') : '<font color="red">'.Language::Get('components','notRoutable').'</font>')."</align></td></tr>";
                            }
                        }
                        
                        if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate)
                            $text .= "<tr><td class='v'>{$link->getName()}".(!$linkFound ? " (<font color='red'>".Language::Get('components','unknown')."</font>)" : '')."</td><td class='v'>{$link->getTargetName()}</td></tr>"; 
                    
                        $lastLink = $link->getName();
                    }
                    
                    // fehlende links
                    $calls = null;
                    if (isset($component['call']))
                        $calls = $component['call'];
                    if ($calls!==null){
                        foreach($calls as $pos => $callList){    
                            $found = false;
                            foreach($links as $link){                    
                                if ($link->getName() == $callList['name']){
                                    $found=true;
                                    break;
                                }
                            }
                            if (!$found){
                                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate)
                                    $text .= "<tr><td class='v'>{$callList['name']}</td><td class='e'><font color='red'>".Language::Get('components','unallocated')."</font></td></tr>";
                            }
                        }
                    }
                }
            }
            
            if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details' && !$isUpdate)
                $text .= Design::erstelleZeile($console, '', '', '', '', '' , '');
            
            $text .= Design::erstelleZeile($console, Language::Get('components','installedComponents'), 'e', '', 'v', $installedComponents, 'v');
            $text .= Design::erstelleZeile($console, Language::Get('components','installedLinks'), 'e', '', 'v', $installedLinks, 'v');
            $text .= Design::erstelleZeile($console, Language::Get('components','installedCommands'), 'e', '', 'v',$installedCommands, 'v');

            $text .= Design::erstelleInstallationszeile($console, $fail, $errno, $error); 
        }
        
        echo Design::erstelleBlock($console, Language::Get('components','title'), $text);
    }
    
    public static function install($data, &$fail, &$errno, &$error)
    {
        $fail = false;
        $url = $data['PL']['init'];
        $components = array();
       
        // inits all components
        $result = Request::get($data['PL']['url'].'/'.$url. '/definition/send',array(),'');
        //echo $result['content'];
        if (isset($result['content']) && isset($result['status'])){

            // component routers
            $router = array();

            $results = Component::decodeComponent($result['content']);
            $results = Installation::orderBy(json_decode(Component::encodeComponent($results),true),'name',SORT_ASC);
            $results = Component::decodeComponent(Component::encodeComponent($results));
            if (!is_array($results)) $results = array($results);
            
            foreach($results as $res){
                $components[$res->getName()] = array();
                $components[$res->getName()]['init'] = $res;
            }

            // get component definitions from database
            $result4 = Request::get($data['PL']['url'].'/'.$url. '/definition',array(),'');
            
            if (isset($result4['content']) && isset($result4['status']) && $result4['status'] === 200){
                $definitions = Component::decodeComponent($result4['content']);
                if (!is_array($definitions)) $definitions = array($definitions);
                
                $result2 = new Request_MultiRequest();
                $result3 = new Request_MultiRequest();
                $tempDef = array();
                foreach ($definitions as $definition){
                    if (strpos($definition->getAddress().'/', $data['PL']['urlExtern'].'/')===false) {continue;}
                    
                    $components[$definition->getName()]['definition'] = $definition;
                    $tempDef[] = $definition;      
                    $request = Request_CreateRequest::createGet($definition->getAddress().'/info/commands',array(),'');
                    $result2->addRequest($request);
                    $request = Request_CreateRequest::createGet($definition->getAddress().'/info/links',array(),'');
                    $result3->addRequest($request);
                }
                $definitions = $tempDef;
                
                $result2 = $result2->run();
                $result3 = $result3->run();
            
                foreach($results as $res){
                    if ($res===null){
                        $fail = true;
                        continue;
                    }

                    $countLinks = 0;
                    $resultCounter=-1;
                    foreach ($definitions as $definition){
                        //if (strpos($definition->getAddress().'/', $data['PL']['urlExtern'].'/')===false) continue;
                        
                        $resultCounter++;
                        if ($definition->getId() === $res->getId()){
                        
                            $links = $definition->getLinks();
                            $links = Installation::orderBy(json_decode(Link::encodeLink($links),true),'name',SORT_ASC);
                            $links = Link::decodeLink(Link::encodeLink($links));
                            if (!is_array($links)) $links = array($links);
                            
                            $components[$definition->getName()]['links'] = $links;
                            
                            if (isset($result2[$resultCounter]['content']) && isset($result2[$resultCounter]['status']) && $result2[$resultCounter]['status'] === 200){
                                $commands = json_decode($result2[$resultCounter]['content'], true);
                                if ($commands!==null){
                                    /*$router = new \Slim\Router();
                                    foreach($commands as $command){
                                        $route = new \Slim\Route($command['path'],'is_array');
                                        $route->via(strtoupper($command['method']));
                                        $router->map($route);
                                    }
                                    $components[$definition->getName()]['router'] = $router;*/
                                    $components[$definition->getName()]['commands'] = $commands;
                                }
                            }
                            
                            if (isset($result3[$resultCounter]['content']) && isset($result3[$resultCounter]['status']) && $result3[$resultCounter]['status'] === 200){
                                $calls = json_decode($result3[$resultCounter]['content'], true);
                                $components[$definition->getName()]['call'] = $calls;
                            }
                                                        
                            break;
                        }
                    }

                    if ($res->getStatus() !== 201){
                        $fail = true;
                    }
                }
            } else{
               $fail = true;
               $error = "keine Definitionen";
            }
            
       }else{
            $fail = true;
       }
        
        if (isset($result['status']) && $result['status'] !== 200){
            $fail = true;
            $error = "Initialisierung fehlgeschlagen";
            $errno = $result['status'];
        }
        return $components;
    }
}
#endregion Komponenten