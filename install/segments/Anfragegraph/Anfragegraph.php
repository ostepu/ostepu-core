<?php
/**
 * @file Anfragegraph.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

#region Anfragegraph
class Anfragegraph
{
    private static $initialized=false;
    public static $name = 'anfragegraph';
    public static $installed = false;
    public static $page = 8;
    public static $rank = 100;
    public static $enabledShow = true;
    private static $langTemplate='Anfragegraph';

    public static $onEvents = array(
                                    'enableQueryTree'=>array(
                                        'name'=>'enableQueryTree',
                                        'event'=>array('actionEnableQueryTree'),
                                        'procedure'=>'enableQueryTree',
                                        'enabledInstall'=>true
                                        ),
                                    'disableQueryTree'=>array(
                                        'name'=>'disableQueryTree',
                                        'event'=>array('actionDisableQueryTree'),
                                        'procedure'=>'disableQueryTree',
                                        'enabledInstall'=>true
                                        ),
                                    'convertQueryTrees'=>array(
                                        'name'=>'convertQueryTrees',
                                        'event'=>array('actionConvertQueryTrees'),
                                        'procedure'=>'convertQueryTrees',
                                        'enabledInstall'=>true
                                        )
                                    );

    public static function getDefaults($data)
    {
        $res = array(
                     'treePath' => array('data[QUERYTREE][treePath]', '/var/www/queryTree')
                     );
        return $res;
    }

    public static function init($console, &$data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        Language::loadLanguageFile('de', self::$langTemplate, 'json', dirname(__FILE__).'/');
        Installation::log(array('text'=>Installation::Get('main','languageInstantiated')));

        $def = self::getDefaults($data);

        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['QUERYTREE']['treePath'], 'data[QUERYTREE][treePath]', $def['treePath'][1], true);

        echo $text;

        self::$initialized = true;
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
    }

    private static function getConfContent($data)
    {
        $confFile = $data['PL']['localPath'] . DIRECTORY_SEPARATOR . 'Assistants' . DIRECTORY_SEPARATOR . 'QEPGenerator' . DIRECTORY_SEPARATOR . 'config.json';
        if (file_exists($confFile)){
            return json_decode(file_get_contents($confFile),true);
        }
        return false;
    }

    private static function setConfContent($data, $confContent)
    {
        $confFile = $data['PL']['localPath'] . DIRECTORY_SEPARATOR . 'Assistants' . DIRECTORY_SEPARATOR . 'QEPGenerator' . DIRECTORY_SEPARATOR . 'config.json';
        file_put_contents($confFile, json_encode($confContent));
    }

    public static function show($console, $result, $data)
    {
        if (!Einstellungen::$accessAllowed) return;

        Installation::log(array('text'=>Installation::Get('main','functionBegin')));

        $location = $data['QUERYTREE']['treePath'];

        $text='';
        $text .= Design::erstelleBeschreibung($console,Installation::Get('main','description',self::$langTemplate));

        $text .= Design::erstelleZeile($console, Installation::Get('manage','path',self::$langTemplate), 'e', Design::erstelleEingabezeile($console, $data['QUERYTREE']['treePath'], 'data[QUERYTREE][treePath]', $data['QUERYTREE']['treePath'], true), 'v');

        // den Status der aktuellen Konfiguration ermitteln
        $content = self::getConfContent($data);
        $status = null;
        if ($content !== false){
            if (isset($content['makeTree'])){
                $status = $content['makeTree'];
            }
        }

        // zeichnet den aktuellen Status
        $statusText = ($status === null ? Installation::Get('manage','unkownState',self::$langTemplate) : ($status === true ? Installation::Get('manage','enabled',self::$langTemplate) : Installation::Get('manage','disabled',self::$langTemplate)));
        $text .= Design::erstelleZeile($console, Installation::Get('manage','state',self::$langTemplate), 'e', $statusText , 'v');

        // zeichnet die Schaltfläche zum Einschalten
        if (self::$onEvents['enableQueryTree']['enabledInstall']){
            if ($status === null || $status === false){
                $text .= Design::erstelleZeile($console, Installation::Get('manage','enableDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['enableQueryTree']['event'][0],Installation::Get('manage','enable',self::$langTemplate)), 'h');
            }
        }

        // zeichnet die Schaltfläche zum Ausschalten
        if (self::$onEvents['disableQueryTree']['enabledInstall']){
            if ($status === true){
                $text .= Design::erstelleZeile($console, Installation::Get('manage','disableDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['disableQueryTree']['event'][0],Installation::Get('manage','disable',self::$langTemplate)), 'h');
            }
        }

        // ermittelt vorhandene Aufzeichnungen
        $recordBase = array();
        $recordAccess = array();

        if (file_exists($location)){
            try {
                $handle = opendir($location);
            } catch (Exception $e) {
                // der Ordner konnte nicht zugegriffen werden
                Installation::log(array('text'=>$location.' existiert nicht oder es fehlt die Zugriffsberechtigung.','logLevel'=>LogLevel::ERROR));
                Installer::$messages[] = array('text'=>$location.' existiert nicht oder es fehlt die Zugriffsberechtigung.','type'=>'error');
                return $pluginFiles;
            }
        } else {
            Installation::log(array('text'=>$location.' existiert nicht.','logLevel'=>LogLevel::WARNING));
            $text .= Design::erstelleZeile($console, '', 'e', $location.' existiert nicht.' , 'error v');
        }
            
        if (isset($handle) && $handle !== false) {
            while (false !== ($file = readdir($handle))) {
                if ($file=='.' || $file=='..') continue;
                $filePath = $location. DIRECTORY_SEPARATOR .$file;
                if (is_dir($filePath)){
                    $recordBase[$file] = $filePath;
                } else {
                    $ext = pathinfo($filePath)['extension'];

                    if ($ext === 'html'){
                        $recordAccess[substr($file,0,-5)] = $filePath;
                    }
                }
            }
            closedir($handle);
        }

        foreach($recordBase as $key => $base){
            if (isset($recordAccess[$key])){
                $text .= Design::erstelleZeile($console, $base , 'v', $recordAccess[$key], 'v_c');
            } else {
                $text .= Design::erstelleZeile($console, $base , 'v', Installation::Get('convertQueryTrees','notConverted',self::$langTemplate), 'v_c');
            }
        }

        if (empty($recordBase)){
            $text .= Design::erstelleZeile($console, '','e',Installation::Get('convertQueryTrees','noRecords',self::$langTemplate),'v_c' );
        }

        // zeichnet die Schaltfläche zum Rendern der Aufzeichnungen
        if (self::$onEvents['convertQueryTrees']['enabledInstall']){
            if (!empty($recordBase)){
                $text .= Design::erstelleZeile($console, Installation::Get('convertQueryTrees','executeDesc',self::$langTemplate), 'e',  Design::erstelleSubmitButton(self::$onEvents['convertQueryTrees']['event'][0],Installation::Get('convertQueryTrees','execute',self::$langTemplate)), 'h');
            }
        }

        echo Design::erstelleBlock($console, Installation::Get('main','title',self::$langTemplate), $text);

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return null;
    }

    public static function enableQueryTree($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));

        $res = array();
        $content = self::getConfContent($data);
        if ($content !== false){
            $content['treePath'] = $data['QUERYTREE']['treePath'];
            $content['makeTree'] = true;
            $content['enabled'] = true;
            self::setConfContent($data, $content);
            $res['success'] = true;
        } else {
            $content = array();
            $content['treePath'] = $data['QUERYTREE']['treePath'];
            $content['makeTree'] = true;
            $content['enabled'] = true;
            self::setConfContent($data, $content);
            $res['success'] = true;
            //$res['success'] = false;
            //$res['statusText'] = Installation::Get('manage','missingConfFile',self::$langTemplate);
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function disableQueryTree($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));

        $res = array();
        $content = self::getConfContent($data);
        if ($content !== false){
            $content['treePath'] = $data['QUERYTREE']['treePath'];
            $content['makeTree'] = false;
            $content['enabled'] = false;
            self::setConfContent($data, $content);
            $res['success'] = true;
        } else {
            $content = array();
            $content['treePath'] = $data['QUERYTREE']['treePath'];
            $content['makeTree'] = false;
            $content['enabled'] = false;
            self::setConfContent($data, $content);
            $res['success'] = true;
            //$res['success'] = false;
            //$res['statusText'] = Installation::Get('manage','missingConfFile',self::$langTemplate, array('file'=>$confFile));
        }

        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    public static function convertQueryTrees($data, &$fail, &$errno, &$error)
    {
        Installation::log(array('text'=>Installation::Get('main','functionBegin')));
        $res = array();
        $mainPath = $data['PL']['localPath'];

        include_once $mainPath . '/Assistants/QEPGenerator/cacheTree.php';

        if (file_exists($mainPath . '/Assistants/vendor/Markdown/Michelf/MarkdownInterface.php')){
            include_once $mainPath . '/Assistants/vendor/Markdown/Michelf/MarkdownInterface.php';
            include_once $mainPath . '/Assistants/vendor/Markdown/Michelf/Markdown.php';
            include_once $mainPath . '/Assistants/vendor/Markdown/Michelf/MarkdownExtra.php';

            $location = $data['QUERYTREE']['treePath'];
            $elements = scandir($location);

            foreach ($elements as $elem){
                if ($elem=='.' || $elem=='..') continue;
                if (!is_dir($location . '/'.$elem)) continue;

                $files = scandir($location .'/'.$elem);
                $SVG = array();
                foreach ($files as $file){
                    if ($file=='.' || $file=='..') continue;

                    if (substr($file,-4)=='.svg'){
                        $SVG[] = $location.'/'.$elem.'/'.$file;
                    } elseif (substr($file,-10)=='_tree.json'){

                        // es handelt sich um einen Anfragebaum, dieser soll nun zu einer dot-Datei werden
                        $tree = tree::decodeTree(file_get_contents($location.'/'.$elem.'/'.$file));

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
                        file_put_contents($location.'/'.$elem.'/'.$dir.'.short.gv',$text);
                        @unlink($location.'/'.$elem.'/'.$file);
                        ////////////////////////////////////// ENDE ///////////////////////////////////////
                        $file = $dir.'.short.gv';

                        // ruft graphviz auf, Ergbebnis: svg im Ordner der gv-Datei
                        $para= '("dot" -O -Tsvg '.$location.'/'.$elem.'/'.$file.') 2>&1';
                        ob_start();
                        system($para,$return);
                        ob_end_clean();

                        // ruft graphviz auf, Ergbebnis: png im Ordner der gv-Datei
                        /*$para= '("dot" -O -Tpng '.dirname(__FILE__).'/path/'.$elem.'/'.$file.') 2>&1';
                        ob_start();
                        system($para,$return);
                        ob_end_clean();*/

                        $SVG[] = $location.'/'.$elem.'/'.$file.'.svg';
                        @unlink($location.'/'.$elem.'/'.$file);

                    } elseif (substr($file,-5)=='.json') {
                        // dieser Zweig soll die Daten der Knoten in HTML-Dateien überführen

                        $data = json_decode(file_get_contents($location.'/'.$elem.'/'.$file),true);
                        $mdFile = $location.'/'.$elem.'/'.$data['name'].'.md';
                        $mdFileResult = $location.'/'.$elem.'/'.$data['name'].'.html';

                        if (!file_exists($mdFileResult)) {
                            if (isset($data['path']) && $data['path'] !== null) {
                                // speichere die Beschreibungsdatei
                                $path = $mainPath.'/'.$data['path'];
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
                                $dar = self::prettyPrint($dar);
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
                        file_put_contents($location.'/'.$elem.'/'.substr($file,0,strlen($file)-5).'.html', $body);

                        if (!file_exists($location.'/'.$elem.'/format.css')) {
                            file_put_contents($location.'/'.$elem.'/format.css',file_get_contents($mainPath.'/install/css/format.css'));
                        }

                        if (!file_exists($mdFileResult) && file_exists($mdFile)){
                            $parser = new \Michelf\MarkdownExtra;
                            $input = self::umlaute(file_get_contents($mdFile));
                            $my_html = $parser->transform($input);
                            file_put_contents($mdFileResult, '<link rel="stylesheet" href="github-markdown.css" type="text/css"><span class="markdown-body"><a style="font-size: 75%" href="javascript:history.back()">&lt; zur&uuml;ck</a><br>'.$my_html.'</span>');
                            @unlink($mdFile);
                        }elseif (file_exists($mdFile)) {
                            @unlink($mdFile);
                        }

                        // entferne nun die bearbeitete Datei
                        @unlink($location.'/'.$elem.'/'.$file);
                    }
                }

                $jqueryFile = $mainPath . '/UI/javascript/jquery-2.0.3.min.js';
                $jqueryFileTarget = $location.'/'.$elem.'/jquery-2.0.3.min.js';
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

                    file_put_contents($location.'/'.$elem.'.html',$body);

                    $cssFile = $mainPath . '/UI/css/github-markdown.css';
                    $cssFileTarget = $location.'/'.$elem.'/github-markdown.css';
                    if (file_exists($cssFile) && !file_exists($cssFileTarget)){
                        file_put_contents($cssFileTarget, file_get_contents($cssFile));
                    }
                }
            }
        }
        Installation::log(array('text'=>Installation::Get('main','functionEnd')));
        return $res;
    }

    private static function umlaute($text){
        $search  = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß');
        $replace = array('&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', '&szlig;');
        return str_replace($search, $replace, $text);;
    }

    public static function prettyPrint( $json )
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
}
#endregion Anfragegraph