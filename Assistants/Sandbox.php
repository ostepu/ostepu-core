<?php
/**
 * @file Sandbox.php Contains the Sandbox class
 * 
 * @author Ralf Busch
 * @date 2015
 */

class Sandbox
{
    /**
     * @var string $profiles the profiles, the class loads
     */
    private static $profile = "";

    /**
     * @var string $workingDir the directory, where the class is working
     */
    private static $workingDir = "";

    /**
     * the $workingDir setter
     *
     * @param string $path the new value for working directory
     */
    public static function setWorkingDir($path)
    {
        if(!preg_match("/\/$/", $path))
        {
            $path = $path . "/";
        }
        Sandbox::$workingDir = $path;

        $parentdir = dirname($path);
        if(!preg_match("/\/$/", $parentdir))
        {
            $parentdir = $parentdir . "/";
        }

        Sandbox::addBlacklistDir($parentdir . "*");
        Sandbox::addWhitelistDir($parentdir);
        Sandbox::addWhitelistDir($path);
    }

     /**
     * the $workingDir getter
     *
     * @return the value of $workingDir
     */
    public static function getWorkingDir()
    {
        return Sandbox::$workingDir;
    }

    /**
     * loads a profile file
     *
     * @param string $filename the complete path to the profile file
     */
    public static function loadProfileFromFile($filename)
    {
        Sandbox::$profile = Sandbox::$profile . PHP_EOL . file_get_contents($filename);
    }

    /**
     * adds folder to whitelist
     * NOTE: first add parent folder with /path/to/parent/* to the blacklist
     * and /path/to/parent/ have to be in whitelist before adding
     * children to whitelist
     *
     * @param string $dir the complete path to the folder
     */
    public static function addWhitelistDir($dir)
    {
        
        Sandbox::$profile =  "noblacklist " . $dir . PHP_EOL . Sandbox::$profile;
    }

    /**
     * adds folder to whitelist
     * NOTE: first add parent folder with /path/to/parent/* to the blacklist
     * and /path/to/parent/ have to be in whitelist before adding
     * children to whitelist
     *
     * @param string $dir the complete path to the folder
     */
    public static function addBlacklistDir($dir)
    {
        
        Sandbox::$profile =  "blacklist " . $dir . PHP_EOL . Sandbox::$profile;
    }

    /**
     * resets all profile files
     */
    public static function resetProfile()
    {
        unset(Sandbox::$profile);
    }


    /**
     * check if command is available on system
     *
     *@param string $cmd the command 
     *
     * @return bool if command is available
     */
    private static function command_exist($cmd)
    {
        $returnVal = shell_exec("which $cmd");
        return (empty($returnVal) ? false : true);
    }


    /**
     * check where command is installed on system
     *
     *@param string $cmd the command 
     *
     * @return string path of the command
     */
    private static function where_is_command($cmd)
    {
        $returnVal = shell_exec("readlink -f $(which ".$cmd.")");
        return $returnVal;
    }

    /**
     * executes terminal commands in sandbox
     * TODO: use other sandbox methods based on operating system
     *
     * @param string $command the executed command
     * @param array $params the parameters for the command
     * @param string $output the output printed by the command
     *
     * @return the status of executed command
     */
    public static function sandbox_exec($command,$params,&$output = null)
    {
        //check if command exists
        if (Sandbox::command_exist($cmd) == false)
        {
            return 1; //error status of "which" that means not available
        }

        // the path to the command (fixes some issues with javac in sandbox firejail)
        $realcmd = Sandbox::where_is_command($command);

        $pathOld = getcwd();
        chdir(Sandbox::$workingDir);                             
        exec('(firejail '.$command.' '.$params.') 2>&1', $output, $return);
        chdir($pathOld);

        // $_SERVER['DOCUMENT_ROOT']

        $descriptorspec = array(
            0 => array("pipe","r"),
            1 => array("pipe","w"),
            2 => array("pipe","w")
        ) ;

        // define current working directory where files would be stored
        $pathOld = getcwd();
        chdir(Sandbox::$workingDir);
        // open process /bin/sh
        $process = proc_open('firejail --quiet --profile='.$_SERVER['DOCUMENT_ROOT'].'/test.profile ', $descriptorspec, $pipes, Sandbox::$workingDir) ;

        if (is_resource($process)) {

          // anatomy of $pipes: 0 => stdin, 1 => stdout, 2 => error log
          fwrite($pipes[0], $realcmd.' '.$params) ;

          fclose($pipes[0]) ;

          // print pipe output
          $output = stream_get_contents($pipes[1]);
          $error = stream_get_contents($pipes[2]);

          // close pipe
          fclose($pipes[1]) ;
          fclose($pipes[2]) ;
         
          // all pipes must be closed before calling proc_close. 
          // proc_close() to avoid deadlock
          proc_close($process) ;
        }
        chdir($pathOld);

        return $return;
    }
}
?>