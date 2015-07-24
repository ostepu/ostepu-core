<?php

require_once( dirname(__FILE__) . '/phpfastcache/phpfastcache.php');

class cacheAccess
{    
    /**
     * @var phpFastCache $cache Enthält den Zugang zum CacheServer
     */
    private static $cache = null;
    
    
    /**
     * Speichert einen Datensatz
     *
     * @param string $key Der Schlüssel unter dem $value abgelegt werden soll
     * @param string $value Der Datensatz 
     * @return bool true = Erfolgreich, false = Fehler
     */
    public static function storeData($key, $value)
    {
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }
        ///Logger::Log('store: '.$key, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');

        return self::$cache->set($key,gzcompress($value) , 43200);
    }
        
    /**
     * Entfernt einen Datensatz
     *
     * @param string $key Der Schlüssel
     * @return bool true = Erfolgreich, false = Fehler
     */
    public static function removeData($key)
    {
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }        
        ///Logger::Log('delete: '.$key, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        return self::$cache->delete($key);
    }
        
    /**
     * Lädt einen Datensatz
     *
     * @param string $key der Schlüssel
     * @return string Der Datensatz oder null im Fehlerfall
     */
    public static function loadData($key)
    {
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }
        ///Logger::Log('load: '.$key, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        
        $res = self::$cache->get($key);
        if ($res === null) return null;        
        $res = gzuncompress($res);
        if ($res === false) return null;
        return $res;
    }
        
    /**
     * Lädt eine Reihe von Datensätzen
     *
     * @param string[] die Schlüssel
     * @return string[] Die Datensätze oder null im Fehlerfall, Bsp.: array('a',null,'b')
     */
    public static function loadDataArray($keys)
    {
        if (self::$cache===null){
            phpFastCache::setup(phpFastCache::$config);
            self::$cache = phpFastCache();
        }
        ///Logger::Log('load: '.implode(':',$keys), LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
        
        $res = self::$cache->getMulti($keys);
        foreach ($res as &$re){
            if ($re === null) continue;
            $re = gzuncompress($re);
            if ($re === false) $re = null;
        }
        
        return $res;
    }
}