<?php

$elements = scandir(dirname(__FILE__).'/path');

// für die HTML Ausgabe der Anfragedaten verwenden wir das Designsystem des Installationsassistenten
include_once dirname(__FILE__) . '/install/include/Design.php';

foreach ($elements as $elem){
    if ($elem=='.' || $elem=='..') continue;
    if (!is_dir(dirname(__FILE__).'/path/'.$elem)) continue;
    
    $files = scandir(dirname(__FILE__).'/path/'.$elem);
    foreach ($files as $file){
        if ($file=='.' || $file=='..') continue;
    
        if (substr($file,-3)=='.gv'){
            // es handelt sich um eine dot-Datei, diese soll
            // in eine svg umgewandelt und dann in HTML eingebettet werden
            
            $dir = $elem;
            
            // ruft graphviz auf, Ergbebnis: svg im Ordner der gv-Datei
            $para= '("dot" -O -Tsvg '.dirname(__FILE__).'/path/'.$dir.'/'.$file.') 2>&1';
            ob_start();
            system($para,$return);
            ob_end_clean();
            
            // ruft graphviz auf, Ergbebnis: png im Ordner der gv-Datei
            $para= '("dot" -O -Tpng '.dirname(__FILE__).'/path/'.$dir.'/'.$file.') 2>&1';
            ob_start();
            system($para,$return);
            ob_end_clean();
            
            if (strpos($file,'.short')!==false){
                // umrandet die svg mit HTML und ein wenig javascript, sodass über die Knoten im Graphen
                // die entsprechenden Infoseiten aufgerufen werden können
                $body = "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"><title></title><style>.node {}.node:hover {font-weight: bold;}</style><script src=\"../UI/javascript/jquery-2.0.3.min.js\"></script><script type=\"text/javascript\">".'$'."(document).ready( function(){".'$'."('.node').click(function(){var trig = ".'$'."(this);var id = trig.prop('id');var q = window.open(\"".$dir."/\"+id+\".html\", 'data', 'width=700,height=600');q.focus();return false;});});</script></head><body><div id=\"bild\">";
                $body .= file_get_contents(dirname(__FILE__).'/path/'.$dir.'/'.$file.'.svg');
                $body .= "</div></body></html>";
                
                file_put_contents(dirname(__FILE__).'/path/'.$dir.'.html',$body);
            }
        } elseif (substr($file,-5)=='.json'){
            // dieser Zweig soll die Daten der Knoten in HTML-Dateien überführen
            
            $data = json_decode(file_get_contents(dirname(__FILE__).'/path/'.$elem.'/'.$file),true);
            $body = '<html><head><meta charset="utf-8"><title></title><style>';
            $body .= file_get_contents(dirname(__FILE__).'/install/css/format.css');
            $body .= '</style></head><body>';
            
            // ab hier wird der Bereich für die Eingabedaten, des Aufrufs, gebaut
            $text = '';
            if (isset($data['inMethod']))
                $text .= Design::erstelleZeileShort(false, 'Methode', 'e', $data['inMethod'] , 'v');
            if (isset($data['inURL']))
                $text .= Design::erstelleZeileShort(false, 'Aufruf', 'e', $data['inURL'] , 'v');
            if (isset($data['inContent']) && trim($data['inContent'])!='')
                $text .= Design::erstelleZeileShort(false, 'Daten', 'e', Design::zeichneEingabebereich(false, 'Daten', $data['inContent'], 'v'));
            if (trim($text)!='')
                $text = Design::erstelleBlock(false, "Eingabe", $text);
            
            // erzeugt den Ausgabeteil des Aufrufs
            $text2 = '';
            if (isset($data['outStatus']))
                $text2 .= Design::erstelleZeileShort(false, 'Status', 'e', $data['outStatus'] , 'v');
            if (isset($data['outContent']) && trim($data['outContent'])!='')
                $text2 .= Design::erstelleZeileShort(false, 'Daten', 'e', Design::zeichneEingabebereich(false, 'Daten', $data['outContent'], 'v'));
            if (trim($text2)!='')
                $text2 = Design::erstelleBlock(false, "Ausgabe", $text2);
            
            // fügt Eingabe und Ausgabe zu einem Block zusammen
            $text3 = '';
            if (trim($text)!='')
                $text3 .= Design::erstelleZeileShort(false, $text, 'break' );
            if (trim($text2)!='')
                $text3 .= Design::erstelleZeileShort(false, $text2, 'break' );
            $body .= Design::erstelleBlock(false, $data['name'], $text3);
            
            $body .= '</body></html>';
            
            // speichert die Knotendaten im Verzeichnis des Aufrufs
            file_put_contents(dirname(__FILE__).'/path/'.$elem.'/'.substr($file,0,strlen($file)-5).'.html', $body);
        }
    }
}

// wir haben es geschafft
echo "fertig....<br>";
echo date("d.m.Y H:i:s",time())."<br>";
?>