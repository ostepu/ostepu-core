<?php
/**
 * @file Logger.php
 * Contains the classes Logger and LogLevel.
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
 *
 * @author Florian Lücke
 */

/**
 * A Logger Class.
 *
 * @author Florian Lücke
 */
class Logger
{
    /*
     * @TODO: Add the possibility to bail on errors using "die", optionally printing
     * a call trace.
     */
    //static $shouldTrace = false;
    //static $shouldBailOnError = false;

    /**
     * @var $logFile The path of the log file. Messages will be sent here.
     */
    static $logFile = 'php://stderr';

    /**
     * Log a message to the log file.
     *
     * @param mixed $message The log message.
     * @param int $logLevel One of the constants defined in the class LogLevel.
     * @param string $logFile An alternative location for the log.
     * @return nothing
     */
    public static function Log($message,
                               $logLevel = LogLevel::INFO,
                               $logFile = NULL)
    {
        // if the function is called with the no prority don't log anything
        if ($logLevel == LogLevel::NONE) {
            return;
        }

        if (!isset($logFile)) {
            $logFile = self::$logFile;
        }

        // get the current date and time
        $infoString = "[Logger] " . date('M j G:i:s');

        // test if the message should be logged
        if ((error_reporting() & $logLevel) > 0) {

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
                    $infoString .= " " . $callerInfo['function'] . "()";
                }
            } else {
                // the function was invoked from outside a class

                $callerInfo = $info[0];

                // show the file in which the function was called
                if (isset($callerInfo['file'])) {
                    $infoString .=  " " . $callerInfo['file'];
                }
            }

            // show the line in which the function was called
            if (isset($callerInfo['line'])) {
                $infoString .= " (" . $callerInfo['line'] . ")";
            }

            // convert message to string if neccessary
            if (is_string($message) == false) {
                $message = implode("\n[Logger] ",explode("\n", print_r($message, true)));
            }

            $infoString .=  " [" . LogLevel::$names[$logLevel] . "]: " . $message . "\n";

            // open the log file for appending
            $fp = @fopen($logFile, "a");

            if (!$fp) {
                return;
            }

            // try to lock the file to prevent messages from overlapping
            // or overwriting each other
            if (flock($fp, LOCK_EX)) {
                // print the message to the file
                fwrite($fp, $infoString);

                // unlock and close the file
                flock($fp, LOCK_UN);

            } else {
                die("Getting lock on " . $logFile . " failed!\n");
            }

            fclose($fp);
        }
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
   // const OFF = 0;          /**< enum OFF: tells the Logger to turn off logging. */
    const NONE = 0; /**< enum NONE: same as above */
    const DEBUG = 11;        /**< enum DEBUG: log debug info */
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

?>