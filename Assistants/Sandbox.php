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
    private $profile = "";

    /**
     * @var string $workingDir the directory, where the class is working
     */
    private $workingDir = "";

    /**
     * @var int $timeout Timeout in Seconds, have to be under apache timeout to ensure script terminates
     */
    private $timeout = 240;

    /**
     * the $workingDir setter
     *
     * @param string $path the new value for working directory
     */
    public function setWorkingDir($path)
    {
        if(!preg_match("/\/$/", $path))
        {
            // path darf kein / hinten haben in Whitelist
            $this->addWhitelistDir($path);
            // speichern tun wir es aber mit /
            $path = $path . "/";
        }
        else
        {
            // entferen / am schluss für Whitelisteintrag
            $whitepath = preg_replace("/\/$/", "", $path);
            $this->addWhitelistDir($whitepath);
        }
        $this->workingDir = $path;

        $parentdir = dirname($path);
        if(!preg_match("/\/$/", $parentdir))
        {
            $parentdir = $parentdir . "/";
        }

        $this->addBlacklistDir($parentdir . "*");
        
    }

     /**
     * the $workingDir getter
     *
     * @return the value of $workingDir
     */
    public function getWorkingDir()
    {
        return $this->workingDir;
    }

    /**
     * loads a profile file
     *
     * @param string $filename the complete path to the profile file
     */
    public function loadProfileFromFile($filename)
    {
        $this->profile = file_get_contents($filename) . PHP_EOL . $this->profile . PHP_EOL;
    }

    /**
     * adds folder to whitelist
     * NOTE: for hiding whole parent and showing only specific children, add /path/to/parent/
     * and /path/to/whitelistpath/ in Whitelist, then add /path/to/parent/* in Blacklist to hide all others
     *
     * @param string $dir the complete path to the folder
     */
    public function addWhitelistDir($dir)
    {
        
        $this->profile =  $this->profile . PHP_EOL . "noblacklist " . $dir . PHP_EOL;
    }

    /**
     * adds folder to whitelist
     *
     * @param string $dir the complete path to the folder
     */
    public function addBlacklistDir($dir)
    {
        
        $this->profile =  $this->profile . PHP_EOL . "blacklist " . $dir . PHP_EOL;
    }

    /**
     * resets all profile files
     */
    public function resetProfile()
    {
        unset($this->profile);
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
        return preg_replace("/".PHP_EOL."/", "", $returnVal);
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
    public function sandbox_exec($command,$params,&$output = null)
    {
        $pathOld = getcwd();
        chdir($this->workingDir);

        //check if command exists

        if (Sandbox::command_exist($command) == false)
        {
            chdir($pathOld);
            return 1; //error status of "which" that means not available
        }

        // the path to the command (fixes some issues with javac in sandbox firejail)
        $realcmd = Sandbox::where_is_command($command);


        // $_SERVER['DOCUMENT_ROOT']

        $descriptorspec = array(
            0 => array("pipe","r"),
            1 => array("pipe","w"),
            2 => array("pipe","w")
        ) ;

        // define current working directory where files would be stored
        $newprofile = $this->workingDir."myprofil.profile";
        $profileerror = file_put_contents($newprofile, $this->profile);
        $treffer = array();

        
        if ($profileerror != false)
        {

            // open process /bin/sh
            $process = proc_open('firejail --quiet --net=none --force --profile='.$newprofile.' ', $descriptorspec, $pipes, $this->workingDir) ;

            if (is_resource($process)) {

                // anatomy of $pipes: 0 => stdin, 1 => stdout, 2 => error log
                // Set the stdout stream to none-blocking.
                stream_set_blocking($pipes[1], false);

                // Turn the timeout into microseconds.
                $mytimeout = $this->timeout * 1000000;

                // Output buffer.
                $output = '';


                fwrite($pipes[0], 'umask 000;'.$realcmd.' '.$params.';echo "STATUS=("$?")";');

                fclose($pipes[0]);

                // While we have time to wait.
                while ($mytimeout > 0) {
                    $start = microtime(true);

                    // Wait until we have output or the timer expired.
                    $read  = array($pipes[1]);
                    $other = array();
                    $noTimeout = stream_select($read, $other, $other, 0, $mytimeout);

                    // Get the status of the process.
                    // Do this before we read from the stream,
                    // this way we can't lose the last bit of output if the process dies between these     functions.
                    $status = proc_get_status($process);

                    // Read the contents from the buffer.
                    // This function will always return immediately as the stream is none-blocking.
                    $output .= stream_get_contents($pipes[1]);

                    if (!$status['running']) {
                      // Break from this loop if the process exited before the timeout.
                      break;
                    }

                    // Subtract the number of microseconds that we waited.
                    $mytimeout -= (microtime(true) - $start) * 1000000;
                }

                // print pipe output

                $error = stream_get_contents($pipes[2]);

                // Kill the process in case the timeout expired and it's still running.
                // If the process already exited this won't do anything.
                proc_terminate($process, 9);


                // close pipe
                fclose($pipes[1]);
                fclose($pipes[2]);
            
                // all pipes must be closed before calling proc_close. 
                // proc_close() to avoid deadlock
                proc_close($process) ;

                // pregmatches erst nachdem stream geschlossen
                preg_match("/STATUS=\([0-9]+\)/", $output, $treffer);
                $output = preg_replace("/STATUS=\([0-9]+\)/", "", $output);
            }
            chdir($pathOld);

            $status = -1;

            // check if Status existiert
            if(isset($treffer[0]) && $treffer[0] != "")
            {
                // lese Status
                preg_match("/[0-9]+/", $treffer[0], $statustreffer);
                if(isset($statustreffer[0]) && $statustreffer[0] != "")
                {
                    $status = (int) $statustreffer[0];

                    // wenn status ungleich 0 dann gebe error aus
                    if($status != 0)
                    {
                        $output = $error.PHP_EOL.$output;
                    }
                }
            }
            else
            {
                $status = 1;
                if ($noTimeout == false) {
                    $output = "Exit with Timeout.";
                }
            }
            

            return (int) $status;
        }
        else
        {
            chdir($pathOld);
            return 1;
        }
    }
}
?>