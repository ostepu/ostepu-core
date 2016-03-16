<?php
/**
 * @file Design.php contains the Design class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2016
 */

 //require_once dirname(__FILE__) . '/Einstellungen.php';

class Design
{

    /**
     * Erzeugt eine Tabellenzeile (für erstelleBlock())
     * Es wird von 3 Spalten ausgegangen (Bei 2 Eingabespalten wird auf die letzte zusammengefasst,
     * bei einer Eingabespalte wird diese auf eine reduziert)
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param string[] Abwechselnd Daten und CSS-Klassen (Bsp.: TextA, classA, TextB, classB)
     * @return string Der Text des Blocks
     */
    public static function erstelleZeileShort()
    {
        $args = func_get_args();
        $console = array_shift($args);
        $text = '';
        $result = '';

        if (count($args)%2!=0)
            $args[] = '';

        if (!$console){
            $addToLast = '';
            if (count($args)<=4)
                $addToLast = ' colspan="2" ';
            if (count($args)<=2)
                $addToLast = ' colspan="3" ';

            $result = '<tr>';
            foreach($args as $pos => $data){
                if ($pos%2===0){
                    $text = $data;
                } else {
                    $result.="<td ".($pos==count($args)-1 ? $addToLast : '')."class='{$data}'>{$text}</td>";
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

    /**
     * Erzeugt eine Tabellenzeile (für erstelleBlock())
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param string[] Abwechselnd Daten und CSS-Klassen (Bsp.: TextA, classA, TextB, classB)
     * @return string Der Text des Blocks
     */
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

    /**
     * Erzeugt einen Block (fasst Elemente zu einem Block zusammen)
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param string $name Der Bezeichner des Blocks
     * @param string $data Der Blockinhalt
     * @return string Der Text des Blocks
     */
    public static function erstelleBlock($console, $name, $data)
    {
        $result = '';
        if (!$console){
            if ($name!==null){
                $result .= "<h2>{$name}</h2>";
            }
            $result .= "<table border='0' cellpadding='3' width='600'>";
            $result .= "<colgroup><col width='200'><col width='300'><col width='100'></colgroup>";
            $result .= $data;
            $result .= "</table><br/>";
        } else {
            $result .= "<<<{$name}>>>\n";
            $result .= $data;
            $result .= "\n";

        }
        return $result;
    }

    /**
     * Erzeugt eine Beschreibung
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param string $description Der Beschreibungstext
     * @return string Der Text des Eingabebereichs
     */
    public static function erstelleBeschreibung($console, $description)
    {
        if (!$console){
            $result = "<tr><td colspan='2'>".$description."</td></tr>";
        } else {
            $result = '';
        }
        return $result;
    }

    /**
     * Erzeugt eine Eingabezeile
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param mixed $variable Der aktuelle Wert des Feldes (null = nicht zugewiesen)
     * @param string $variablenName Der Name des Feldes
     * @param mixed $default Der Standartwert (wenn $variable = null)
     * @param bool $save true = speichere $variable in den Server Einstellungen, false = sonst
     * @return string Der Text der Eingabezeile
     */
    public static function erstelleEingabezeile($console, &$variable, $variablenName, $default, $save=false)
    {
        if ($save == true && $variable === null){
            $variable = Einstellungen::Get($variablenName, $default);
        }

        if ($save == true && $variable !== null)
            Einstellungen::Set($variablenName, $variable);

        if ($variable === null)
            $variable = $default;

        $result = '';

        if (!$console)
            $result = "<input style='width:100%' type='text' name='{$variablenName}' value='".(isset($variable) ? $variable : $default)."'>";

        return $result;
    }

    /**
     * Erzeugt einen Eingabebereich
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param mixed $variable Der aktuelle Wert des Feldes (null = nicht zugewiesen)
     * @param string $variablenName Der Name des Feldes
     * @param mixed $default Der Standartwert (wenn $variable = null)
     * @param bool $save true = speichere $variable in den Server Einstellungen, false = sonst
     * @return string Der Text des Eingabebereichs
     */
    public static function erstelleEingabebereich($console, &$variable, $variablenName, $default, $save=false)
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
            $result = self::zeichneEingabebereich($console, $variablenName, ($variable != null? $variable : $default));

        return $result;
    }

    /**
     * Erzeugt einen Eingabebereich
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param string $variablenName Der Name des Feldes
     * @param string $text Der Inhalt
     * @return string Der Text des Eingabebereichs
     */
    public static function zeichneEingabebereich($console, $variablenName, $text)
    {
        $result = '';
        if (!$console){
            $result .= "<textarea rows='10' cols='100' style='width:100%' name='{$variablenName}'>";
            $result .= $text;
            $result .="</textarea>";
        }
        return $result;
    }

    /**
     * Erzeugt eine verstecke Eingabezeile
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param mixed $variable Der aktuelle Wert des Feldes (null = nicht zugewiesen)
     * @param string $variablenName Der Name des Feldes
     * @param mixed $default Der Standartwert (wenn $variable = null)
     * @param bool $save true = speichere $variable in den Server Einstellungen, false = sonst
     * @return string Der Text der Eingabezeile
     */
    public static function erstelleVersteckteEingabezeile($console, &$variable, $variablenName, $default, $save=false)
    {
        if ($save === true && $variable === null){
            $variable = Einstellungen::Get($variablenName, $default);
        }

        if ($save === true && $variable !== null){
            Einstellungen::Set($variablenName, $variable);
        }

        if ($variable === null)
            $variable = $default;

        $result = '';

        if (!$console){
            $result = "<input type='hidden' name='{$variablenName}' value='".(isset($variable) ? $variable : $default)."'>";
        }

        return $result;
    }

    /**
     * Erzeugt eine Gruppen-Auswahl
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param mixed $variable Der aktuelle Wert des Feldes (null = nicht zugewiesen)
     * @param string $variablenName Der Name des Feldes
     * @param mixed $default Der Standartwert (wenn $variable = null)
     * @param bool $save true = speichere $variable in den Server Einstellungen, false = sonst
     * @return string Der Text der Auswahl
     */
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

    /**
     * Erzeugt eine Auswahlbox
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param mixed $variable Der aktuelle Wert des Feldes (null = nicht zugewiesen)
     * @param string $variablenName Der Name des Feldes
     * @param mixed $default Der Standartwert (wenn $variable = null)
     * @param bool $save true = speichere $variable in den Server Einstellungen, false = sonst
     * @return string Der Text der Auswahl
     */
    public static function erstelleAuswahl($console, &$variable, $variablenName, $value, $default, $save=false)
    {
        if ($save === true && $variable === null){
           $variable = Einstellungen::Get($variablenName, $default);
        }

        if ($save === true && $variable !== null)
            Einstellungen::Set($variablenName, $variable);

        if ($variable === null)
            $variable = $default;

        $empty = '_';
        $result = Design::erstelleVersteckteEingabezeile($console, $empty , $variablenName, $default, false);
        $result .= "<input style='' type='checkbox' id='{$variablenName}' name='{$variablenName}' value='".$value."'".(($variable==$value && $variable != null) ? "checked" : ($default === null ? '' : ($default===$value ? "checked" : '')) ).">";
        return $result;
    }

    /**
     * Erzeugt eine Passwortzeile
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param mixed $variable Der aktuelle Wert des Feldes (null = nicht zugewiesen)
     * @param string $variablenName Der Name des Feldes
     * @param mixed $default Der Standartwert (wenn $variable = null)
     * @param bool $save Wird nicht verwendet
     * @return string Der Text der Passwortzeile
     */
    public static function erstellePasswortzeile($console, $variable, $variablenName, $default, $save=false)
    {
        if ($save == true && $variable === null){
            $variable = Einstellungen::Get($variablenName, $default);
        }

        if ($save == true && $variable !== null)
            Einstellungen::Set($variablenName, $variable);

        if ($variable === null)
            $variable = $default;

        $result = '';

        if (!$console)
            $result = "<input style='width:100%' type='password' name='{$variablenName}' value='".(isset($variable) ? $variable : $default)."'>";

        return $result;
    }

    /**
     * Erzeugt eine Installationszeile
     * Prüft ob Fehler gemeldet wurden und gibt OK oder Fehler aus
     *
     * @param bool $console true = Konsolendarstellung, false = HTML
     * @param bool $fail true = Fehler, false = sonst
     * @param int $errno Die Fehlernummer
     * @param string $error Der Fehlertext
     * @return string Der Text der Installationszeile
     */
    public static function erstelleInstallationszeile($console, $fail, $errno, $error, $descText = null)
    {
        $descText = (isset($descText) ? $descText : Language::Get('main','installation'));

        if (!$console){
            if ($fail === true){
                //$installFail = true;
                return Design::erstelleZeile($console, $descText, 'e', '', 'v', "<div align ='center'><font color='red'>".Language::Get('main','fail'). (($errno!=null && $errno!='') ? " ({$errno})" : '') ."<br> {$error}</font></align>", 'v');
            } else{
                return Design::erstelleZeile($console, $descText, 'e', '', 'v', '<div align ="center">'.Language::Get('main','ok').'</align>', 'v');
            }
        } else {
            if ($fail === true){
                //$installFail = true;
                return Design::erstelleZeile($console, $descText, 'e', '', 'v', Language::Get('main','fail'). (($errno!=null && $errno!='') ? " ({$errno})" : '') ." {$error}", 'v');
            } else{
                return Design::erstelleZeile($console, $descText, 'e', '', 'v', Language::Get('main','ok'), 'v');
            }
        }
    }

    /**
     * Erzeugt einen Forumular Auslöser
     *
     * @param string $var Der Name des Auslösers
     * @param string $text Der Wert des Auslösers
     * @return string Der Text des Auslösers
     */
    public static function erstelleSubmitButton($var, $text = null)
    {
        if ($text === null)
            $text = Language::Get('main','install');
        return "<input type='submit' name='{$var}' value=' {$text} '>";
    }

    /**
     * Erzeugt einen Auslöser
     *
     * @param string $varName Der Name des Auslösers
     * @param string $value Der Wert des Auslösers
     * @param string $text Der sichtbare Text des Auslösers
     * @return string Der Text des Auslösers
     */
    public static function erstelleSubmitButtonFlach($varName, $value, $text = null)
    {
        if ($text === null)
            $text = Language::Get('main','install');
        return "<button class='text-button info-color bold' name='{$varName}' value='{$value}'>{$text}</button>";
    }

    public static function erstelleLink($varName, $value, $text = null)
    {
        if ($text === null)
            $text = Language::Get('main','install');
        return "<span class='text-button info-color bold'><a href='{$value}'>{$text}</a></span>";
    }

    /**
     * Erzeugt einen grafischen Auslöser
     *
     * @param string $var Der Name des Auslösers
     * @param string $bild Der Pfad des Bildes
     * @param string $width Die Breite
     * @param string $height Die Höhe
     * @return string Der Text des Auslösers
     */
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
        if ($size<=0) return '0B';
        $base = log($size) / log(1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), 2) . ' ' . $suffixes[floor($base)] . "B";
    }
}
