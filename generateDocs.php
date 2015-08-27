<?php

$path = dirname(__FILE__) . '/DB/DBBeispiel';

if (!file_exists($path.'/info/de.md')){
    $commands = json_decode(file_get_contents($path.'/Commands.json'),true);
    $component = json_decode(file_get_contents($path.'/Component.json'),true);
    $component = isset($component['links']) ? $component['links'] : array();
    $connectors = json_decode(file_get_contents($path.'/Component.json'),true);
    $connectors = isset($connectors['connector']) ? $connectors['connector'] : array();

    if (!is_dir($path.'/info'))
        mkdir($path.'/info', 0755);

    if (count($commands)>0){
        $text="";
        $text.="#### Eingänge\n";
        
        $vars = array();
        foreach($commands as $command){
            $pa = $command['path'];
            $treffer = array();
            if (preg_match('![:](\w+)!', $pa, $treffer)){
                foreach ($treffer as $treff){
                    if (substr($treff,0,1) != ':') continue;
                    $vars[] = substr($treff,1,strlen($treff)-1);
                }
            }
        }
        $vars = array_unique($vars);
        foreach ($vars as $var){
            $text.="- {$var} = ??? \n";
        }

        $text.="\n";
        $text.="| Bezeichnung  | Eingabetyp  | Ausgabetyp | Befehl | Beschreibung |\n";
        $text.="| :----------- |:-----------:| :---------:| :----- | :----------- |\n";
        foreach($commands as $command){
            $name = isset($command['name']) ? $command['name'] : ' ??? ';
            $inputType = isset($command['inputType']) ? $command['inputType'] : ' ??? ';
            if (trim($inputType)=='')$inputType='-';
            $outputType = isset($command['outputType']) ? $command['outputType'] : ' ??? ';
            if (trim($outputType)=='')$outputType='-';
            $method = isset($command['method']) ? $command['method'] : ' ??? ';
            $pa = isset($command['path']) ? $command['path'] : ' ??? ';
            $text.="|{$name}|{$inputType}|{$outputType}|{$method}<br>{$pa}| ??? |\n";
        }
        $text.="\n";
    }
    
    if (count($component)>0){
        $text.="#### Ausgänge\n";
        $vars = array();
        foreach($component as $command){
            if (!isset($command['links'])) continue;
            foreach($command['links'] as $link){
                $pa = $link['path'];
                $treffer = array();
                if (preg_match('![:](\w+)!', $pa, $treffer)){
                    foreach ($treffer as $treff){
                        if (substr($treff,0,1) != ':') continue;
                        $vars[] = substr($treff,1,strlen($treff)-1);
                    }
                }
            }
        }
    
        $vars = array_unique($vars);
        foreach ($vars as $var){
            $text.="- {$var} = ??? \n";
        }
        
        $text.="\n";
        $text.="| Bezeichnung  | Ziel  | Verwendung | Beschreibung |\n";
        $text.="| :----------- |:----- | :--------- | :----------- |\n";
        foreach($component as $command){
            if (!isset($command['links'])) continue;
            foreach($command['links'] as $link){
                $name = isset($command['name']) ? $command['name'] : ' ??? ';
                $target = isset($command['target']) ? $command['target'] : ' ??? ';
                $method = isset($link['method']) ? $link['method'] : ' ??? ';
                $pa = isset($link['path']) ? $link['path'] : ' ??? ';
                $text.="|{$name}|{$target}|{$method}<br>{$pa}| ??? |\n";
            }
        }
        $text.="\n";
    }
    
    if (count($connectors)>0){
        $text.="#### Anbindungen\n";
        $text.="| Bezeichnung  | Ziel  | Priorität | Beschreibung |\n";
        $text.="| :----------- |:----- | :--------:| :------------|\n";
        foreach($connectors as $command){
                $name = isset($command['name']) ? $command['name'] : ' ??? ';
                $target = isset($command['target']) ? $command['target'] : ' ??? ';
                $text.="|{$name}|{$target}|-| ??? |\n";
        }
        $text.="\n";
    }
    
    
    $text.="Stand ".date("d.m.Y",time())."\n";
    file_put_contents($path.'/info/de.md',$text);
}

// wir haben es geschafft
echo "fertig....<br>";
echo date("d.m.Y H:i:s",time())."<br>";
?>