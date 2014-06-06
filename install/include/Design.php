<?php
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
        $result.=$data;
        $result.="</table><br/>";
        return $result;
    }
    
    public static function erstelleEingabezeile($simple, $variable, $variablenName, $default)
    {
        $result = "<input style='width:100%' type='text' name='{$variablenName}' value='".($variable!==null ? $variable : $default)."'>";
        return $result;
    }
    
    public static function erstellePasswortzeile($simple, $variable, $variablenName, $default)
    {
        $result = "<input style='width:100%' type='password' name='{$variablenName}' value='".(isset($variable) ? $variable : $default)."'>";
        return $result;
    }
    
    public static function erstelleInstallationszeile($simple, &$installFail, $fail, $errno, $error)
    {
        if ($fail === true){
            $installFail = true;
            return Design::erstelleZeile($simple, 'Installation', 'e', '', 'v', "<div align ='center'><font color='red'>Fehler ({$errno}) <br> {$error}</font></align>", 'v');
        } else{
            return Design::erstelleZeile($simple, 'Installation', 'e', '', 'v', '<div align ="center">OK</align>', 'v');
        }
    }
    
    public static function erstelleSubmitButton($var)
    {
        return "<input type='submit' name='{$var}' value=' Installieren '>";
    }
}

?>