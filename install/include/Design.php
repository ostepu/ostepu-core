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
        $simple = array_shift($args);
        $text = '';
        
        $result = '<tr>';
        foreach($args as $pos => $data){
            if ($pos%2===0){
                $text = $data;
            } else {
                $result.="<td class='{$data}'>{$text}</td>";
            }
        }
        $result.='</tr>';
        
        return $result;
    }
    
    public static function erstelleBlock($simple, $name, $data)
    {
        $result = "<h2>{$name}</h2><table border='0' cellpadding='3' width='600'>";
        $result .= "<colgroup><col width='200'><col width='300'><col width='100'></colgroup>";
        $result.=$data;
        $result.="</table><br/>";
        return $result;
    }
    
    public static function erstelleEingabezeile($simple, &$variable, $variablenName, $default, $save=false)
    {
        if ($save == true && $variable == null){
            $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;
        
        $result = "<input style='width:100%' type='text' name='{$variablenName}' value='".($variable != null? $variable : $default)."'>";
        return $result;
    }
    
    public static function erstelleVersteckteEingabezeile($simple, &$variable, $variablenName, $default, $save=false)
    {
        if ($save == true && $variable == null){
            $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;
        
        $result = '';
            $result = "<input type='hidden' name='{$variablenName}' value='".($variable != null ? $variable : $default)."'>";
        return $result;
    }
    
    public static function erstelleAuswahl($simple, &$variable, $variablenName, $value, $default, $save=false)
    {
        if ($save == true && $variable == null){
           $variable = Einstellungen::Get($variablenName, $default);
        } 
        
        if ($save == true && $variable != null)
            Einstellungen::Set($variablenName, $variable);
            
        if ($variable == null)
            $variable = $default;

        $result = "<input style='width:100%' type='checkbox' name='{$variablenName}' value='".$value."'".(($variable==$value && $variable != null) ? "checked" : ($default === null ? '' : ($default===$value ? "checked" : '')) ).">";
        return $result;
    }
    
    public static function erstellePasswortzeile($simple, $variable, $variablenName, $default, $save=false)
    {
        $result = "<input style='width:100%' type='password' name='{$variablenName}' value='".(isset($variable) ? $variable : $default)."'>";
        return $result;
    }
    
    public static function erstelleInstallationszeile($simple, &$installFail, $fail, $errno, $error)
    {
        if ($fail === true){
            $installFail = true;
            return Design::erstelleZeile($simple, Sprachen::Get('main','installation'), 'e', '', 'v', "<div align ='center'><font color='red'>".Sprachen::Get('main','fail'). (($errno!==null && $errno!='') ? " ({$errno})" : '') ."<br> {$error}</font></align>", 'v');
        } else{
            return Design::erstelleZeile($simple, Sprachen::Get('main','installation'), 'e', '', 'v', '<div align ="center">'.Sprachen::Get('main','ok').'</align>', 'v');
        }
    }
    
    public static function erstelleSubmitButton($var, $text = null)
    {
        if ($text === null)
            $text = Sprachen::Get('main','install');
        return "<input type='submit' name='{$var}' value=' {$text} '>";
    }
    
    public static function erstelleSubmitButtonGrafisch($var, $bild, $width = null, $height = null)
    {
        return "<input type='image' src='{$bild}' name='{$var}' style='".($width!==null ? 'width:'.$width.'px;': '' ).($height!==null ? 'height:'.$height.'px;': '' )."'>";
    }
}

?>