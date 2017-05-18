<?php
/**
 * @file Logger.php
 * Contains the classes Logger and LogLevel.
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Florian Lücke <florian.luecke@gmail.com>
 * @date 2013-2014
 *
 * This file contains two classes. This is an exception but it seemed neccessary
 * to keep them as close toghether as possible as LogLevel is only used to
 * enumerate values for Logger
 *
 * Example usage:
 * @code{.php}
 * //log to file test.log in home directory
 * Logger::Log("test", LogLevel::INFO, "~/test.log");
 *
 * // log to file test2.log in /var/log
 * Logger::Log("test2", LogLevel::INFO, "/var/log/test2.log");
 *
 * // set the location for all logs to /var/log/test2.log
 * Logger::$logFile = "/var/log/test2.log";
 * // log to file test2.log in /var/log
 * Logger::Log("test3");
 * @endcode
 */

/**
 * A Logger Class.
 *
 * @author Florian Lücke
 */
class Logger
{
    /**
     * @var $logFile The path of the log file. Messages will be sent here.
     */
    static $logFile = 'php://stderr';
    
    static $defaultLogLevel = LogLevel::ERROR;

    /**
     * Log a message to the log file.
     *
     * @param mixed $message The log message.
     * @param int $logLevel One of the constants defined in the class LogLevel.
     * @param boolean $trace Enables or disables the trace mechanism (true = enabled, false = disabled).
     * @param string $logFile An alternative location for the log.
     * @param string $name Sets a custom identifier for this log entry.
     * @param boolean $timestamp Adds the timestamp to the log message (true = enabled, false = disabled).
     * @param string $currentLogLevel Sets a custom log level for this call.
     * @return nothing
     */
    public static function Log($message,
                               $logLevel = LogLevel::INFO,
                               $trace = NULL,
                               $logFile = NULL,
                               $name = NULL,
                               $timestamp = NULL,
                               $currentLogLevel = null)
    {
        if ($name === null){
            $name = 'Logger';
        }
        
        if ($trace === null){
            $trace = true;
        }
        
        if ($timestamp === null){
            $timestamp = true;
        }
        
        if (!isset($currentLogLevel)){
            $currentLogLevel = self::$defaultLogLevel; //error_reporting();
        }
        
        // if the function is called with the no prority don't log anything
        if ($logLevel == LogLevel::NONE) {
            return false;
        }

        if (!isset($logFile)) {
            $logFile = self::$logFile;
        }

        // get the current date and time
        $infoString = '[' . $name . ']' .($timestamp ? ' '.date('M j G:i:s'): '');

        // test if the message should be logged
        if (($currentLogLevel & $logLevel) === $logLevel) {

            if ($trace){
                $info = debug_backtrace();
                if (isset($info[1])) {
                    // the function was invoked from a class

                    $callerInfo = $info[1];

                    // show calling class
                    if (isset($callerInfo['class'])) {
                        $infoString .= " " . $callerInfo['class'];
                    }

                    // show class/instance Method
                    if (isset($callerInfo['type'])) {
                        $infoString .= " " . $callerInfo['type'];
                    }

                    // shwo calling function
                    if (isset($callerInfo['function'])) {
                        $infoString .= ' ' . $callerInfo['function'] . "()";
                    }
                } else {
                    // the function was invoked from outside a class

                    $callerInfo = $info[0];

                    // show the file in which the function was called
                    if (isset($callerInfo['file'])) {
                        $infoString .=  ' ' . $callerInfo['file'];
                    }
                }

                // show the line in which the function was called
                if (isset($callerInfo['line'])) {
                    $infoString .= ' (' . $callerInfo['line'] . ')';
                }
            }

            // convert message to string if neccessary
            if (is_array($message)) {
                $message = implode("\n[".$name.'] ',explode("\n", print_r($message, true)));
            }

            if ($trace){
                $infoString .=  ' [' . LogLevel::$names[$logLevel] . ']: ' . $message . "\n";
            } else
                $infoString .=  ' ' . $message . "\n";
            
            // open the log file for appending
            $fp = @fopen($logFile, 'a');

            if (!$fp) {
                return false;
            }

            // try to lock the file to prevent messages from overlapping
            // or overwriting each other
            //do{} while(!flock($fp, LOCK_EX);
            
           /// if (flock($fp, LOCK_EX)) {
                // print the message to the file
                fwrite($fp, $infoString);

                // unlock and close the file
             ///   flock($fp, LOCK_UN);

          ///  } else {
                //die("Getting lock on " . $logFile . " failed!\n");
           /// }

            fclose($fp);
            return true;
        }
        
        return false;
    }
}

/**
 * Different log levels.
 *
 * This class is a workaround for php's missing enum. Loo-levels are constants of
 * this class, this has the same effect as enum-ing them in C. This is achieved
 * by declaring the class abstract
 *
 * @author Florian Lücke
 */
abstract class LogLevel
{
   // const OFF = 0;        /**< enum OFF: tells the Logger to turn off logging. */
    const NONE = 0;         /**< enum NONE: same as above */
    const DEBUG = 11;       /**< enum DEBUG: log debug info */
    const INFO = 8;         /**< enum INFO: log general info */
    const WARNING = 2;      /**< enum WARNING: log warnings */
    const ERROR = 1;        /**< enum ERROR: log errors */

    /**
     * An array that allows the conversion from a constant to a name.
     */
    static $names = array(self::INFO => 'INFO',
                          self::WARNING => 'WARNING',
                          self::ERROR => 'ERROR',
                          self::DEBUG => 'DEBUG');
}
