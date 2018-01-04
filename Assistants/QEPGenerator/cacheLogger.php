<?php

include_once ( dirname(__FILE__) . '/../Logger.php' );

class cacheLogger {

    protected static $logState = false;

    public static function enableLog(){
        self::$logState = true;
    }
    
    public static function disableLog(){
        self::$logState = false;
    }

    public static function Log($text, $name='undefined', $logLevel = LogLevel::DEBUG){
        $level = isset($GLOBALS['logLevel']) ? $GLOBALS['logLevel'] : 0;
        $levelReal = $level | $logLevel;
        
        if (self::$logState){
            Logger::Log($text, $levelReal, false,dirname(__FILE__) . '/../calls.log', $name, true, LogLevel::DEBUG);
        }
    }

    public static function LogError($text, $name='undefined'){
        self::Log($text, $name, LogLevel::ERROR);
    }
}