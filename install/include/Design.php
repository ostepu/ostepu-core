<?php


/**
 * @file Design.php contains the Design class
 *
 * @author Till Uhlig
 * @date 2014
 */
 
 require_once dirname(__FILE__) . '/Einstellungen.php';
 
class Design
{
    public static function erstelleZeile()
    {
        $args = func_get_args();
        $console = array_shift($args);
        $text = '';
        $result = '';
        
        if (count($args)%2!=0)
            $args[] = '';
        
        if (!$console){
            $result = '<tr>';
            foreach($args as $pos => $data){
                if ($pos%2===0){
                    $text = $data;
                } else {
                    $result.="<td class='{$data}'>{$text}</td>";
                }
            }
            $result.='</tr>';
        } else {
            foreach($args as $pos => $data){
                if ($pos%2===0){
                    $text = $data;
                } else {
                    $result.=" {$text}";
                }
            }
            $result.="\n";         
        }
        
        return trim($result,' ');
    }
    
    public static function erstelleBlock($console, $name, $data)
    {
        if (!$console){
            $result = "<h2>{$name}</h2><table border='0' cellpadding='3' width='600'>";
            $result .= "<colgroup><col width='200'><col width='300'><col width='100'></colgroup>";
            $result .= $data;
            $result .= "</table><br/>";
        } else {
            $result = "<<<{$name}>>>\n";
            $result .= $data;
            $result .= "\n";

        }
        return $result;
    }
    
    public static function erstelleBeschreibung($console, $description)
    {
        if (!$console){
            $result = "<tr><td colspan='2'>".$description."</td></tr>";
        } else {
            $result = '';
        }
        return $result;
    }
    
    public static function erstelleEingabezeile($console, &$variable, $variablenName, $default, $save=false)
    {
        if ($save == true && $variable == null){
            $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;
        
        $result = '';
        
        if (!$console)
            $result = "<input style='width:100%' type='text' name='{$variablenName}' value='".($variable != null? $variable : $default)."'>";
        
        return $result;
    }
    
    public static function erstelleVersteckteEingabezeile($console, &$variable, $variablenName, $default, $save=false)
    {
        if ($save == true && $variable == null){
            $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;
        
        $result = '';
        
        if (!$console)
            $result = "<input type='hidden' name='{$variablenName}' value='".($variable != null ? $variable : $default)."'>";
        
        return $result;
    }
    
    public static function erstelleGruppenAuswahl($console, &$variable, $variablenName, $value, $default, $save=false)
    {
        if ($save == true && $variable == null){
           $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;

        $empty = '_';
        $result = "<input style='width:100%' type='radio' name='{$variablenName}' value='".$value."'".(($variable==$value && $variable != null) ? "checked" : ($default === null ? '' : ($default===$value ? "checked" : '')) ).">";
        return $result;
    }
    
    public static function erstelleAuswahl($console, &$variable, $variablenName, $value, $default, $save=false)
    {
        if ($save == true && $variable == null){
           $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;

        $empty = '_';
        $result = Design::erstelleVersteckteEingabezeile($console, $empty , $variablenName, $default, $save);
        $result .= "<input style='width:100%' type='checkbox' name='{$variablenName}' value='".$value."'".(($variable==$value && $variable != null) ? "checked" : ($default === null ? '' : ($default===$value ? "checked" : '')) ).">";
        return $result;
    }
    
    public static function erstellePasswortzeile($console, $variable, $variablenName, $default, $save=false)
    {
        $result = '';
        
        if (!$console)
            $result = "<input style='width:100%' type='password' name='{$variablenName}' value='".(isset($variable) ? $variable : $default)."'>";
        
        return $result;
    }
    
    public static function erstelleInstallationszeile($console, $fail, $errno, $error)
    {
        if (!$console){
            if ($fail === true){
                //$installFail = true;
                return Design::erstelleZeile($console, Sprachen::Get('main','installation'), 'e', '', 'v', "<div align ='center'><font color='red'>".Sprachen::Get('main','fail'). (($errno!=null && $errno!='') ? " ({$errno})" : '') ."<br> {$error}</font></align>", 'v');
            } else{
                return Design::erstelleZeile($console, Sprachen::Get('main','installation'), 'e', '', 'v', '<div align ="center">'.Sprachen::Get('main','ok').'</align>', 'v');
            }
        } else {
            if ($fail === true){
                //$installFail = true;
                return Design::erstelleZeile($console, Sprachen::Get('main','installation'), 'e', '', 'v', Sprachen::Get('main','fail'). (($errno!=null && $errno!='') ? " ({$errno})" : '') ." {$error}", 'v');
            } else{
                return Design::erstelleZeile($console, Sprachen::Get('main','installation'), 'e', '', 'v', Sprachen::Get('main','ok'), 'v');
            }
        }
    }
    
    public static function erstelleSubmitButton($var, $text = null)
    {
        if ($text === null)
            $text = Sprachen::Get('main','install');
        return "<input type='submit' name='{$var}' value=' {$text} '>";
    }
    
    public static function erstelleSubmitButtonFlach($varName, $value, $text = null)
    {
        if ($text === null)
            $text = Sprachen::Get('main','install');
        return "<button class='text-button info-color bold' name='{$varName}' value='{$value}'>{$text}</button>";
    }
    
    public static function erstelleSubmitButtonGrafisch($var, $bild, $width = null, $height = null)
    {
        return "<input type='image' src='{$bild}' name='{$var}' style='".($width!==null ? 'width:'.$width.'px;': '' ).($height!==null ? 'height:'.$height.'px;': '' )."'>";
    }
    
    /**
     * Converts bytes into a readable file size.
     *
     * @param int $size bytes that need to be converted
     * @return string readable file size
     */
    public static function formatBytes($size)
    {
        $base = log($size) / log(1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), 2) . ' ' . $suffixes[floor($base)] . "B";
    }
}
