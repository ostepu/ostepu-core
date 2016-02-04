<?php

$elements = scandir(dirname(__FILE__).'/path');

// für die HTML Ausgabe der Anfragedaten verwenden wir das Designsystem des Installationsassistenten
include_once dirname(__FILE__) . '/install/include/Design.php';
include_once dirname(__FILE__) . '/Assistants/CacheManager/cacheTree.php';

include_once dirname(__FILE__) . '/Assistants/vendor/Markdown/Michelf/MarkdownInterface.php';
include_once dirname(__FILE__) . '/Assistants/vendor/Markdown/Michelf/Markdown.php';
include_once dirname(__FILE__) . '/Assistants/vendor/Markdown/Michelf/MarkdownExtra.php';

foreach ($elements as $elem){
    if ($elem=='.' || $elem=='..') continue;
    if (!is_dir(dirname(__FILE__).'/path/'.$elem)) continue;

    $files = scandir(dirname(__FILE__).'/path/'.$elem);
    $SVG = array();
    foreach ($files as $file){
        if ($file=='.' || $file=='..') continue;

        if (substr($file,-4)=='.svg'){
            $SVG[] = dirname(__FILE__).'/path/'.$elem.'/'.$file;
        } elseif (substr($file,-10)=='_tree.json'){

            // es handelt sich um einen Anfragebaum, dieser soll nun zu einer dot-Datei werden
            $tree = tree::decodeTree(file_get_contents(dirname(__FILE__).'/path/'.$elem.'/'.$file));

            $text="digraph G {rankdir=TB;edge [splines=\"polyline\"];\n";

            $nodes = $tree->getElements();
            $edgeText = '';
            $root = $tree->getElementById($tree->findRoot());
            $dir = $root->name.'_'.$root->id;
            foreach ($nodes as $node) {
                foreach ($node->childs as $child){
                    $childElem = $tree->getElementById($child);
                    $edgeText.="\"".$node->name.'_'.$node->id.'"->"'.$childElem->name.'_'.$childElem->id."\"[ label = \"".$childElem->method."\" ];\n";
                }
            }

            $nodeText = '';
            foreach ($nodes as $node) {
                $key = $node->name.'_'.$node->id;
                $add = "";

                if (isset($node->status) && ($node->status<200 || $node->status>299)) {
                    $add = "color = \"red\" fontcolor=\"red\" ";
                }
                $nodeText.="\"".$key."\" [ ".$add."label=\"".$node->name."\" id=\"$key\" ]; \n";
            }

            $text.= "{".$nodeText."}";
            $text.= $edgeText;

            $text.="\n}";
            file_put_contents(dirname(__FILE__).'/path/'.$elem.'/'.$dir.'.short.gv',$text);
            @unlink(dirname(__FILE__).'/path/'.$elem.'/'.$file);
            ////////////////////////////////////// ENDE ///////////////////////////////////////
            $file = $dir.'.short.gv';

            // ruft graphviz auf, Ergbebnis: svg im Ordner der gv-Datei
            $para= '("dot" -O -Tsvg '.dirname(__FILE__).'/path/'.$elem.'/'.$file.') 2>&1';
            ob_start();
            system($para,$return);
            ob_end_clean();

            // ruft graphviz auf, Ergbebnis: png im Ordner der gv-Datei
            /*$para= '("dot" -O -Tpng '.dirname(__FILE__).'/path/'.$elem.'/'.$file.') 2>&1';
            ob_start();
            system($para,$return);
            ob_end_clean();*/

            $SVG[] = dirname(__FILE__).'/path/'.$elem.'/'.$file.'.svg';
            @unlink(dirname(__FILE__).'/path/'.$elem.'/'.$file);

        } elseif (substr($file,-5)=='.json') {
            // dieser Zweig soll die Daten der Knoten in HTML-Dateien überführen

            $data = json_decode(file_get_contents(dirname(__FILE__).'/path/'.$elem.'/'.$file),true);
            $mdFile = dirname(__FILE__).'/path/'.$elem.'/'.$data['name'].'.md';
            $mdFileResult = dirname(__FILE__).'/path/'.$elem.'/'.$data['name'].'.html';

            if (!file_exists($mdFileResult)) {
                if (isset($data['path']) && $data['path'] !== null) {
                    // speichere die Beschreibungsdatei
                    $path = dirname(__FILE__).'/..'.$data['path'];
                    $mdFile2 = $path.'/info/de.md';
                    if (file_exists($mdFile2)) {
                        file_put_contents($mdFile,file_get_contents($mdFile2));
                    }
                }
            }

            // ab hier wird der Bereich für die Eingabedaten, des Aufrufs, gebaut
            $text = '';
            if (isset($data['method']))
                $text .= Design::erstelleZeileShort(false, 'Methode', 'e', $data['method'] , 'v');
            if (isset($data['URI']))
                $text .= Design::erstelleZeileShort(false, 'Aufruf', 'e', $data['URI'] , 'v');
            if (isset($data['input']) && trim($data['input'])!='') {
                $text .= Design::erstelleZeileShort(false, 'Eingabe', 'e', Design::zeichneEingabebereich(false, 'Daten', $data['input'], 'v'));
                $text .= Design::erstelleZeileShort(false, 'Größe', 'e', Design::formatBytes(strlen($data['input'])) , 'v');
            }
            if (trim($text)!='') {
                //$text = Design::erstelleBlock(false, "Eingabe", $text);
                $result  = "<h2>Eingabe</h2>";
                $result .= "<table border='0' cellpadding='2' width='100%'>";
                $result .= "<colgroup><col width='200'><col width='*'></colgroup>";
                $result .= $text;
                $result .= "</table><br/>";
                $text = $result;
            }

            // erzeugt den Ausgabeteil des Aufrufs
            $text2 = '';
            if (isset($data['status'])) {
                $text2 .= Design::erstelleZeileShort(false, 'Status', 'e', $data['status'] , 'v');
            }
            if (isset($data['executionTime']) && trim($data['executionTime'])!='') {
                $text2 .= Design::erstelleZeileShort(false, 'Rechenzeit', 'e', $data['executionTime'].' ms' , 'v');
            }
            if (isset($data['result']) && trim($data['result'])!='') {
                $dar = $data['result'];
                if (@json_decode($dar)!==null) {
                    $dar = prettyPrint($dar);
                    $text2 .= Design::erstelleZeileShort(false, 'Resultat', 'e', $dar , 'v');
                } elseif (strpos($data['mimeType'], 'text/html') !== false) {
                    $text2 .= Design::erstelleZeileShort(false, 'Resultat', 'e', $dar , 'v');
                } else {
                    $text2 .= Design::erstelleZeileShort(false, 'Resultat', 'e', Design::zeichneEingabebereich(false, 'Daten', $data['result'], 'v'));
                }
                $text2 .= Design::erstelleZeileShort(false, 'Größe', 'e', Design::formatBytes(strlen($data['result'])) , 'v');
            }
            if (trim($text2)!=''){
                $result  = "<h2>Ausgabe</h2>";
                $result .= "<table border='0' cellpadding='2' width='100%'>";
                $result .= "<colgroup><col width='200'><col width='*'></colgroup>";
                $result .= $text2;
                $result .= "</table><br/>";
                $text2 = $result;
            }

            // fügt Eingabe und Ausgabe zu einem Block zusammen
            $text3 = '';
            if (trim($text)!='')
                $text3 .= Design::erstelleZeileShort(false, $text, 'break' );
            if (trim($text2)!='')
                $text3 .= Design::erstelleZeileShort(false, $text2, 'break' );

            $addLink='';
            if (file_exists($mdFile) || file_exists($mdFileResult)){
                $addLink = '<br><a style="font-size: 75%" href="'.$data['name'].'.html'.'">Beschreibung ></a>';
            }

            $result  = "<h2>".$data['name'].$addLink."</h2>";
            $result .= "<table border='0' cellpadding='2' width='100%'>";
            $result .= "<colgroup><col width='200'><col width='*'></colgroup>";
            $result .= $text3;
            $result .= "</table><br/>";

            $body = '<html><head><meta charset="utf-8">';
            $body .= '<link rel="stylesheet" type="text/css" href="format.css">';
            $body .= '<title></title></head><body>';
            $body .= $result;
            //$body .= Design::erstelleBlock(false, $data['name'].$addLink, $text3);

            $body .= '</body></html>';

            // speichert die Knotendaten im Verzeichnis des Aufrufs
            file_put_contents(dirname(__FILE__).'/path/'.$elem.'/'.substr($file,0,strlen($file)-5).'.html', $body);

            if (!file_exists(dirname(__FILE__).'/path/'.$elem.'/format.css')) {
                file_put_contents(dirname(__FILE__).'/path/'.$elem.'/format.css',file_get_contents(dirname(__FILE__).'/install/css/format.css'));
            }

            if (!file_exists($mdFileResult) && file_exists($mdFile)){
                $parser = new \Michelf\MarkdownExtra;
                $input = umlaute(file_get_contents($mdFile));
                $my_html = $parser->transform($input);
                file_put_contents($mdFileResult, '<link rel="stylesheet" href="github-markdown.css" type="text/css"><span class="markdown-body">'.$my_html.'</span>');
                @unlink($mdFile);
            }elseif (file_exists($mdFile)) {
                @unlink($mdFile);
            }

            // entferne nun die bearbeitete Datei
            @unlink(dirname(__FILE__).'/path/'.$elem.'/'.$file);
        }
    }

    $jqueryFile = dirname(__FILE__) . '/UI/javascript/jquery-2.0.3.min.js';
    $jqueryFileTarget = dirname(__FILE__).'/path/'.$elem.'/jquery-2.0.3.min.js';
    if (file_exists($jqueryFile) && !file_exists($jqueryFileTarget)){
        file_put_contents($jqueryFileTarget, file_get_contents($jqueryFile));
    }

    // umrandet die svg mit HTML und ein wenig javascript, sodass über die Knoten im Graphen
    // die entsprechenden Infoseiten aufgerufen werden können
    $SVG = array_values(array_unique($SVG));
    if (!empty($SVG)){
        $body  = "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"><title></title>";
        $body .= "<style>.node {}.node:hover {font-weight: bold;}</style>";
        $body .= '<link rel="stylesheet" type="text/css" href="'.$elem.'/format.css">';
        $body .= "<script src=\"".$elem."/jquery-2.0.3.min.js\"></script>";
        $body .= "<script type=\"text/javascript\">".'$'."(document).ready( function(){".'$'."('.node').click(function(){var trig = ".'$'."(this);var id = trig.prop('id');var q = window.open(\"".$elem."/\"+id+\".html\", 'data', 'width=700,height=600');q.focus();return false;});});</script></head><body><div id=\"bild\">";

        foreach ($SVG as $key=>$svg){
            $body .= "<h2>Anfrage ".($key+1)."</h2>";
            $body .= "<table border='0' cellpadding='2' width='100%'>";
            $body .= "<colgroup><col width='200'><col width='*'></colgroup>";
            $svgData = file_get_contents($svg);
            $body .= Design::erstelleZeileShort(false, $svgData , 'center');
            $body .= "</table><br/>";
            // entferne nun die bearbeitete Datei
            @unlink($svg);
        }
        $body .= "</div></body></html>";

        file_put_contents(dirname(__FILE__).'/path/'.$elem.'.html',$body);

        $cssFile = dirname(__FILE__) . '/UI/css/github-markdown.css';
        $cssFileTarget = dirname(__FILE__).'/path/'.$elem.'/github-markdown.css';
        if (file_exists($cssFile) && !file_exists($cssFileTarget)){
            file_put_contents($cssFileTarget, file_get_contents($cssFile));
        }
    }
}

