<?php

/**
 * @file cacheAccess.php
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
if (file_exists(dirname(__FILE__) . '/../vendor/phpfastcache/phpfastcache.php')) {
    include_once(dirname(__FILE__) . '/../vendor/phpfastcache/phpfastcache.php');
}

include_once(dirname(__FILE__) . '/cacheLogger.php');

class cacheAccess{

    /**
     * @var phpFastCache $_cache Enthält den Zugang zum CacheServer
     */
    private static $_cache = null;
    
    /*
     * dieser Bezeichner wird in den Logeinträgen dieser Datei verwendet
     */
    private static $logName = 'cacheAccess';
    
    /**
     * Speichert einen Datensatz
     *
     * @param string $key Der Schlüssel unter dem $value abgelegt werden soll
     * @param string $value Der Datensatz
     * @return bool true = Erfolgreich, false = Fehler
     */
    public static function storeData($key, $value, $time = 43200) {
        if (self::$_cache === null) {
            phpFastCache::setup(phpFastCache::$config);
            self::$_cache = phpFastCache();
        }
        cacheLogger::Log('store: '.$key, self::$logName);

        return self::$_cache->set($key, gzcompress($value), $time);
    }

    /**
     * Entfernt einen Datensatz
     *
     * @param string $key Der Schlüssel
     * @return bool true = Erfolgreich, false = Fehler
     */
    public static function removeData($key) {
        if (self::$_cache === null) {
            phpFastCache::setup(phpFastCache::$config);
            self::$_cache = phpFastCache();
        }
        cacheLogger::Log('delete: '.$key, self::$logName);
        return self::$_cache->delete($key);
    }

    /**
     * Lädt einen Datensatz
     *
     * @param string $key der Schlüssel
     * @return string Der Datensatz oder null im Fehlerfall
     */
    public static function loadData($key) {
        if (self::$_cache === null) {
            phpFastCache::setup(phpFastCache::$config);
            self::$_cache = phpFastCache();
        }
        cacheLogger::Log('load: '.$key, self::$logName);

        $res = self::$_cache->get($key);
        if ($res === null) {
            return null;
        }
        $res = gzuncompress($res);
        if ($res === false) {
            return null;
        }
        return $res;
    }

    /**
     * Lädt eine Reihe von Datensätzen
     *
     * @param string[] die Schlüssel
     * @return string[] Die Datensätze oder null im Fehlerfall,
     *                                           Bsp.: array('a',null,'b')
     */
    public static function loadDataArray($keys) {
        if (self::$_cache === null) {
            phpFastCache::setup(phpFastCache::$config);
            self::$_cache = phpFastCache();
        }
        cacheLogger::Log('load: '.implode(':',$keys), self::$logName);

        $res = self::$_cache->getMulti($keys);
        foreach ($res as &$re) {
            if ($re === null) {
                continue;
            }
            $re = gzuncompress($re);
            if ($re === false) {
                $re = null;
            }
        }

        return $res;
    }

    /*
     * gibt zurück ob eine Datei im Cache existiert und verlängert dabei
     * deren Lebensdauer, sodass sie in nächster Zeit nicht automatisch
     * gelöscht wird
     */
    public static function touch($key) {
        if (self::$_cache === null) {
            phpFastCache::setup(phpFastCache::$config);
            self::$_cache = phpFastCache();
        }

        return self::$_cache->touch($key);
    }

}
