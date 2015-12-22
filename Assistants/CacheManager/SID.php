<?php

class SID
{
    /**
     * @var int $currentBaseSID Die Anfangsadresse
     */
    private static $currentBaseSID = 0;
    
    /**
     * @var int $maxSid Die letzte vergebene SID
     */
    private static $maxSid = -1;
    
    /**
     * Liefert die nächste eindeutige ID
     *
     * @return int Die neue SID
     */
    public static function getNextSid()
    {
        $header = array_merge(array(),Request::http_parse_headers_short(headers_list()));
        
        $id=null;
        if (isset($header['Cachesid'])){
            $id = intval($header['Cachesid']);
           
            if (self::$currentBaseSID !== $id)
                return $id;
        }
            
        if ($id === self::$currentBaseSID) self::$currentBaseSID = self::$maxSid+1;
        self::$maxSid=self::$maxSid+1;
        return self::$maxSid;
    }
    
    /**
     * Setzt alle Daten auf den Standartwert zurück.
     */
    public static function reset()
    {
        self::$maxSid = -1;
    }
}