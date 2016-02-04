<?php

class SID
{
    /**
     * @var int $_currentBaseSID Die Anfangsadresse
     */
    private static $_currentBaseSID = 0;

    /**
     * @var int $_maxSid Die letzte vergebene SID
     */
    private static $_maxSid = -1;

    public static function isRoot()
    {
        $id = self::getSid();
        if ($id === null || $id !== self::$_currentBaseSID) {
            return false;
        }
        return true;
    }

    public static function getRoot()
    {
        return self::$_currentBaseSID;
    }

    /**
     * Liefert die nächste eindeutige ID
     *
     * @return int Die neue SID
     */
    public static function getNextSid()
    {
        $header = array_merge(
            array(),
            Request::http_parse_headers_short(headers_list())
        );

        $id=null;
        if (isset($header['Cachesid'])) {
            $id = intval($header['Cachesid']);

            if (self::$_currentBaseSID !== $id) {
                return $id;
            }
        }

        if ($id === self::$_currentBaseSID) {
            self::$_currentBaseSID = self::$_maxSid+1;
        }
        self::$_maxSid=self::$_maxSid+1;
        return self::$_maxSid;
    }

    public static function getSid()
    {
        $header = array_merge(
            array(),
            Request::http_parse_headers_short(headers_list())
        );

        if (isset($header['Cachesid'])) {
            $id = intval($header['Cachesid']);
            return $id;
        }

        return null;
    }

    /**
     * Setzt alle Daten auf den Standartwert zurück.
     */
    public static function reset()
    {
        self::$_maxSid = -1;
    }
}
