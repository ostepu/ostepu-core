<?php
#region Komponenten
if (!$simple)
    if ($selected_menu === 3){
        $text='';

        $text .= "<tr><td colspan='2'>".Sprachen::Get('components','description')."</td></tr>";
        //Design::erstelleEingabezeile($simple, (isset($data['PL']['init']) ? $data['PL']['init'] : null), 'data[PL][init]', 'DB/CControl')
        $text .= Design::erstelleZeile($simple, '<s>'.Sprachen::Get('components','init').'</s>', 'e', '', 'v', Design::erstelleSubmitButton("actionInitComponents"), 'h');
        $text .= Design::erstelleZeile($simple, Sprachen::Get('components','details'), 'e', Design::erstelleAuswahl($simple, $data['CO']['co_details'], 'data[CO][co_details]', 'details', null, true), 'v');
        
        if ($initComponents){
            // counts installed commands
            $installedCommands = 0;
            
            // counts installed components
            $installedComponents = 0;
            
            // counts installed links
            $installedLinks = 0;
            
            foreach($components as $componentName => $component)
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
                if (isset($component['init']) && isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                    $text .= "<tr><td class='e' rowspan='{$countLinks}'>{$componentName}</td><td class='v'>{$component['init']->getAddress()}</td><td class='e'><div align ='center'>".($component['init']->getStatus() === 201 ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$component['init']->getStatus()})</font>")."</align></td></tr>";
                
                if (isset($component['init']) && $component['init']->getStatus() === 201){
                    $installedComponents++;
                    $installedLinks+=count(isset($component['links']) ? $component['links'] : array());
                    $installedCommands+=$countCommands;
                    
                    if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                        $text .= "<tr><td class='v' colspan='2'>".Sprachen::Get('components','installedCalls').": {$countCommands}</td></tr>";
                
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
                                        if (!isset($components[$link->getTargetName()]['router'])){
                                            $notRoutable=true;
                                            break;
                                        }
                                        if ($components[$link->getTargetName()]['router']==null) continue;
                                        if ($call===null) continue;
                                        if (!isset($call['method'])) continue;
                                        if (!isset($call['path'])) continue;
                                        
                                        $routes = count($components[$link->getTargetName()]['router']->getMatchedRoutes(strtoupper($call['method']), $call['path']),true);
                                        if ($routes===0){
                                            $notRoutable=true;
                                            break;
                                        }
                                    }
                                    if ($notRoutable) break;
                                }
                                
                                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                                    $text .= "<tr><td class='v'>{$link->getName()}</td><td class='e'><div align ='center'>".(!$notRoutable ? Sprachen::Get('main','ok') : '<font color="red">'.Sprachen::Get('components','notRoutable').'</font>')."</align></td></tr>";
                            }
                        }
                        
                        if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                            $text .= "<tr><td class='v'>{$link->getName()}".(!$linkFound ? " (<font color='red'>".Sprachen::Get('components','unknown')."</font>)" : '')."</td><td class='v'>{$link->getTargetName()}</td></tr>"; 
                    
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
                                if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                                    $text .= "<tr><td class='v'>{$callList['name']}</td><td class='e'><font color='red'>".Sprachen::Get('components','unallocated')."</font></td></tr>";
                            }
                        }
                    }
                }
            }
            
            if (isset($data['CO']['co_details']) && $data['CO']['co_details'] === 'details')
                $text .= Design::erstelleZeile($simple, '', '', '', '', '' , '');
            
            $text .= Design::erstelleZeile($simple, Sprachen::Get('components','installedComponents'), 'e', '', 'v', "<div align ='center'>".$installedComponents."</align", 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('components','installedLinks'), 'e', '', 'v', "<div align ='center'>".$installedLinks."</align", 'v');
            $text .= Design::erstelleZeile($simple, Sprachen::Get('components','installedCommands'), 'e', '', 'v', "<div align ='center'>".$installedCommands."</align", 'v');

            $text .= Design::erstelleInstallationszeile($simple, $installFail, $fail, $errno, $error); 
        }
        
        echo Design::erstelleBlock($simple, Sprachen::Get('components','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($simple, $data['CO']['co_details'], 'data[CO][co_details]', null,true);
        echo $text;
    }
#endregion Komponenten
?>