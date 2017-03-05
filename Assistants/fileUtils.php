<?php

if (file_exists(dirname(__FILE__) . '/../UI/include/Config.php')) include_once dirname(__FILE__) . '/../UI/include/Config.php';

class fileUtils
{
    public static function generateDownloadAddress($fileObject, $dur = 1800){
        global $externalURI; // kommt aus UI/include/Config.php
        global $downloadSiteKey;
        
        if (!isset($fileObject['address']) || !isset($fileObject['displayName'])){
            return '';
        }
        
        // gibt die Gültigkeitsdauer in Sekunden an
        $duration = time()+$dur; // 1800s = 30 Minuten
        
        // jetzt wird die Signatur erzeugt, bestehend aus
        // Ablaufzeitpunkt_URL
        $auth = new Authentication(false);
        
        if (trim($downloadSiteKey) == ''){
            $downloadSiteKey = null;
        }
        
        $auth->siteKey = $downloadSiteKey;
        
        // die FSBinder nutzt diese Methode beim verifizieren der eingehenden Dateianfrage
        $signature = $duration.'_'.$auth->hashData("sha256", $duration.'_'.$fileObject['address'].'/'.$fileObject['displayName']);
        return $signature.'/'.$fileObject['address'];
    }
    
    public static function prepareFileObject($fileObject, $dur = 1800){
        if (!isset($fileObject['address']) || !isset($fileObject['displayName'])){
            // wenn das Objekt ungültig ist, dann verändere es nicht
            return $fileObject;
        }
        $fileObject['address'] = self::generateDownloadAddress($fileObject, $dur);
        return $fileObject;
    }
    
    public static function generateDownloadURL($fileObject, $dur = 1800){
        global $externalURI; // kommt aus UI/include/Config.php
        return $externalURI.'/FS/FSBinder/'.self::generateDownloadAddress($fileObject, $dur).'/'.$fileObject['displayName'];
    }
    
    /**
     * Creates a file path by splitting the hash.
     *
     * @param string $type The prefix of the file path.
     * @param string $hash The hash of the file.
     */
    public static function generateFilePath(
                                            $type,
                                            $hash
                                            )
    {
        if ( strlen( $hash ) >= 4 ){
            return $type . '/' . $hash[0] . '/' . $hash[1] . '/' . $hash[2] . '/' . substr(
                                                                                           $hash,
                                                                                           3
                                                                                           );
           
        } else
            return '';
    }

    /**
     * Creates the path in the filesystem, if necessary.
     *
     * @param string $path The path which should be created.
     * @see http://php.net/manual/de/function.mkdir.php#83265
     */
    public static function generatepath( $path, $mode = 0755 )
    {
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        $e = explode("/", ltrim($path, "/"));
        if(substr($path, 0, 1) == "/") {
            $e[0] = "/".$e[0];
        }
        $c = count($e);
        $cp = $e[0];
        for($i = 1; $i < $c; $i++) {
            if(!is_dir($cp) && !@mkdir($cp, $mode)) {
                return false;
            }
            $cp .= "/".$e[$i];
        }
        return @mkdir($path, $mode);
    }
}