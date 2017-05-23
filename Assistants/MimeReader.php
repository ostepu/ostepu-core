<?php
/**
 * @file MimeReader.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
 
if (file_exists(dirname(__FILE__).'/vendor/mimey/src/MimeTypesInterface.php')){
    include_once(dirname(__FILE__).'/vendor/mimey/src/MimeTypesInterface.php');
    include_once(dirname(__FILE__).'/vendor/mimey/mime.types.php');
    include_once(dirname(__FILE__).'/vendor/mimey/src/MimeTypes.php');
}


class MimeReader {

    /**
     * Ermittelt den mimeType einer Datei
     * (wurde erweitert, sodass versucht wird ein text/plain nochmals anhand der 
     * Dateiendung zu übersetzen)
     * http://stackoverflow.com/a/134930/1593459
     *
     * @param string $file Der Pfad der Datei
     * @return string Der Typ
     */
    public static function get_mime($file, $preferExtension=false) {
        if (!file_exists($file)) return null;
        $mime = null;
    
        if ($preferExtension){
            if (class_exists('\Mimey\MimeTypes')){
                $mimes = new \Mimey\MimeTypes;
                $path_parts = pathinfo($file);

                // Convert extension to MIME type:
                $mime = $mimes->getMimeType($path_parts['extension']);
                
                if ($mime !== null){
                    return $mime;
                }
            }
        }
        
        if (function_exists("finfo_file")) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if (!$finfo) return null;
            
            $mime = @finfo_file($finfo, $file);
            if (!$mime) return null;
            
            finfo_close($finfo);
        } else if (function_exists("mime_content_type")) {
            $mime = mime_content_type($file);
        } else if (!stristr(ini_get("disable_functions"), "shell_exec")) {
            $file = escapeshellarg($file);
            $mime = shell_exec("file -bi " . $file);
        } else {
            // ich kann hier keine passende Funktion finden
        }
        
        if ($mime === null || (!$preferExtension && $mime === 'text/plain')){
            if (class_exists('\Mimey\MimeTypes')){
                $mimes = new \Mimey\MimeTypes;
                $path_parts = pathinfo($file);

                // Convert extension to MIME type:
                $newMime = $mimes->getMimeType($path_parts['extension']);
                
                if ($newMime !== null){
                    return $newMime;
                }
            }
        }
        
        return $mime;
    }
}
    