function umlaute($text){
    $search  = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß');
    $replace = array('&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', '&szlig;');
    return str_replace($search, $replace, $text);;
}

function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );
    $in_value = false;
    $lastClass = null;
    $old_line_level = null;

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        $class = null;

        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            if ($in_quotes) {
                $class = 'g';
            }
            if ($in_quotes && $in_value) {
                $class = 'z';
            }
            $in_quotes = !$in_quotes;
            if ($in_quotes) {
                $class = 'g';
            }
            if ($in_quotes && $in_value) {
                $class = 'z';
            }
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    $class = 'm';
                    $in_value = false;
                    break;

                case '{': case '[':
                    $level++;
                    $class = 'm';
                    $in_value = false;
                case ',':
                    $ends_line_level = $level;
                    $class = 'm';
                    $in_value = false;
                    break;

                case ':':
                    $post = " ";
                    $class = 'm';
                    $in_value = true;
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
                default:
                    $class = 'g';
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }

        if ($in_quotes) {
            $class = 'g';
        }
        if ($in_value) {
            $class = 'z';
        }

        if( $new_line_level !== NULL ) {
            if ($old_line_level === null) {
                $result .= "<div style='margin-left: ".($new_line_level*5)."px'>";
            } elseif($new_line_level !== $old_line_level) {
                $result .= "</div><div style='margin-left: ".($new_line_level*5)."px'>";
            } else {
                $result .= "<br>";
            }

            $old_line_level = $new_line_level;
        }

        if ($class === null && $class !== $lastClass){
            $result .= $char.$post;
        } elseif ($class !== null && $lastClass === null) {
           $result .= '<'.$class.'>'.$char.$post;
        } elseif ($class === null && $class === $lastClass) {
           $result .= $char.$post;
        } elseif ($class !== null && $class !== $lastClass && $lastClass !== null) {
           $result .= '<'.$class.'>'.$char.$post;
        } else {
            $result .= $char.$post;
        }

        $lastClass = $class;
    }

    if( $old_line_level !== NULL ) {
        $result .= "</div>";
    }

    return $result;
}

// wir haben es geschafft
echo "fertig....<br>";
echo date("d.m.Y H:i:s",time())."<br>";